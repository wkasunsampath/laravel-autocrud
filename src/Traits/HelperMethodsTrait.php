<?php

namespace WKasunSampath\LaravelAutocrud\Traits;

trait HelperMethodsTrait
{
    /**
     * Gives relative folder according to the path of Autocrud file.
     *
     * Ex: if Usercrud class is in "App\Http\Autocruds\Company\User" folder, it will give
     * "Company\User" for resource context.
     */
    protected function getRelativeFolder(): string
    {
        $classNamespaceArray = explode('\\', get_class($this));
        $autocrudNamespaceArray = explode('\\', config('autocrud.autocrud_file.namespace'));

        return implode('\\', array_diff($classNamespaceArray, $autocrudNamespaceArray));
    }
}
