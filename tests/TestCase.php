<?php

namespace Tests;

use Mockery as m;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->init();
    }

    protected function init()
    {
        // Stub/template method - overloadable by children
    }

    protected function getPackageProviders($app)
    {
        return [
            'Spatie\Html\HtmlServiceProvider',
            'FloatingPoint\Stylist\StylistServiceProvider',
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Stylist' => 'FloatingPoint\Stylist\Facades\StylistFacade',
            'Theme' => 'FloatingPoint\Stylist\Facades\ThemeFacade',
        ];
    }
}
