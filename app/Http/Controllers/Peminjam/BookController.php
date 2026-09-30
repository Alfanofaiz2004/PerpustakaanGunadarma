<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::with('category')
            ->orderBy('title')
            ->paginate(18);

        return view('peminjam.books.index', compact('books'));
    }

    public function search(Request $request): JsonResponse
    {
        $query = substr(trim($request->get('q', '')), 0, 100);

        $books = Book::with('category')
            ->when($query !== '', function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($catQuery) use ($query) {
                        $catQuery->where('name', 'like', "%{$query}%");
                    });
            })
            ->latest()
            ->limit(36)
            ->get()
            ->each(function ($book) {
                $book->cover_url = $book->cover_url;
            });

        return response()->json([
            'status' => 'success',
            'data' => $books,
        ]);
    }
}
