<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.access');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $categories = Category::where('company_id', $user->company_id)->paginate(10);
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:categories,code',
            'description' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
        Category::create($validated);
        return redirect()->route('categories.index')->with('success', 'Categoria cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $category = Category::where('company_id', $user->company_id)->findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $category = Category::where('company_id', $user->company_id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:categories,code,' . $category->id,
            'description' => 'nullable|string',
        ]);
        $category->update($validated);
        return redirect()->route('categories.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $category = Category::where('company_id', $user->company_id)->findOrFail($id);

        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Não é possível excluir a categoria porque existem produtos vinculados a ela.');
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Categoria removida com sucesso!');
    }
}
