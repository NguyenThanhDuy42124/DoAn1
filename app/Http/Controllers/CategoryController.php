<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Category as ModelsCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ModelsCategory::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
        ]);
        ModelsCategory::create($request->all());
        return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully.');
    }

    public function show(string $id)
    {
        $category = ModelsCategory::findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(string $id)
    {
        $category = ModelsCategory::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
        ]);
        $category = ModelsCategory::findOrFail($id);
        $category->update($request->all());
        return redirect()->route('admin.categories.index')
              ->with('success', 'Category updated successfully.');
    }

    public function destroy(string $id)
    {
        $category = ModelsCategory::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.categories.index')
              ->with('success', 'Category deleted successfully.');
    }
}