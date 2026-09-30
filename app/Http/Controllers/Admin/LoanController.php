<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $query = substr(trim($request->get('q', '')), 0, 100);
        $userId = $request->get('user_id');
        $filteredUser = $userId ? \App\Models\User::find($userId) : null;

        $pendingLoans = Loan::with(['user', 'book'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $returnRequested = Loan::with(['user', 'book'])
            ->where('status', 'return_requested')
            ->latest()
            ->get();

        $allLoans = Loan::with(['user', 'book'])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->whereHas('user', fn($u) => $u->where('name', 'like', "%{$query}%")->orWhere('email', 'like', "%{$query}%")->orWhere('npm', 'like', "%{$query}%"))
                        ->orWhereHas('book', fn($b) => $b->where('title', 'like', "%{$query}%"))
                        ->orWhere('id', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.loans.index', compact('pendingLoans', 'returnRequested', 'allLoans', 'query', 'userId', 'filteredUser'));
    }

    /**
     * Admin approves a pending loan request.
     */
    public function approve(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Permintaan peminjaman ini tidak dalam status menunggu persetujuan.');
        }

        try {
            DB::transaction(function () use ($loan) {
                if ($loan->book->stock <= 0) {
                    throw new \RuntimeException('Stok buku sudah habis, tidak bisa menyetujui peminjaman.');
                }

                $loan->book->decrement('stock');

                $loan->update([
                    'status' => 'dipinjam',
                    'loan_date' => Carbon::now()->toDateString(),
                ]);
            });

            \App\Models\Notification::create([
                'user_id' => $loan->user_id,
                'title' => 'Peminjaman Disetujui',
                'message' => "Permintaan pinjam buku \"{$loan->book->title}\" telah disetujui petugas. Batas pengembalian: " . ($loan->due_date ? $loan->due_date->format('d M Y') : '14 hari kedepan') . ".",
                'type' => 'success',
                'action_url' => '/peminjam/loans',
                'is_read' => false,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Peminjaman buku \"{$loan->book->title}\" oleh {$loan->user->name} telah disetujui.");
    }

    /**
     * Admin rejects a pending loan request with a reason and notifies the borrower.
     */
    public function reject(\Illuminate\Http\Request $request, Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Permintaan peminjaman ini tidak dalam status menunggu persetujuan.');
        }

        $reason = trim($request->get('reason', '')) ?: 'Stok buku sedang tidak tersedia atau kuota peminjaman tercapai.';

        $loan->update([
            'status' => 'ditolak',
            'rejection_note' => $reason,
        ]);

        \App\Models\Notification::create([
            'user_id' => $loan->user_id,
            'title' => 'Permintaan Pinjam Ditolak',
            'message' => "Permintaan pinjam buku \"{$loan->book->title}\" ditolak petugas: {$reason}",
            'type' => 'danger',
            'action_url' => '/peminjam/loans',
            'is_read' => false,
        ]);

        return back()->with('success', "Permintaan peminjaman buku \"{$loan->book->title}\" telah ditolak dan bukti penolakan diteruskan ke peminjam.");
    }

    public function confirmReturn(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'return_requested') {
            return back()->with('error', 'Buku ini tidak dalam status permintaan pengembalian.');
        }

        try {
            DB::transaction(function () use ($loan) {
                $loan->book->increment('stock');

                $loan->update([
                    'status' => 'dikembalikan',
                    'return_date' => Carbon::now()->toDateString(),
                ]);
            });

            \App\Models\Notification::create([
                'user_id' => $loan->user_id,
                'title' => 'Pengembalian Buku Selesai',
                'message' => "Buku \"{$loan->book->title}\" telah dikonfirmasi pengembaliannya oleh petugas perpustakaan. Terima kasih!",
                'type' => 'info',
                'action_url' => '/peminjam/loans',
                'is_read' => false,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses pengembalian buku: ' . $e->getMessage());
        }

        return back()->with('success', "Pengembalian buku \"{$loan->book->title}\" oleh {$loan->user->name} telah dikonfirmasi.");
    }
}
