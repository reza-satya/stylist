<?php

namespace Tests\Html;

use FloatingPoint\Stylist\Facades\StylistFacade;
use FloatingPoint\Stylist\Html\ThemeHtmlBuilder;
use Spatie\Html\Html;
use Tests\TestCase;

class ThemeHtmlBuilderTest extends TestCase
{
    private ThemeHtmlBuilder $builder;

    protected function init()
    {
        $this->builder = new ThemeHtmlBuilder($this->app->make(Html::class), $this->app['url']);

        StylistFacade::registerPath(__DIR__.'/../Stubs/Themes/Parent');
        StylistFacade::activate('Parent theme');
    }

    public function test_script_url_creation()
    {
        $script = $this->builder->script('script.js');

        $this->assertStringContainsString('/themes/parent-theme/script.js', (string) $script);
        $this->assertStringContainsString('<script', (string) $script);
        $this->assertStringContainsString('src=', (string) $script);
    }

    public function test_style_url_creation()
    {
        $style = $this->builder->style('css/app.css');

        $this->assertStringContainsString('/themes/parent-theme/css/app.css', (string) $style);
        $this->assertStringContainsString('<link', (string) $style);
        $this->assertStringContainsString('rel="stylesheet"', (string) $style);
    }

    public function test_image_url_creation()
    {
        $image = $this->builder->image('images/my-image.png');

        $this->assertStringContainsString('/themes/parent-theme/images/my-image.png', (string) $image);
    }

    public function test_html_link_asset_creation()
    {
        $flashLink = $this->builder->linkAsset('swf/video.swf');

        $this->assertStringContainsString('/themes/parent-theme/swf/video.swf', (string) $flashLink);
    }

    public function test_asset_url_response()
    {
        $this->assertEquals(url('themes/parent-theme/'), $this->builder->url());
        $this->assertEquals(url('themes/parent-theme/favicon.ico'), $this->builder->url('favicon.ico'));
    }
}
