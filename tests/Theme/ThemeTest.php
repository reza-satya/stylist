<?php

namespace Tests\Theme;

use FloatingPoint\Stylist\Theme\Theme;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    private Theme $theme;

    protected function init()
    {
        $this->theme = new Theme('TheThemeName', 'description', 'path', 'parent');
    }

    public function test_getters()
    {
        $this->assertEquals('TheThemeName', $this->theme->getName());
        $this->assertEquals('description', $this->theme->getDescription());
        $this->assertEquals('path', $this->theme->getPath());
        $this->assertEquals('parent', $this->theme->getParent());
        $this->assertEquals('thethemename', $this->theme->getAssetPath());
    }

    public function test_parent_check()
    {
        $this->assertTrue($this->theme->hasParent());
    }

    public function test_to_array()
    {
        $this->assertEquals([
            'name' => 'TheThemeName',
            'description' => 'description',
            'parent' => 'parent',
            'path' => 'path',
        ], $this->theme->toArray());
    }
}
