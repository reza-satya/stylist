<?php

namespace FloatingPoint\Stylist\Theme\Exceptions;

use RuntimeException;

class ThemeNotFoundException extends RuntimeException
{
    public function __construct(string $themeName)
    {
        parent::__construct(
            "Theme [$themeName] is not registered with Stylist."
        );
    }
}
