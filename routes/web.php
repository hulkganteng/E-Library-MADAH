<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\{
    Dashboard,
    Login,
    Notifications,
    Books\Index as Books,
    Books\Collections as Copies,
    Master\Categories,
    Master\Authors,
    Master\Publishers,
    Master\Shelves,
    Master\Classes,
    Members\Students,
    Members\Teachers,
    Loans\Index as Loans,
    Catalog\Index as Catalog,
    Catalog\Show as CatalogShow,
    Display\Index as Display,
    Reports\Index as Reports,
    Announcements\Index as Announcements,
    Audit\Index as Audit,
    Settings\Index as Settings,
};

Route::get('/', function () {
    return redirect()->route('catalog.index');
})->name('home');

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::get('/katalog', Catalog::class)->name('catalog.index');
Route::get('/katalog/{book}', CatalogShow::class)->name('catalog.show');
Route::get('/display', Display::class)->name('display.index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/notifikasi', Notifications::class)->name('notifications.index');

    Route::middleware('can:katalog.view')->group(function () {
        Route::get('/buku', Books::class)->name('books.index');
        Route::get('/eksemplar', Copies::class)->name('copies.index');
        Route::get('/kategori', Categories::class)->name('categories.index');
        Route::get('/penulis', Authors::class)->name('authors.index');
        Route::get('/penerbit', Publishers::class)->name('publishers.index');
        Route::get('/rak', Shelves::class)->name('shelves.index');
    });

    Route::middleware('can:peminjaman.view')->group(function () {
        Route::get('/peminjaman', Loans::class)->name('loans.index');
    });

    Route::middleware('can:siswa.view')->get('/siswa', Students::class)->name('students.index');
    Route::middleware('can:guru.view')->get('/guru', Teachers::class)->name('teachers.index');
    Route::middleware('can:kelas.view')->get('/kelas', Classes::class)->name('classes.index');

    Route::middleware('can:laporan.view')->get('/laporan', Reports::class)->name('reports.index');

    Route::middleware('role:Admin')->group(function () {
        Route::get('/pengumuman', Announcements::class)->name('announcements.index');
        Route::get('/audit', Audit::class)->name('audit.index');
        Route::get('/pengaturan', Settings::class)->name('settings.index');
    });
});
