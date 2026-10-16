<?php

namespace FloatingPoint\Stylist\Theme;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

/**
 * Class Theme
 *
 * Theme objects are just dumb little DTOs, used as a reference point for collecting information
 * about themes, namely their name, description and parent details.
 */
class Theme implements Arrayable
{
    private string $name;

    private string $description;

    private ?string $parent;

    /**
     * Absolute path of where the theme can be found on the disk.
     */
    private string $path;

    /**
     * Theme just needs to know one thing - where the theme is found. It'll do the rest.
     *
     * @param  string  $path  Absolute path on the filesystem to the theme.
     */
    public function __construct(
        string $name,
        string $description,
        string $path,
        ?string $parent = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->parent = $parent;
        $this->path = $path;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Determines whether or not a theme has a parent.
     */
    public function hasParent(): bool
    {
        return (bool) $this->parent;
    }

    /**
     * Return the asset path to the theme.
     */
    public function getAssetPath(): string
    {
        return Str::slug($this->getName());
    }

    /**
     * Returns the theme object as an array, containing all theme information.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
