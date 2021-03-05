<?php

namespace App;

use Illuminate\View\FileViewFinder;
use Illuminate\Filesystem\Filesystem as Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container as Container;
use Illuminate\View\Factory;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\View as IlluminateView;

class View
{
    protected $viewPath;

    public static function make($viewPath, $compiledPath)
    {
        $view = new self($viewPath, $compiledPath);
        return $view;
    }

    public function __construct($viewPath, $compiledPath)
    {
        $this->viewPath = $viewPath;
        $this->compiledPath = $compiledPath;
    }

    public function render($view, $data = [])
    {
        $r = $this->loadBlade($view, $data);

        return $r;
    }

    protected function loadBlade($viewName, $data = array())
    {
        $viewPath = $this->viewPath . '/' . $viewName . '.blade.php';

        // this path needs to be array
        $FileViewFinder = new FileViewFinder(new Filesystem, [$viewPath]);

        // use blade instead of phpengine
        // pass in filesystem object and cache path
        $compiler = new BladeCompiler(new Filesystem(), $this->compiledPath);
        $BladeEngine = new CompilerEngine($compiler);

        // create a dispatcher
        $dispatcher = new Dispatcher(new Container);

        // build the factory
        $factory = new Factory(
            new EngineResolver,
            $FileViewFinder,
            $dispatcher
        );

        // this path needs to be string
        $viewObj = new IlluminateView(
            $factory,
            $BladeEngine,
            'not-sure-what-this-does',
            $viewPath,
            $data
        );

        return $viewObj;
    }
}
