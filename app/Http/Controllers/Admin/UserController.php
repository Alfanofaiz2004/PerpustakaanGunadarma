<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = substr(trim($request->get('q', '')), 0, 100);

        $users = User::where('role', 'peminjam')
            ->withCount([
                'loans as active_loans_count' => fn($q) => $q->where('status', 'dipinjam'),
                'loans as pending_loans_count' => fn($q) => $q->where('status', 'pending'),
                'loans as return_requested_count' => fn($q) => $q->where('status', 'return_requested'),
                'loans as returned_loans_count' => fn($q) => $q->where('status', 'dikembalikan'),
            ])
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('npm', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $totalMembers = User::where('role', 'peminjam')->count();
        $activeBorrowers = User::where('role', 'peminjam')
            ->whereHas('loans', fn($q) => $q->where('status', 'dipinjam'))
            ->count();

        return view('admin.users.index', compact('users', 'query', 'totalMembers', 'activeBorrowers'));
    }

    public function show(Request $request, User $user): JsonResponse|View
    {
        $user->load(['loans.book']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'npm' => $user->npm ?? '-',
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('d M Y'),
                    'loans' => $user->loans->map(function ($loan) {
                        return [
                            'id' => $loan->id,
                            'book_title' => $loan->book->title ?? 'Buku Terhapus',
                            'book_author' => $loan->book->author ?? '-',
                            'book_cover' => $loan->book->cover_url ?? null,
                            'loan_date' => $loan->loan_date ? $loan->loan_date->format('d M Y') : '-',
                            'due_date' => $loan->due_date ? $loan->due_date->format('d M Y') : '-',
                            'return_date' => $loan->return_date ? $loan->return_date->format('d M Y') : '-',
                            'status' => $loan->status,
                        ];
                    }),
                ],
            ]);
        }

        return view('admin.users.show', compact('user'));
    }
}
