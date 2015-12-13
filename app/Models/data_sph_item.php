<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_sph_item extends Model {

    protected $table 		= 'data_sph_item';
	protected $primaryKey 	= 'id_sph_item';
	protected $fillable 	= [
		'id_sph',
		'id_prq',
		'id_item',
		'qty',
		'harga',
		'diskon',
		'ppn',
		'pph',
		'keterangan',
		'id_satuan'
	];

	public function scopeBysph($query, $id_sph){
		return $query->join('data_barang', 'data_barang.id_barang', '=','data_sph_item.id_item')
			->where('data_sph_item.id_sph', $id_sph)
			->select('data_sph_item.*', 'data_barang.kode', 'data_barang.nm_barang');
	}
}
