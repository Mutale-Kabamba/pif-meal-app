<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Auth\LoginController;
use App\Livewire\TerminalPage;

// Auth routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Matrix
Route::middleware(['auth'])->group(function () {
    Route::get('/terminal', TerminalPage::class)->name('terminal');

    /**
     * Secure Download & Print Proxy Endpoint Route
     * Dual-mode handler: 
     * - Append '?stream=true' to load directly in browser frame for clean printing.
     * - Omit parameter to force download attachment.
     */
    Route::get('/admin/generate-cards/direct-download/{filename}', function ($filename) {
        $filePath = 'public/generated_sheets/' . $filename;
        
        if (!Storage::exists($filePath)) {
            abort(404, 'Requested card sheet file no longer exists in history storage folders.');
        }

        if (request()->query('stream') === 'true') {
            return response()->file(Storage::path($filePath), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);
        }

        return Storage::download($filePath);
    })->name('pdf.direct-download');
});

// Redirect based on role
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->isCook()) {
            return redirect()->route('terminal');
        }
        return redirect('/admin');
    })->name('dashboard');
});

// Guest redirect
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
})->name('home');