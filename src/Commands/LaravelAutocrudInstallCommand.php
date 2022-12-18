<?php

namespace WKasunSampath\LaravelAutocrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LaravelAutocrudInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autocrud:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all Autocrud classes & folders';

    /**
     * Handle command
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Installing Autocrud...');

        $this->call('autocrud:generate', ['name' => 'Files']);
        $this->info('Published assets');

        if (! $this->configExists('autocrud.php')) {
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }

        $this->info('Installed Autocrud');
    }

    private function configExists(string $fileName): bool
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig(): bool
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration(bool $forcePublish = false): void
    {
        $params = [
            '--provider' => "WKasunSampath\LaravelAutocrud\LaravelAutocrudServiceProvider",
            '--tag' => 'config',
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}
