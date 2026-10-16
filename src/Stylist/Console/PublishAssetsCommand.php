<?php

namespace FloatingPoint\Stylist\Console;

use FloatingPoint\Stylist\Facades\StylistFacade as Stylist;
use FloatingPoint\Stylist\Theme\Theme;
use Illuminate\Console\Command;

class PublishAssetsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'stylist:publish {theme? : Name of the theme to publish}';

    /**
     * @var string
     */
    protected $description = 'Publish assets associated with Stylist themes.';

    /**
     * Fire the command, running through the following steps:
     *
     *   1. Install the migrations table
     *   3. Migrate the shift package
     *   4. Publish any and all assets
     *   5. Rebuild the services.json file
     */
    public function handle()
    {
        $this->setupThemes();
        $this->publishAssets();

        $this->info('Assets published.');
    }

    /**
     * Fires the publishing event, then works through the array of returned paths and registers
     * themes for those which are valid (aka, contain a theme.json file).
     */
    protected function setupThemes()
    {
        $this->laravel->make('events')->dispatch('stylist.publishing');

        $themes = Stylist::themes();

        foreach ($themes as $theme) {
            $path = $theme->getPath();

            if ($this->laravel->make('files')->exists($path.'assets/')) {
                $this->laravel->make('stylist')->registerPath($path);
            }
        }
    }

    /**
     * Copies the assets for those themes which were successfully registered with stylist.
     */
    protected function publishAssets()
    {
        $themes = $this->laravel->make('stylist')->themes();
        $requestedTheme = $this->argument('theme');

        if ($requestedTheme) {
            $theme = $this->laravel->make('stylist')->get($requestedTheme);

            return $this->publishSingle($theme);
        }

        foreach ($themes as $theme) {
            $this->publishSingle($theme);
        }
    }

    /**
     * Publish a single theme's assets.
     */
    protected function publishSingle(Theme $theme)
    {
        $themePath = public_path('themes/'.$theme->getAssetPath());

        $this->laravel->make('files')->copyDirectory($theme->getPath().'/assets/', $themePath);

        $this->info($theme->getName().' assets published.');
    }
}
