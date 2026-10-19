<?php

namespace Tests\Console;

use FloatingPoint\Stylist\Facades\StylistFacade as Stylist;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PublishAssetsCommandTest extends TestCase
{
    public function test_asset_publishing()
    {
        Stylist::registerPaths(Stylist::discover(__DIR__.'/../Stubs/Themes'));

        // File::shouldReceive('exists')->andReturn(true)->times(12);
        // File::shouldReceive('get')->times(9);
        // File::shouldReceive('copyDirectory')->times(4);

        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('get')->andReturn('{}');
        File::shouldReceive('copyDirectory')->andReturnTrue();

        // Action
        $this->artisan('stylist:publish')
            ->assertExitCode(0);

        // Assert
        //        $this->assertTrue($this->app['files']->exists(public_path('themes/child-theme')));
        //        $this->assertFalse($this->app['files']->exists(public_path('themes/parent-theme')));
    }

    public function test_asset_publishing_for_specific_theme(): void
    {
        Stylist::registerPaths(Stylist::discover(__DIR__.'/../Stubs/Themes'));

        $this->artisan('stylist:publish', [
            'theme' => 'Parent theme',
        ])->assertExitCode(0);
    }
}
