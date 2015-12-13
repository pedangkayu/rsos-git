<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_faktur_item extends Model {

    protected $table 		= 'data_faktur_item';
	protected $primaryKey 	= 'id_faktur_item';
	protected $fillable 	= [
		'id_faktur',
		'id_item',
		'deskripsi',
		'qty',
		'harga',
		'diskon',
		'total',
		'status',
		'id_po',
		'id_satuan'
	];

	public function scopeByfaktur($query, $id = 0){
		$item = $query->leftJoin('data_barang', 'data_barang.id_barang', '=', 'data_faktur_item.id_item')
			->leftJoin('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_faktur_item.id_satuan')
			->where('data_faktur_item.id_faktur', $id)
			->select(
				'data_faktur_item.*',
				'data_barang.kode',
				'data_barang.nm_barang',
				'ref_satuan.nm_satuan'
			);
	}

}
