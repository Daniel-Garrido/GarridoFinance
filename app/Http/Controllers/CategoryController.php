<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    // Categorías por página en el listado
    private const PER_PAGE = 10;

    public function index(Request $request)
    {
        // Filtro de tipo: solo se aceptan income / expense; cualquier otro valor muestra todas
        $type = array_key_exists($request->get('type'), Category::TYPES) ? $request->get('type') : null;

        // Categorías del usuario autenticado, filtradas por tipo y paginadas
        $categories = Category::where('user_id', Auth::id())
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->orderBy('id', 'desc')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // Si la página pedida ya no existe (por ejemplo, tras eliminar), ir a la última
        if ($categories->isEmpty() && $categories->currentPage() > 1) {
            return redirect($categories->url($categories->lastPage()));
        }

        return view('categories.index', compact('categories', 'type'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        Category::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada correctamente');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate($this->rules());

        $category->update($validated);

        // back() conserva la página y el filtro en los que estaba el usuario
        return redirect()->back()
            ->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back()
            ->with('success', 'Categoría eliminada');
    }

    // Reglas compartidas por crear y editar
    private function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'type' => ['required', Rule::in(array_keys(Category::TYPES))],
            'is_active' => 'required|boolean',
        ];
    }
}
