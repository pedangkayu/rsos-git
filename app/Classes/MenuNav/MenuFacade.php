<?php

	namespace App\Classes\MenuNav;
	use Illuminate\Support\Facades\Facade;

	class MenuFacade extends Facade {
	    
	    protected static function getFacadeAccessor() { return 'Menu'; }

	}