<?php

	namespace App\Classes\LogUser;
	use Illuminate\Support\Facades\Facade;

	class LogUserFacade extends Facade {
	    
	    protected static function getFacadeAccessor() { return 'Loguser'; }

	}