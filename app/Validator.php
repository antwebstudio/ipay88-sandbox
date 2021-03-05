<?php

namespace App;

use Illuminate\Validation;
use Illuminate\Translation;
use Illuminate\Validation\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

class Validator
{
    public $lang;
    public $group;
    public $factory;
    public $namespace;

    // Translations root directory
    public $basePath;

    public static $translator;

    protected static $translationRootPath;

    public function __construct($namespace = 'lang', $lang = 'en', $group = 'validation')
    {
        $this->lang = $lang;
        $this->group = $group;
        $this->namespace = $namespace;
        $this->basePath = $this->getTranslationsRootPath();
        $this->factory = new Factory($this->loadTranslator());
    }

    public static function make($data, $rules)
    {
        $validatorFactory = new self();
        return $validatorFactory->factory->make($data, $rules);
    }

    public static function setTranslationPath($path)
    {
        self::$translationRootPath = $path;
    }

    public function translationsRootPath(string $path = '')
    {
        if (!empty($path)) {
            $this->basePath = $path;
            $this->reloadValidatorFactory();
        }
        return $this;
    }

    private function reloadValidatorFactory()
    {
        $this->factory = new Factory($this->loadTranslator());
        return $this;
    }

    public function getTranslationsRootPath(): string
    {
        return self::$translationRootPath . '/' ?? dirname(__FILE__) . '/';
    }

    public function loadTranslator(): Translator
    {
        $loader = new FileLoader(new Filesystem(), $this->basePath . $this->namespace);
        $loader->addNamespace($this->namespace, $this->basePath . $this->namespace);
        $loader->load($this->lang, $this->group, $this->namespace);
        return static::$translator = new Translator($loader, $this->lang);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->factory, $method], $args);
    }
}
