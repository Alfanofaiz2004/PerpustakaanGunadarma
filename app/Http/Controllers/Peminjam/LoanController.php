<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(): View
    {
        $loans = Loan::with('book.category')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('peminjam.loans.index', compact('loans'));
    }

    /**
     * Peminjam requests to borrow a book → status = pending (waits for admin approval).
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'due_date' => ['required', 'date', 'after:today', 'before_or_equal:' . Carbon::now()->addDays(14)->toDateString()],
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->stock <= 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Stok buku ini sedang kosong.'], 422);
            }
            return back()->with('error', 'Stok buku ini sedang kosong.');
        }

        $existingLoan = Loan::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'dipinjam', 'return_requested'])
            ->exists();

        if ($existingLoan) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah memiliki peminjaman aktif atau permintaan tertunda untuk buku ini.'], 422);
            }
            return back()->with('error', 'Anda sudah memiliki peminjaman aktif atau permintaan tertunda untuk buku ini.');
        }

        Loan::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'loan_date' => Carbon::now()->toDateString(),
            'due_date' => $request->due_date,
            'status' => 'pending',
        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'title' => 'Permintaan Pinjam Terkirim',
            'message' => "Permintaan pinjam buku \"{$book->title}\" berhasil diajukan. Menunggu persetujuan petugas.",
            'type' => 'info',
            'action_url' => '/peminjam/loans',
            'is_read' => false,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Permintaan pinjam berhasil dikirim. Tunggu persetujuan admin.']);
        }

        return redirect()->route('peminjam.loans.index')->with('success', 'Permintaan peminjaman berhasil dikirim.');
    }

    public function returnBook(Request $request, Loan $loan): JsonResponse|RedirectResponse
    {
        $this->authorize('returnBook', $loan);

        if ($loan->status !== 'dipinjam') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Buku ini tidak dalam status sedang dipinjam.'], 422);
            }
            return back()->with('error', 'Buku ini tidak dalam status sedang dipinjam.');
        }

        $loan->update(['status' => 'return_requested']);

        Notification::create([
            'user_id' => auth()->id(),
            'title' => 'Permintaan Pengembalian Terkirim',
            'message' => "Permintaan pengembalian buku \"{$loan->book->title}\" berhasil diajukan ke petugas perpustakaan.",
            'type' => 'info',
            'action_url' => '/peminjam/loans',
            'is_read' => false,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Permintaan pengembalian telah dikirim. Tunggu konfirmasi petugas perpustakaan.']);
        }

        return redirect()
            ->route('peminjam.loans.index')
            ->with('success', 'Permintaan pengembalian telah dikirim. Tunggu konfirmasi petugas perpustakaan.');
    }
}
