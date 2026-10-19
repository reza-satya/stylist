<?php

namespace FloatingPoint\Stylist\Theme;

/**
 * Class Loader
 *
 * The theme loader will instantiate theme objects based on whether or not we're loading the theme
 * from a path, or from a cached value. Either method will return a new Theme object that represents
 * the theme in all its glory.
 */
class Loader
{
    /**
     * Creates a new theme based on the theme's path, fetching the theme.json file
     * and loading the necessary attributes for that theme.
     */
    public function fromPath(string $path): Theme
    {
        $themeJson = new Json($path);

        return new Theme(
            (string) $themeJson->getJsonAttribute('name'),
            (string) $themeJson->getJsonAttribute('description'),
            $path,
            $themeJson->getJsonAttribute('parent')
        );
    }

    /**
     * Creates a new theme instance based on the cache object provided.
     *
     * @param  stdClass  $cache
     */
    public function fromCache(\stdClass $cache): Theme
    {
        return new Theme(
            (string) $cache->name,
            (string) $cache->description,
            (string) $cache->path,
            isset($cache->parent) ? (string) $cache->parent : null
        );
    }
}
