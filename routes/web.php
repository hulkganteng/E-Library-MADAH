<?php

use App\Livewire\Announcements\Index as Announcements;
use App\Livewire\Audit\Index as Audit;
use App\Livewire\Books\Collections as Copies;
use App\Livewire\Books\Index as Books;
use App\Livewire\Catalog\Index as Catalog;
use App\Livewire\Catalog\Show as CatalogShow;
use App\Livewire\Dashboard;
use App\Livewire\Display\Index as Display;
use App\Livewire\Loans\Index as Loans;
use App\Livewire\Login;
use App\Livewire\Master\Authors;
use App\Livewire\Master\Categories;
use App\Livewire\Master\Classes;
use App\Livewire\Master\Publishers;
use App\Livewire\Master\Shelves;
use App\Livewire\Members\Students;
use App\Livewire\Members\Teachers;
use App\Livewire\Notifications;
use App\Livewire\Reports\Index as Reports;
use App\Livewire\Settings\Index as Settings;
use Illuminate\Support\Facades\Route;

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

    Route::middleware('can:buku.view')->get('/buku', Books::class)->name('books.index');
    Route::middleware('can:eksemplar.view')->get('/eksemplar', Copies::class)->name('copies.index');
    Route::middleware('can:kategori.view')->get('/kategori', Categories::class)->name('categories.index');
    Route::middleware('can:penulis.view')->get('/penulis', Authors::class)->name('authors.index');
    Route::middleware('can:penerbit.view')->get('/penerbit', Publishers::class)->name('publishers.index');
    Route::middleware('can:rak.view')->get('/rak', Shelves::class)->name('shelves.index');
    Route::middleware('can:peminjaman.view')->get('/peminjaman', Loans::class)->name('loans.index');

    Route::middleware('can:siswa.view')->get('/siswa', Students::class)->name('students.index');
    Route::middleware('can:guru.view')->get('/guru', Teachers::class)->name('teachers.index');
    Route::middleware('can:kelas.view')->get('/kelas', Classes::class)->name('classes.index');

    Route::middleware('can:laporan.view')->get('/laporan', Reports::class)->name('reports.index');

    Route::middleware('can:pengumuman.view')->get('/pengumuman', Announcements::class)->name('announcements.index');
    Route::middleware('can:audit.view')->get('/audit', Audit::class)->name('audit.index');
    Route::middleware('can:pengaturan.view')->get('/pengaturan', Settings::class)->name('settings.index');
});
