<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
        $dir = storage_path('app/backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $filename = 'backup-'.now()->format('Y-m-d_His').'.sql';
        $path = $dir.'/'.$filename;
        $dump = "USE elibrary;\n";
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $t) {
            $name = array_values((array) $t)[0];
            $dump .= "-- Table: $name\n";
            $create = DB::selectOne("SHOW CREATE TABLE `$name`");
            $dump .= array_values((array) $create)[1].";\n\n";
            $rows = DB::table($name)->get();
            foreach ($rows as $row) {
                $cols = implode(',', array_map(fn ($c) => "`$c`", array_keys((array) $row)));
                $vals = implode(',', array_map(fn ($v) => is_null($v) ? 'NULL' : "'".addslashes($v)."'", array_values((array) $row)));
                $dump .= "INSERT INTO `$name` ($cols) VALUES ($vals);\n";
            }
            $dump .= "\n";
        }
        file_put_contents($path, $dump);
        $this->refreshBackupInfo();
        $this->dispatch('notify', ['message' => "Backup berhasil: $filename"]);
    }

    private function refreshBackupInfo()
    {
        $dir = storage_path('app/backups');
        $files = is_dir($dir) ? glob($dir.'/*.sql') : [];
        $files = array_values(array_filter($files, 'is_file'));
        if ($files) {
            usort($files, fn ($a, $b) => filemtime($a) <=> filemtime($b));
            $last = end($files);
            $this->lastBackup = basename($last).' · '.date('d M Y H:i', filemtime($last));
            $this->backupSize = filesize($last);
        }
    }

    public function render()
    {
        return view('livewire.settings.index');
    }
}
