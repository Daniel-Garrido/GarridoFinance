<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Perfil del usuario. Los datos personales y la contraseña se guardan con las
 * rutas de Fortify (user-profile-information.update y user-password.update);
 * aquí se resuelven la vista, la foto de perfil y la eliminación de la cuenta.
 */
class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Resumen de la cuenta
        $transactionsCount = Transaction::where('user_id', $user->id)->count();
        $activeAccounts = Account::where('user_id', $user->id)->where('is_active', true)->count();
        $memberSince = ucfirst($user->created_at->locale('es')->translatedFormat('F Y'));

        // Último acceso: la actividad más reciente en otra sesión (otro navegador o dispositivo);
        // si no hay otra, la sesión actual es el único acceso
        $lastActivity = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->max('last_activity');

        $lastAccess = $lastActivity
            ? $this->formatLastAccess(Carbon::createFromTimestamp($lastActivity, config('app.timezone')))
            : 'Esta sesión';

        return view('profile.index', compact('user', 'transactionsCount', 'activeAccounts', 'memberSince', 'lastAccess'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validateWithBag('updatePhoto', [
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'photo.required' => 'Selecciona una imagen.',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La imagen debe ser JPG, PNG o WEBP.',
            'photo.max' => 'La imagen no puede pesar más de 2 MB.',
        ]);

        $user = $request->user();
        $previous = $user->profile_photo_path;

        $user->forceFill([
            'profile_photo_path' => $request->file('photo')->store('profile-photos', 'public'),
        ])->save();

        // Se borra la foto anterior solo después de guardar la nueva
        if ($previous) {
            Storage::disk('public')->delete($previous);
        }

        return back()->with('success', 'Tu foto de perfil se actualizó correctamente.');
    }

    public function destroyPhoto(Request $request)
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save();
        }

        return back()->with('success', 'Se quitó tu foto de perfil.');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('deleteAccount', [
            'password' => ['required', 'current_password:web'],
        ], [
            'password.required' => 'Escribe tu contraseña para confirmar.',
            'password.current_password' => 'La contraseña no es correcta.',
        ]);

        $user = $request->user();
        $photo = $user->profile_photo_path;

        Auth::guard('web')->logout();

        // Las llaves foráneas borran en cascada cuentas, categorías, métodos de pago,
        // transacciones y transferencias del usuario
        $user->delete();

        if ($photo) {
            Storage::disk('public')->delete($photo);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Tu cuenta se eliminó correctamente.');
    }

    // "Hoy, 08:42", "Ayer, 21:10" o "12 sep 2026, 18:30"
    private function formatLastAccess(Carbon $date): string
    {
        $time = $date->format('H:i');

        if ($date->isToday()) {
            return 'Hoy, ' . $time;
        }

        if ($date->isYesterday()) {
            return 'Ayer, ' . $time;
        }

        return str_replace('.', '', $date->locale('es')->translatedFormat('j M Y')) . ', ' . $time;
    }
}
