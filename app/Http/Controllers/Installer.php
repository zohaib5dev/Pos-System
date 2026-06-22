<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\ClientRepository;

class Installer extends Controller
{
    /**
     * Safely update .env file
     */
    public function updateEnv(array $data)
    {
        if (empty($data)) return false;

        $envPath = base_path('.env');
        $envLines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($data as $key => $value) {
            $found = false;
            foreach ($envLines as $i => $line) {
                if (str_starts_with($line, $key . '=')) {
                    $envLines[$i] = $key . '="' . $value . '"';
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $envLines[] = $key . '="' . $value . '"';
            }
        }

        file_put_contents($envPath, implode("\n", $envLines));
        return true;
    }

    /**
     * Show application settings form
     */
    public function showApplicationSettings()
    {
        $data = [
            'APP_NAME' => session('env.APP_NAME') ?? config('app.name'),
            'APP_ENV' => session('env.APP_ENV') ?? config('app.env'),
            'APP_DEBUG' => session('env.APP_DEBUG') ?? config('app.debug'),
            'APP_KEY' => session('env.APP_KEY') ?? config('app.key'),
        ];

        return view('installer.applicationSettings', compact('data'));
    }

    /**
     * Save application settings
     */
    public function saveApplicationSettings(Request $request)
    {
        $request->session()->put('env.APP_NAME', $request->app_name);
        $request->session()->put('env.APP_ENV', $request->app_env);
        $request->session()->put('env.APP_DEBUG', $request->app_debug);
        $request->session()->put('env.APP_KEY', $request->app_key);

        return redirect()->route('installer.showDatabaseSettings');
    }

    /**
     * Show database settings form
     */
    public function showDatabaseSettings()
    {
        $db = config('database.connections.mysql');

        $data = [
            'DB_CONNECTION' => session('env.DB_CONNECTION') ?? config('database.default'),
            'DB_HOST' => session('env.DB_HOST') ?? $db['host'] ?? '',
            'DB_PORT' => session('env.DB_PORT') ?? $db['port'] ?? '',
            'DB_DATABASE' => session('env.DB_DATABASE') ?? $db['database'] ?? '',
            'DB_USERNAME' => session('env.DB_USERNAME') ?? $db['username'] ?? '',
            'DB_PASSWORD' => session('env.DB_PASSWORD') ?? $db['password'] ?? '',
        ];

        return view('installer.databaseSettings', compact('data'));
    }

    /**
     * Save database settings
     */
    public function saveDatabaseSettings(Request $request)
    {
        $request->session()->put('env.DB_CONNECTION', $request->db_connection);
        $request->session()->put('env.DB_HOST', $request->db_host);
        $request->session()->put('env.DB_PORT', $request->db_port);
        $request->session()->put('env.DB_DATABASE', $request->db_database);
        $request->session()->put('env.DB_USERNAME', $request->db_username);
        $request->session()->put('env.DB_PASSWORD', $request->db_password);

        return redirect()->route('installer.reviewSettings');
    }

    /**
     * Show review of changes before finalizing
     */
    public function reviewSettings()
    {
        $data = [
            'APP_NAME' => session('env.APP_NAME') ?? 'old',
            'APP_ENV' => session('env.APP_ENV') ?? 'old',
            'APP_DEBUG' => session('env.APP_DEBUG') ?? 'old',
            'APP_KEY' => session('env.APP_KEY') ?? 'old',
            'DB_CONNECTION' => session('env.DB_CONNECTION') ?? 'old',
            'DB_HOST' => session('env.DB_HOST') ?? 'old',
            'DB_PORT' => session('env.DB_PORT') ?? 'old',
            'DB_DATABASE' => session('env.DB_DATABASE') ?? 'old',
            'DB_USERNAME' => session('env.DB_USERNAME') ?? 'old',
            'DB_PASSWORD' => session('env.DB_PASSWORD') ?? 'old',
        ];

        return view('installer.reviewSettings', compact('data'));
    }

    /**
     * Finalize setup: migrations + write .env + mark installed
     */
    public function finalizeSetup(Request $request)
    {
        ini_set('max_execution_time', 2000);
        ini_set('memory_limit', '512M');

        $envData = session('env');
        if (!$envData) {
            return redirect()->back()->with('error', 'Session expired. Please start over.');
        }

        try {
            // 1️⃣ Run migrations first with current config
            Artisan::call('migrate:fresh', [
                '--force' => true,
                '--seed' => true,
            ]);

            // 2️⃣ Queue writing .env AFTER request finishes
            $this->deferEnvUpdate($envData);

            // 3️⃣ Mark installation as done
            Storage::disk('public')->put('installed', 'OK');

        } catch (\Exception $e) {
            \Log::error('Installer finalizeSetup error: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }

        return view('installer.finishedSetup');
    }

    /**
     * Generate new APP_KEY
     */
    public function generateAppKey()
    {
        Artisan::call('key:generate', ['--show' => true]);
        return trim(Artisan::output());
    }

    /**
     * Safely defer .env updates so Laravel doesn't crash mid-request
     */
    protected function deferEnvUpdate(array $data)
    {
        register_shutdown_function(function () use ($data) {
            $envFile = base_path('.env');
            $env = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($data as $key => $value) {
                $found = false;
                foreach ($env as $index => $line) {
                    if (str_starts_with($line, $key . '=')) {
                        $env[$index] = $key . '="' . $value . '"';
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $env[] = $key . '="' . $value . '"';
                }
            }

            file_put_contents($envFile, implode("\n", $env));
            \Artisan::call('config:clear');
            \Artisan::call('config:cache');
        });
    }
}