<?php

namespace Tests\Theme;

use FloatingPoint\Stylist\Theme\Loader;
use Tests\TestCase;

class LoaderTest extends TestCase
{
    private $loader;

    protected function init()
    {
        $this->loader = new Loader;
    }

    public function test_from_path()
    {
        $theme = $this->loader->fromPath(__DIR__.'/../Stubs/Themes/Parent/');

        $this->assertEquals('Parent theme', $theme->getName());
        $this->assertEquals('This is a parent theme.', $theme->getDescription());
    }

    public function test_from_cache()
    {
        $cachedTheme = new \stdClass;
        $cachedTheme->name = 'name';
        $cachedTheme->description = 'description';
        $cachedTheme->path = 'path';
        $cachedTheme->parent = 'parent';

        $theme = $this->loader->fromCache($cachedTheme);

        $this->assertEquals('name', $theme->getName());
        $this->assertEquals('description', $theme->getDescription());
        $this->assertEquals('path', $theme->getPath());
        $this->assertEquals('parent', $theme->getParent());
    }
}
