<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_harga extends Model {

    protected $table 		= 'data_harga';
	protected $primaryKey 	= 'id_harga';
	protected $fillable 	= [
		'id_barang',
		'harga',
		'keterangan',
		'keterangan',
		'status',
		'id_po',
		'id_karyawan',
		'tipe' /*  1:harga_beli|2:harga Jual */
	];
}
