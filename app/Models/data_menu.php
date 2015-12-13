<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_menu extends Model{
    protected $table 		= 'data_menu';
	protected $primaryKey 	= 'id_menu';
	protected $fillable 	= [
		'parent_id',
		'title',
		'slug',
		'class',
		'class_id',
		'seri',
		'ket',
		'status'
	];
}
