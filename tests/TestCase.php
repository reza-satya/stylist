<?php
namespace Tests;

use Mockery as m;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

    public function setUp(): void
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
            'RezaSatya\Stylist\StylistServiceProvider',
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Stylist' => 'RezaSatya\Stylist\Facades\StylistFacade',
            'Theme' => 'RezaSatya\Stylist\Facades\ThemeFacade',
        ];
    }

    protected function getApplicationAliases($app)
    {
        $aliases = parent::getApplicationAliases($app);
        
        $aliases['Stylist'] = 'RezaSatya\Stylist\Facades\StylistFacade';

        return $aliases;
    }
}
