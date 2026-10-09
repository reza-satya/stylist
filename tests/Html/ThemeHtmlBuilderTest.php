<?php
namespace Tests\Html;

use FloatingPoint\Stylist\Facades\StylistFacade;
use FloatingPoint\Stylist\Html\ThemeHtmlBuilder;
use Spatie\Html\Html;
use Tests\TestCase;

class ThemeHtmlBuilderTest extends TestCase
{
    private $builder;

    public function init()
    {
        $this->builder = new ThemeHtmlBuilder($this->app->make(Html::class), $this->app['url']);

        StylistFacade::registerPath(__DIR__.'/../Stubs/Themes/Parent');
        StylistFacade::activate('Parent theme');
    }

    public function testScriptUrlCreation()
    {
        $script = $this->builder->script('script.js');

        $this->assertStringContainsString('/themes/parent-theme/script.js', (string) $script);
        $this->assertStringContainsString('<script', (string) $script);
        $this->assertStringContainsString('src=', (string) $script);
    }

    public function testStyleUrlCreation()
    {
        $style = $this->builder->style('css/app.css');

        $this->assertStringContainsString('/themes/parent-theme/css/app.css', (string) $style);
        $this->assertStringContainsString('<link', (string) $style);
        $this->assertStringContainsString('rel="stylesheet"', (string) $style);
    }

    public function testImageUrlCreation()
    {
        $image = $this->builder->image('images/my-image.png');

        $this->assertStringContainsString('/themes/parent-theme/images/my-image.png', (string) $image);
    }

    public function testHtmlLinkAssetCreation()
    {
        $flashLink = $this->builder->linkAsset('swf/video.swf');

        $this->assertStringContainsString('/themes/parent-theme/swf/video.swf', (string) $flashLink);
    }

    public function testAssetUrlResponse()
    {
        $this->assertEquals(url('themes/parent-theme/'), $this->builder->url());
        $this->assertEquals(url('themes/parent-theme/favicon.ico'), $this->builder->url('favicon.ico'));
    }
}
