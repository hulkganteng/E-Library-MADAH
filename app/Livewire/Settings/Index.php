<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public array $settings = [];

    public $lastBackup = null;

    public $backupSize = 0;

    public $logo;

    public $favicon;

    public $currentLogo = null;

    public $currentFavicon = null;

    public $restoreFile = null;

    public function mount()
    {
        $keys = ['school_name', 'library_name', 'address', 'phone', 'email', 'loan_duration_days', 'fine_per_day', 'max_borrow'];
        foreach ($keys as $k) {
            $this->settings[$k] = Setting::get($k, '');
        }
        $this->currentLogo = Setting::get('logo');
        $this->currentFavicon = Setting::get('favicon');
        $this->refreshBackupInfo();
    }

    public function save()
    {
        Gate::authorize('pengaturan.edit');
        $this->validate([
            'settings.school_name' => 'required|string',
            'settings.loan_duration_days' => 'required|integer|min:1',
            'settings.fine_per_day' => 'required|numeric|min:0',
            'settings.max_borrow' => 'required|integer|min:1',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,svg,webp|max:1024',
        ]);

        foreach ($this->settings as $k => $v) {
            Setting::set($k, $v);
        }

        if ($this->logo) {
            if ($this->currentLogo && Storage::disk('public')->exists($this->currentLogo)) {
                Storage::disk('public')->delete($this->currentLogo);
            }
            $logoPath = $this->logo->store('branding', 'public');
            Setting::set('logo', $logoPath);
            $this->currentLogo = $logoPath;
            $this->logo = null;
        }

        if ($this->favicon) {
            if ($this->currentFavicon && Storage::disk('public')->exists($this->currentFavicon)) {
                Storage::disk('public')->delete($this->currentFavicon);
            }
            $faviconPath = $this->favicon->store('branding', 'public');
            Setting::set('favicon', $faviconPath);
            $this->currentFavicon = $faviconPath;
            $this->favicon = null;
        }

        $this->dispatch('notify', ['message' => 'Pengaturan dan identitas visual berhasil disimpan.']);
    }

    public function deleteLogo()
    {
        Gate::authorize('pengaturan.edit');
        if ($this->currentLogo) {
            if (Storage::disk('public')->exists($this->currentLogo)) {
                Storage::disk('public')->delete($this->currentLogo);
            }
            Setting::set('logo', null);
            $this->currentLogo = null;
            $this->dispatch('notify', ['message' => 'Logo berhasil dihapus.']);
        }
    }

    public function deleteFavicon()
    {
        Gate::authorize('pengaturan.edit');
        if ($this->currentFavicon) {
            if (Storage::disk('public')->exists($this->currentFavicon)) {
                Storage::disk('public')->delete($this->currentFavicon);
            }
            Setting::set('favicon', null);
            $this->currentFavicon = null;
            $this->dispatch('notify', ['message' => 'Favicon berhasil dihapus.']);
        }
    }

    public function backup()
    {
        Gate::authorize('backup.create');

        $throttleKey = 'db-backup:' . (auth()->id() ?? request()->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->dispatch('notify', [
                'message' => "Terlalu sering melakukan backup. Tunggu {$seconds} detik.",
                'type' => 'error',
            ]);
            return;
        }

        RateLimiter::hit($throttleKey, 600);

        $tables = [
            'settings',
            'categories',
            'authors',
            'publishers',
            'shelves',
            'class_rooms',
            'teachers',
            'books',
            'author_book',
            'book_copies',
            'loans',
            'guest_visits',
            'announcements',
        ];

        $exportData = [
            'app' => 'E-Library MA Assadah',
            'version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'tables' => [],
        ];

        foreach ($tables as $tableName) {
            if (DB::getSchemaBuilder()->hasTable($tableName)) {
                $exportData['tables'][$tableName] = DB::table($tableName)->get()->map(fn ($row) => (array) $row)->toArray();
            }
        }

        $dir = storage_path('app/backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filename = 'backup-elibrary-'.now()->format('Y-m-d_His').'.json';
        $path = $dir.'/'.$filename;

        file_put_contents($path, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->refreshBackupInfo();
        $this->dispatch('notify', ['message' => "Cadangan JSON berhasil dibuat: $filename"]);

        return response()->download($path, $filename);
    }

    public function restore()
    {
        Gate::authorize('backup.restore');

        $this->validate([
            'restoreFile' => 'required|file|max:102400',
        ]);

        $content = file_get_contents($this->restoreFile->getRealPath());
        $data = json_decode($content, true);

        if (! is_array($data) || ! isset($data['tables']) || ! is_array($data['tables'])) {
            $this->addError('restoreFile', 'Format file JSON cadangan tidak valid.');
            return;
        }

        $isSqlite = DB::getDriverName() === 'sqlite';

        try {
            DB::transaction(function () use ($data, $isSqlite) {
                if ($isSqlite) {
                    DB::statement('PRAGMA foreign_keys = OFF;');
                } else {
                    DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
                }

                $order = [
                    'settings',
                    'categories',
                    'authors',
                    'publishers',
                    'shelves',
                    'class_rooms',
                    'teachers',
                    'books',
                    'author_book',
                    'book_copies',
                    'loans',
                    'guest_visits',
                    'announcements',
                ];

                foreach ($order as $table) {
                    if (isset($data['tables'][$table]) && DB::getSchemaBuilder()->hasTable($table)) {
                        DB::table($table)->truncate();
                        $rows = $data['tables'][$table];
                        if (! empty($rows)) {
                            // Chunk inserts for large tables
                            foreach (array_chunk($rows, 100) as $chunk) {
                                DB::table($table)->insert($chunk);
                            }
                        }
                    }
                }

                if ($isSqlite) {
                    DB::statement('PRAGMA foreign_keys = ON;');
                } else {
                    DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
                }
            });

            $this->reset('restoreFile');
            $this->mount();
            $this->dispatch('notify', ['message' => 'Restorasi database dari file JSON berhasil!']);
        } catch (\Throwable $e) {
            if ($isSqlite) {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
            }
            $this->addError('restoreFile', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }

    private function refreshBackupInfo()
    {
        $dir = storage_path('app/backups');
        $files = is_dir($dir) ? glob($dir.'/*.json') : [];
        $files = array_values(array_filter($files, 'is_file'));
        if ($files) {
            usort($files, fn ($a, $b) => filemtime($a) <=> filemtime($b));
            $last = end($files);
            $this->lastBackup = basename($last).' · '.date('d M Y H:i', filemtime($last));
            $this->backupSize = filesize($last);
        } else {
            $this->lastBackup = null;
            $this->backupSize = 0;
        }
    }

    public function render()
    {
        return view('livewire.settings.index');
    }
}
