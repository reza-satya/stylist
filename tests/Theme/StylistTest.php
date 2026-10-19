<?php

namespace Tests\Theme;

use FloatingPoint\Stylist\Theme\Exceptions\ThemeNotFoundException;
use FloatingPoint\Stylist\Theme\Loader;
use FloatingPoint\Stylist\Theme\Stylist;
use FloatingPoint\Stylist\Theme\Theme;
use InvalidArgumentException;
use Tests\TestCase;

class StylistTest extends TestCase
{
    public function test_theme_registration()
    {
        $stylist = new Stylist(new Loader, $this->app);
        $theme = new Theme('n', 'd', 'path');

        $stylist->register($theme, true);

        $this->assertEquals($stylist->get('n'), $theme);
        $this->assertEquals($stylist->current(), $theme);
    }

    public function test_activation_accepts_theme_object_and_theme_name(): void
    {
        $stylist = new Stylist(new Loader, $this->app);
        $theme = new Theme('name', 'description', 'path');
        $stylist->register($theme);
        $stylist->activate('name');
        $this->assertSame($theme, $stylist->current());
        $stylist->activate($theme);
        $this->assertSame($theme, $stylist->current());
    }

    public function test_theme_discovery()
    {
        $stylist = new Stylist(new Loader, $this->app);
        $themes = $stylist->discover(__DIR__.'/../Stubs');

        $this->assertCount(3, $themes);
    }

    public function test_cache_management()
    {
        $stylist = new Stylist(new Loader, $this->app);
        $theme = new Theme('name', 'desc', 'path');

        $stylist->cache([$theme]);

        // To test cache, we setup a new stylist instance
        $stylist = new Stylist(new Loader, $this->app);
        $stylist->setupFromCache();

        $this->assertEquals($theme, $stylist->get('name'));
    }

    public function test_path_registration()
    {
        $stylist = new Stylist(new Loader, $this->app);

        $stylist->registerPath(__DIR__.'/../Stubs/Themes/Parent');

        $this->assertEquals('Parent theme', $stylist->get('Parent theme')->getName());
    }

    public function test_multiple_path_registrations()
    {
        $stylist = new Stylist(new Loader, $this->app);
        $paths = $stylist->discover(__DIR__.'/../Stubs');

        $stylist->registerPaths($paths);
        $stylist->activate($stylist->get('Child theme'));

        $view = $this->app->make('view');

        $this->assertEquals('Parent theme', $stylist->get('Parent theme')->getName());
        $this->assertEquals('Child theme', $stylist->get('Child theme')->getName());
        $this->assertTrue($view->exists('partials.menu')); // should pull this from the child theme
        $this->assertTrue($view->exists('layouts.application')); // should pull this from the parent theme
    }

    public function test_invalid_theme()
    {
        $this->expectException(ThemeNotFoundException::class);
        $this->expectExceptionMessage(
            'Theme [invalidtheme] is not registered with Stylist.'
        );

        $stylist = new Stylist(new Loader, $this->app);
        $stylist->get('invalidtheme');
    }

    public function test_theme_view_is_overloadable()
    {
        $stylist = new Stylist(new Loader, $this->app);
        $paths = $stylist->discover(__DIR__.'/../Stubs');

        $stylist->registerPaths($paths);
        $stylist->activate($stylist->get('Overloader'));

        $view = $this->app->make('view');

        $this->assertTrue($view->exists('partials.menu'));
        $this->assertSame('Overload', $view->make('partials.menu')->render());
    }

    public function test_theme_path_does_not_exist()
    {
        $stylist = new Stylist(new Loader, $this->app);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionmessage("Theme path does not exist: [Themes].");
        $stylist->registerPath('Themes');
    }
}
