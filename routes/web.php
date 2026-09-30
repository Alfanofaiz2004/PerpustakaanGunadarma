<?php

use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Peminjam\BookController as PeminjamBookController;
use App\Http\Controllers\Peminjam\LoanController as PeminjamLoanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.books.index')
            : redirect()->route('peminjam.books.index');
    }

    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.books.index')
        : redirect()->route('peminjam.books.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::match(['get', 'post'], '/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::resource('books', AdminBookController::class)->except(['show']);
    Route::get('categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::delete('categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('loans', [AdminLoanController::class, 'index'])->name('loans.index');
    Route::post('loans/{loan}/approve', [AdminLoanController::class, 'approve'])->name('loans.approve');
    Route::match(['post', 'delete'], 'loans/{loan}/reject', [AdminLoanController::class, 'reject'])->name('loans.reject');
    Route::post('loans/{loan}/confirm-return', [AdminLoanController::class, 'confirmReturn'])->name('loans.confirm-return');
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
});

Route::middleware(['auth', 'role:peminjam'])->prefix('peminjam')->as('peminjam.')->group(function () {
    Route::get('books', [PeminjamBookController::class, 'index'])->name('books.index');
    Route::get('books/search', [PeminjamBookController::class, 'search'])->name('books.search');
    Route::get('loans', [PeminjamLoanController::class, 'index'])->name('loans.index');
    Route::post('loans', [PeminjamLoanController::class, 'store'])->name('loans.store');
    Route::post('loans/{loan}/return', [PeminjamLoanController::class, 'returnBook'])->name('loans.return');
});

require __DIR__.'/auth.php';
