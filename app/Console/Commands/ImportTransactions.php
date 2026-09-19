<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportTransactions extends Command
{
    /**
     * php artisan import:transactions REPORTS_FINANCE_APP.xlsx
     * php artisan import:transactions REPORTS_FINANCE_APP.xlsx --user_id=1 --dry-run
     */
    protected $signature = 'import:transactions
        {file : Ruta al archivo .xlsx}
        {--user_id= : ID del usuario dueño de los datos (por defecto el primero que exista)}
        {--dry-run : Corre todo sin guardar nada en la base de datos}';

    protected $description = 'Importa masivamente transacciones y transferencias históricas desde un Excel';

    /**
     * Mapeo cuenta -> método de pago inferido.
     * La llave se compara ya "trim + strtolower" contra el nombre base de la cuenta.
     */
    private array $paymentMethodMap = [
        'billetera' => 'Efectivo',
        'ahorro dinero físico' => 'Efectivo',
        'td bbva' => 'Tarjeta de débito BBVA',
        'td mercado pago' => 'Tarjeta de débito Mercado Pago',
        'dinero prestado' => 'Préstamo',
    ];

    private array $accountCache = [];
    private array $categoryCache = [];
    private array $paymentMethodCache = [];

    public function handle(): int
    {
        $path = $this->argument('file');

        if (!file_exists($path)) {
            $this->error("No encontré el archivo: {$path}");
            return self::FAILURE;
        }

        $userId = $this->option('user_id') ?? User::query()->value('id');

        if (!$userId) {
            $this->error('No hay ningún usuario en la base de datos. Crea uno primero o pasa --user_id.');
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        $this->info("Leyendo {$path} ...");
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        // Primera fila = encabezados: Fecha | Tipo | Categoría | Detalles | Cuenta | Monto
        array_shift($rows);

        $stats = ['transacciones' => 0, 'transferencias' => 0, 'omitidas' => 0];

        DB::beginTransaction();

        try {
            foreach ($rows as $i => $row) {
                [$fecha, $tipo, $categoria, $detalles, $cuenta, $monto] = array_pad($row, 6, null);

                if (blank($fecha) || blank($tipo) || blank($cuenta) || blank($monto)) {
                    $stats['omitidas']++;
                    continue;
                }

                $date = $this->parseDate($fecha);
                $amount = $this->parseAmount($monto);
                $tipo = trim($tipo);
                $categoria = $categoria !== null ? trim($categoria) : null;
                $detalles = $detalles !== null ? trim($detalles) : null;

                if ($tipo === 'Transferencia') {
                    [$fromName, $toName] = array_map('trim', explode('>', $cuenta, 2));

                    $from = $this->resolveAccount($userId, $fromName);
                    $to = $this->resolveAccount($userId, $toName);

                    Transfer::create([
                        'user_id' => $userId,
                        'date' => $date,
                        'amount' => $amount,
                        'from_account_id' => $from->id,
                        'to_account_id' => $to->id,
                        'description' => $detalles ?: $categoria,
                    ]);

                    $stats['transferencias']++;
                    continue;
                }

                $type = $tipo === 'Ingreso' ? 'income' : 'expense';

                $account = $this->resolveAccount($userId, trim($cuenta));
                $category = $this->resolveCategory($userId, $categoria ?: 'Otros', $type);
                $paymentMethod = $this->resolvePaymentMethod($userId, trim($cuenta));

                Transaction::create([
                    'user_id' => $userId,
                    'date' => $date,
                    'type' => $type,
                    'amount' => $amount,
                    'account_id' => $account->id,
                    'category_id' => $category->id,
                    'payment_method_id' => $paymentMethod->id,
                    'description' => $detalles,
                    'is_historical' => true,
                ]);

                $stats['transacciones']++;
            }

            if ($dryRun) {
                DB::rollBack();
                $this->warn('DRY RUN: no se guardó nada.');
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Falló la importación, se revirtió todo: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info("Transacciones importadas: {$stats['transacciones']}");
        $this->info("Transferencias importadas: {$stats['transferencias']}");
        $this->info("Filas omitidas (incompletas): {$stats['omitidas']}");

        return self::SUCCESS;
    }

    private function resolveAccount(int $userId, string $name): Account
    {
        $key = $userId . '|' . mb_strtolower($name);

        if (!isset($this->accountCache[$key])) {
            $this->accountCache[$key] = Account::firstOrCreate(
                ['user_id' => $userId, 'name' => $name],
                ['type' => 'bank', 'is_active' => true]
            );
        }

        return $this->accountCache[$key];
    }

    private function resolveCategory(int $userId, string $name, string $type): Category
    {
        $key = $userId . '|' . $type . '|' . mb_strtolower($name);

        if (!isset($this->categoryCache[$key])) {
            $this->categoryCache[$key] = Category::firstOrCreate(
                ['user_id' => $userId, 'name' => $name, 'type' => $type],
                ['is_active' => true]
            );
        }

        return $this->categoryCache[$key];
    }

    private function resolvePaymentMethod(int $userId, string $accountName): PaymentMethod
    {
        $normalized = mb_strtolower(trim($accountName));
        $methodName = $this->paymentMethodMap[$normalized] ?? 'Efectivo';

        $key = $userId . '|' . mb_strtolower($methodName);

        if (!isset($this->paymentMethodCache[$key])) {
            $this->paymentMethodCache[$key] = PaymentMethod::firstOrCreate(
                ['user_id' => $userId, 'name' => $methodName],
                ['is_active' => true]
            );
        }

        return $this->paymentMethodCache[$key];
    }

    private function parseDate(string $raw): string
    {
        // El Excel viene en formato d/m/Y
        return Carbon::createFromFormat('d/m/Y', trim($raw))->format('Y-m-d');
    }

    private function parseAmount(mixed $raw): float
    {
        if (is_numeric($raw)) {
            return (float) $raw;
        }

        // "2,500.00" -> 2500.00
        return (float) str_replace(',', '', trim((string) $raw));
    }
}
