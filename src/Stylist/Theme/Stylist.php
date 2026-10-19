<?php

namespace FloatingPoint\Stylist\Theme;

use FloatingPoint\Stylist\Theme\Exceptions\ThemeNotFoundException;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\ViewFinderInterface;

/**
 * Class Stylist
 *
 * Manages a repository of themes that are registered. Can be used to activate specific themes,
 * search for a given theme, register new ones or even search for themes within your application
 * directory.
 */
class Stylist
{
    /**
     * The cache key is used for setting and retrieving the stylist theme cache.
     */
    private string $cacheKey = 'stylist.themes';

    /**
     * An array of registered themes.
     */
    protected array $themes = [];

    /**
     * The currently activated theme.
     */
    protected Theme $activeTheme;

    /**
     * Manages the loading of themes via various mechanisms.
     */
    private Loader $themeLoader;

    private Container $app;

    private ViewFinderInterface $view;

    public function __construct(Loader $themeLoader, Container $app)
    {
        $this->themeLoader = $themeLoader;
        $this->app = $app;
        $this->view = $this->app->make('view');
    }

    /**
     * Register a new theme based on its path. An optional
     * parameter allows the theme to be activated as soon as its registered.
     */
    public function register(Theme $theme, bool $activate = false): void
    {
        if (! $this->has($theme->getName())) {
            $this->themes[] = $theme;
        }

        if ($activate) {
            $this->activate($theme);
        }
    }

    /**
     * Register a theme with Stylist based on its path.
     *
     */
    public function registerPath(string $path, bool $activate = false): void
    {
        $realPath = realpath($path);
        $theme = $this->themeLoader->fromPath($realPath);

        $this->register($theme, $activate);
    }

    /**
     * Register a number of themes based on the array of paths provided.
     */
    public function registerPaths(array $paths): void
    {
        foreach ($paths as $path) {
            $this->registerPath($path);
        }
    }

    /**
     * Activate a theme. Activation can be done by the theme's name, or via a Theme object.
     *
     * @param string|Theme $theme
     * @throws ThemeNotFoundException
     */
    public function activate($theme)
    {
        if (!$theme instanceof $theme) {
            $theme = $this->get($theme);
        }

        $this->activeTheme = $theme;

        $this->activateFinderPaths($theme);
    }

    /**
     * Activates the view finder paths for a theme and its parents.
     */
    protected function activateFinderPaths(Theme $theme)
    {
        if ($theme->hasParent()) {
            $this->activateFinderPaths($this->get($theme->getParent()));
        }

        $this->view->getFinder()->prependLocation($theme->getPath().'/views/');
    }

    /**
     * Returns the currently active theme.
     */
    public function current(): Theme
    {
        return $this->activeTheme;
    }

    /**
     * Checks to see whether a theme by a given name has been registered.
     */
    public function has(string $themeName): bool
    {
        foreach ($this->themes as $theme) {
            if ($theme->getName() == $themeName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves a theme based on its name. If no theme is found it'll throw a ThemeNotFoundException.
     *
     * @throws ThemeNotFoundException
     */
    public function get(string $themeName): Theme
    {
        foreach ($this->themes as $theme) {
            if ($theme->getName() === $themeName) {
                return $theme;
            }
        }

        throw new ThemeNotFoundException($themeName);
    }

    /**
     * Returns an array of themes that have been registered with Stylist.
     */
    public function themes(): array
    {
        return $this->themes;
    }

    /**
     * Searches for theme.json files within the directory structure specified by $directory and
     * returns the theme locations found. This method means that themes do not need to be manually
     * registered, however - it is a costly operation, and should be cached once you've found the
     * themes.
     *
     * @return array Returns an array of theme directory locations
     */
    public function discover(string $directory): array
    {
        $searchString = $directory.'/theme.json';

        $files = str_replace('theme.json', '', $this->rglob($searchString));

        return $files;
    }

    /**
     * Will glob recursively for a files specified within the pattern.
     */
    protected function rglob(string $pattern, int $flags = 0): array
    {
        $files = glob($pattern, $flags);

        if ($files) {
            return $files;
        }

        $files = [];

        $possibleFiles = glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT);

        if ($possibleFiles === false) {
            $possibleFiles = [];
        }

        foreach ($possibleFiles as $dir) {
            $files = array_merge($files, $this->rglob($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }

    /**
     * Caches the themes provide. This is particularly handy if you use the discover method
     * to search your entire installation for themes. Whenever this method is called, it
     * will wipe the old cache file and re-write the new cache.
     *
     * @param  array  $themes  Must consist of Theme objects
     */
    public function cache(array $themes = []): void
    {
        $cacheJson = [];

        foreach ($themes as $theme) {
            $cacheJson[] = $theme->toArray();
        }

        Cache::forever($this->cacheKey, json_encode($cacheJson));
    }

    /**
     * Clear any cache stylist may currently be using for configuration.
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Sets up stylist to use themes from the cache. Stylist uses Laravel's own caching
     * mechanisms, so this could be stored on the disk, in memcache or elsewhere.
     */
    public function setupFromCache(): void
    {
        if (! Cache::has($this->cacheKey)) {
            return;
        }

        $this->themes = [];
        $cachedThemes = json_decode(Cache::get($this->cacheKey));

        foreach ($cachedThemes as $cachedTheme) {
            $this->themes[] = $this->themeLoader->fromCache($cachedTheme);
        }
    }

    /**
     * Return the key used for cache storage.
     */
    public function cacheKey(): string
    {
        return $this->cacheKey;
    }
}
