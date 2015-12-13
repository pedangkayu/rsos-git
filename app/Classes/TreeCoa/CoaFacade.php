<?php

	namespace App\Classes\TreeCoa;
	use Illuminate\Support\Facades\Facade;

	class CoaFacade extends Facade {
	    
	    protected static function getFacadeAccessor() { return 'Coa'; }

	}