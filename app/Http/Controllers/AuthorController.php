<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    // INDEX
    public function index()
    {
        $authors = Author::paginate(10);
        return view('authors.index', compact('authors'));
    }

    // SHOW
    public function show($id)
    {
        $author = Author::findOrFail($id);
        return view('authors.show', compact('author'));
    }

    // CREATE (form)
    public function create()
    {
        return view('authors.create');
    }

    // STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required',
            'email'   => 'required|email|unique:authors,email',
            'website' => 'nullable',
            'phone'   => 'nullable',
        ]);

        Author::create($validated);

        return redirect()->route('authors.index')
            ->with('success', 'Author created successfully!');
    }

    // EDIT
    public function edit($id)
    {
        $author = Author::findOrFail($id);
        return view('authors.edit', compact('author'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $author = Author::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'required',
            'email'   => 'required|email|unique:authors,email,' . $author->id,
            'website' => 'nullable',
            'phone'   => 'nullable',
        ]);

        $author->update($validated);

        return redirect()->route('authors.index')
            ->with('success', 'Author updated successfully!');
    }

    // DELETE
    public function destroy($id)
    {
        Author::findOrFail($id)->delete();

        return redirect()->route('authors.index')
            ->with('success', 'Author deleted successfully!');
    }
}
