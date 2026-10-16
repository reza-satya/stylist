<?php

namespace FloatingPoint\Stylist\Theme\Exceptions;

use RuntimeException;

class ThemeJsonNotFoundException extends RuntimeException
{
    public function __construct(string $path)
    {
        parent::__construct(
            "theme.json file does not exist at [$path]."
        );
    }
}
