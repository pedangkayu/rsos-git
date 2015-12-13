<?php

	namespace App\Classes\Karyawan;
	use Illuminate\Support\Facades\Facade;

	class KaryawanFacade extends Facade {
	    
	    protected static function getFacadeAccessor() { return 'Me'; }

	}