<?php

namespace WKasunSampath\LaravelAutocrud\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class LaravelAutocrudGeneratorCommand extends GeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'autocrud:create {name} {--m} {--mg} {--c} {--p} {--req} {--r} {--s} {--o}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new autocrud class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Autocrud class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return config('autocrud.app_type') === 'api'
        ? __DIR__ . '/stubs/autocrudApi.php.stub'
        : __DIR__ . '/stubs/autocrudWeb.php.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('autocrud.autocrud_file.namespace');
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        return str_replace('{{crud_class}}', $class, $stub);
    }

    /**
     * Handle command
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $this->doOtherOperations();

        $this->editAutocrudFile();

        $name = explode('/', $this->argument('name'));
        $name = array_pop($name);

        if ($this->hasOption('m') && $this->option('m')) {
            if ($this->hasOption('mg') && $this->option('mg')) {
                $this->call('make:model', ['name' => $this->argument('name'), '--migration' => true]);
            } else {
                $this->call('make:model', ['name' => $this->argument('name')]);
            }
        } elseif ($this->hasOption('mg') && $this->option('mg')) {
            $this->call('make:migration', ['name' => "create_" . Str::plural(strtolower($name)) . "_table"]);
        }

        if ($this->hasOption('c') && $this->option('c')) {
            $this->call('make:controller', ['name' => $this->argument('name') . 'Controller']);
        }

        if ($this->hasOption('p') && $this->option('p')) {
            $this->call('make:policy', ['name' => $this->argument('name') . 'Policy', '--model' => $this->argument('name')]);
        }

        if ($this->hasOption('req') && $this->option('req')) {
            $this->call('make:request', ['name' => $this->argument('name') . 'CreateRequest']);
            $this->call('make:request', ['name' => $this->argument('name') . 'UpdateRequest']);
        }

        if ($this->hasOption('r') && $this->option('r')) {
            $this->call('make:resource', ['name' => $this->argument('name') . 'Resource']);
            $this->call('make:resource', ['name' => $this->argument('name') . 'Collection']);
        }

        if ($this->hasOption('s') && $this->option('s')) {
            $this->call('make:seeder', ['name' => $name . 'Seeder']);
        }

        if ($this->hasOption('o') && $this->option('o')) {
            $this->call('make:observer', ['name' => $name . 'Observer', '--model' => $this->argument('name')]);
        }
    }

    protected function doOtherOperations(): void
    {
        $class = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($class);

        $content = file_get_contents($path);

        $content .= '    protected string $model = \\' .
        config('autocrud.model_namespace') .
        '\\' .
        implode('\\', explode('/', $this->argument('name'))) .
            "::class;\n" .
            "\n    /**
     * Middlewares which are applied to all routes
     *
     * Ex: ['auth:sanctum']
     */
    public function commonMiddlewares(): array
    {
        return [];
    }
           ";

        $content .= config('autocrud.app_type') !== 'api' ? $this->webSpecificMethods() : "\n}";

        file_put_contents($path, $content);
    }

    protected function webSpecificMethods(): string
    {
        return "\n    /**
     * View to redirect after index operation
     * (This is applicable for web routes only.)
     *
     * Ex: For Users, it will be \"users.index\". Response will redirect to
     * \"resources/views/users/index.blade.php\"
     */
    public function afterIndexPage(): string
    {
        return \$this->routeName() . '.index';
    }

    /**
     * View to redirect after view operation
     * (This is applicable for web routes only.)
     *
     * Ex: For Users, it will be \"users.view\". Response will redirect to
     * \"resources/views/users/view.blade.php\"
     */
    public function afterViewPage(): string
    {
        return \$this->routeName() . '.view';
    }

    /**
     * View to redirect after create operation
     * (This is applicable for web routes only.)
     *
     * Ex: For Users, it will be \"users.create\". Response will redirect to
     * \"resources/views/users/create.blade.php\"
     */
    public function afterCreatePage(): string
    {
        return \$this->routeName() . '.create';
    }

    /**
     * View to redirect after update operation
     * (This is applicable for web routes only.)
     *
     * Ex: For Users, it will be \"users.update\". Response will redirect to
     * \"resources/views/users/update.blade.php\"
     */
    public function afterUpdatePage(): string
    {
        return \$this->routeName() . '.update';
    }

    /**
     * View to redirect after delete operation
     * (This is applicable for web routes only.)
     *
     * Ex: For Users, it will be \"users.delete\". Response will redirect to
     * \"resources/views/users/delete.blade.php\"
     */
    public function afterDeletePage(): string
    {
        return \$this->routeName() . '.delete';
    }
        " .
            "\n}";
    }

    protected function editAutocrudFile()
    {
        $path = config('autocrud.autocrud_file.namespace') . '\\' . config('autocrud.autocrud_file.name') . '.php';
        $content = file_get_contents($path);

        $seperatedContent = explode('[', $content);
        $firstPart = $seperatedContent[0];
        $secondParts = explode(']', $seperatedContent[1]);

        $arrayContent = $secondParts[0];
        if (empty($secondParts[0])) {
            $arrayContent .= "\n        \\" . config('autocrud.autocrud_file.namespace') . '\\' . implode('\\', explode('/', $this->argument('name'))) . '::class,';
        } else {
            $arrayContent .= '    \\' . config('autocrud.autocrud_file.namespace') . '\\' . implode('\\', explode('/', $this->argument('name'))) . '::class,';
        }

        $arrayContent .= "\n    ";

        file_put_contents($path, implode('[', [$firstPart, implode(']', [$arrayContent, $secondParts[1]])]));
    }
}
