<?php

namespace Tests\Theme;

use FloatingPoint\Stylist\Theme\Exceptions\ThemeJsonNotFoundException;
use FloatingPoint\Stylist\Theme\Json;
use Tests\TestCase;

class JsonTest extends TestCase
{
    private $themeJson;

    protected function init()
    {
        $this->themeJson = new Json(__DIR__.'/../Stubs/Themes/Parent');
    }

    public function test_json_retrieval_for_existing_theme()
    {
        $json = $this->themeJson->getJson();

        $this->assertEquals('Parent theme', $json->name);
    }

    public function test_json_attribute_retrieval()
    {
        $this->themeJson->getJson();

        $this->assertEquals('Parent theme', $this->themeJson->getJsonAttribute('name'));
        $this->assertEquals('This is a parent theme.', $this->themeJson->getJsonAttribute('description'));
    }

    public function test_theme_file_missing()
    {
        $this->expectException(ThemeJsonNotFoundException::class);
        $this->expectExceptionMessage(
            'theme.json file does not exist at [path/that/doesnt/exist].'
        );

        $json = new Json('path/that/doesnt/exist');

        $json->getJson();
    }
}
