<?php
namespace RezaSatya\Stylist\Facades;

use Illuminate\Support\Facades\Facade;

class ThemeFacade extends Facade
{
	public static function getFacadeAccessor()
    {
        return 'stylist.theme';
    }
}
