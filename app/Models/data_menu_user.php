<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_menu_user extends Model{
    
	protected $table 		= 'data_menu_user';
	protected $primaryKey 	= 'id_menu_user';
	protected $fillable 		= [
		'id_menu',
		'id_level'
	];

}
