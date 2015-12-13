<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_barang_detail extends Model{

   	protected $table = 'data_barang_detail';
	protected $primaryKey = 'id_barang_detail';
	protected $fillable = [
		'id_barang',
		'nm_detail',
		'label'
	];
}
