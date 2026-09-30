<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $query = substr(trim($request->get('q', '')), 0, 100);

        $books = Book::with('category')
            ->when($query !== '', function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($catQ) use ($query) {
                        $catQ->where('name', 'like', "%{$query}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.books.index', compact('books', 'query'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.create', compact('categories'));
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        Book::create($data);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && ! str_starts_with($book->cover_image, 'http')) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book->update($data);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $hasActiveLoans = $book->loans()
            ->whereIn('status', ['pending', 'dipinjam', 'return_requested'])
            ->exists();

        if ($hasActiveLoans) {
            return redirect()
                ->route('admin.books.index')
                ->with('error', 'Buku tidak dapat dihapus karena sedang dipinjam atau memiliki transaksi berjalan.');
        }

        if ($book->cover_image && ! str_starts_with($book->cover_image, 'http')) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
