<?php

namespace WKasunSampath\LaravelAutocrud\Commands;

use Illuminate\Console\GeneratorCommand;

class LaravelAutocrudInstallFilesCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'autocrud:generate {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all Autocrud files';

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
        return __DIR__.'/stubs/autocrudinstall.php.stub';
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
        return str_replace('{{crud_router_class}}', config('autocrud.autocrud_file.name'), $stub);
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return config('autocrud.autocrud_file.name');
    }
}
