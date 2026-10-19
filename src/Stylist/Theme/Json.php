<?php

namespace FloatingPoint\Stylist\Theme;

use FloatingPoint\Stylist\Theme\Exceptions\ThemeJsonNotFoundException;
use Illuminate\Support\Facades\File;

class Json
{
    /**
     * Stores the theme object.
     */
    private string $themePath;

    /**
     * Stores the json-decoded file as an array.
     */
    private $json;

    public function __construct(string $themePath)
    {
        $this->themePath = $themePath;
    }

    /**
     * Retrieves the JSON-decoded json file.
     *
     * @throws ThemeJsonNotFoundException
     */
    public function getJson()
    {
        if ($this->json) {
            return $this->json;
        }

        $themeJsonPath = $this->themePath.'/theme.json';

        if (! File::exists($themeJsonPath)) {
            throw new ThemeJsonNotFoundException($this->themePath);
        }

        return $this->json = json_decode(File::get($themeJsonPath));
    }

    /**
     * Returns the value for a specific json attribute.
     */
    public function getJsonAttribute(string $attribute): mixed
    {
        $json = $this->getJson();

        if (isset($json->$attribute)) {
            return $json->$attribute;
        }

        return null;
    }
}
