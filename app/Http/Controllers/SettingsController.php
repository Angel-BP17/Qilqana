<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAccess($request);

        $refreshInterval = (int) Setting::getValue('charges_refresh_interval', '5');
        $chargePeriod = Setting::getValue('charge_period', '');

        return view('settings.index', compact('refreshInterval', 'chargePeriod'));
    }

    public function update(Request $request)
    {
        $this->authorizeAccess($request);

        $data = $request->validate([
            'charges_refresh_interval' => ['required', 'integer', 'min:3', 'max:3600'],
            'charge_period' => ['nullable', 'string', 'size:4', 'regex:/^\d{4}$/'],
        ]);

        Setting::setValue('charges_refresh_interval', (string) $data['charges_refresh_interval']);
        Setting::setValue('charge_period', $data['charge_period'] ?? null);

        return redirect()->route('settings.index')->with('success', 'Configuración actualizada correctamente.');
    }

    public function backup(Request $request)
    {
        $this->authorizeAccess($request);

        $timestamp = now()->format('Ymd_His');
        $backupDir = storage_path('app/backups');
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $zipPath = $backupDir.DIRECTORY_SEPARATOR."backup_{$timestamp}.zip";
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()->route('settings.index')->with('error', 'No se pudo crear el backup.');
        }

        foreach ($this->backupTables() as $table) {
            $data = DB::table($table)->get();
            $zip->addFromString("db/{$table}.json", $data->toJson(JSON_UNESCAPED_UNICODE));
        }

        $this->addStorageFilesToZip($zip, 'private/charges_signatures');
        $this->addStorageFilesToZip($zip, 'private/charges_poder');
        // Incluir evidencias adjuntas en el backup.
        $this->addStorageFilesToZip($zip, 'private/charges_evidence');

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $this->authorizeAccess($request);

        $data = $request->validate([
            'backup_file' => ['required', 'file', 'mimes:zip'],
        ]);

        $path = $data['backup_file']->store('tmp', 'local');
        $absolutePath = Storage::disk('local')->path($path);
        $extractPath = storage_path('app/tmp/backup_'.now()->format('Ymd_His'));

        $zip = new ZipArchive;
        if ($zip->open($absolutePath) !== true) {
            return redirect()->route('settings.index')->with('error', 'No se pudo abrir el backup.');
        }

        if (! is_dir($extractPath)) {
            mkdir($extractPath, 0755, true);
        }
        $zip->extractTo($extractPath);
        $zip->close();

        $this->restoreFromBackup($extractPath);

        Storage::disk('local')->delete($path);
        $this->deleteDirectory($extractPath);

        return redirect()->route('settings.index')->with('success', 'Backup importado correctamente.');
    }

    public function reset(Request $request)
    {
        $this->authorizeAccess($request);

        DB::transaction(function () {
            $this->disableForeignKeys();
            foreach ($this->backupTables() as $table) {
                DB::table($table)->delete();
            }
            $this->enableForeignKeys();
        });

        Storage::disk('local')->deleteDirectory('private/charges_signatures');
        Storage::disk('local')->deleteDirectory('private/charges_poder');
        Storage::disk('local')->deleteDirectory('private/charges_evidence');

        Artisan::call('db:seed');

        return redirect()->route('settings.index')->with('success', 'Sistema reiniciado correctamente.');
    }

    protected function authorizeAccess(Request $request): void
    {
        $user = $request->user();
        $allowed = $user?->hasRole('ADMINISTRADOR') || $user?->can('modulo configuracion');
        if (! $allowed) {
            abort(403);
        }
    }

    protected function backupTables(): array
    {
        return [
            'resolucions',
            'charges',
            'natural_people',
            'legal_entities',
            'signatures',
            'users',
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
            'settings',
        ];
    }

    protected function addStorageFilesToZip(ZipArchive $zip, string $directory): void
    {
        $disk = Storage::disk('local');
        foreach ($disk->allFiles($directory) as $file) {
            $contents = $disk->get($file);
            $zip->addFromString("storage/{$file}", $contents);
        }
    }

    protected function restoreFromBackup(string $extractPath): void
    {
        DB::transaction(function () use ($extractPath) {
            $this->disableForeignKeys();
            foreach ($this->backupTables() as $table) {
                DB::table($table)->delete();
                $filePath = $extractPath.DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR."{$table}.json";
                if (! file_exists($filePath)) {
                    continue;
                }
                $data = json_decode(file_get_contents($filePath), true) ?? [];
                if ($data) {
                    foreach (array_chunk($data, 500) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                }
            }
            $this->enableForeignKeys();
        });

        Storage::disk('local')->deleteDirectory('private/charges_signatures');
        Storage::disk('local')->deleteDirectory('private/charges_poder');
        Storage::disk('local')->deleteDirectory('private/charges_evidence');

        $storagePath = $extractPath.DIRECTORY_SEPARATOR.'storage';
        if (is_dir($storagePath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($storagePath, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }
                $relative = str_replace($storagePath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                $contents = file_get_contents($file->getPathname());
                Storage::disk('local')->put($relative, $contents);
            }
        }
    }

    protected function disableForeignKeys(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }
    }

    protected function enableForeignKeys(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    protected function deleteDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($path);
    }
}
