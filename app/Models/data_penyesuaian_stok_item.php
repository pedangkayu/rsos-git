<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_penyesuaian_stok_item extends Model {

    protected $table 		= 'data_penyesuaian_stok_item';
	protected $primaryKey 	= 'id_penyesuaian_stok_item';
	protected $fillable 	= [
		'id_penyesuaian_stok',
		'id_barang',
		'id_satuan',
		'qty_lg',
		'current_qty',
		'new_qty',
		'keterangan',
		'status'
	];

	public function scopeByhead($query, $id){
		$items = $query->join('data_barang', 'data_barang.id_barang', '=', 'data_penyesuaian_stok_item.id_barang')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_penyesuaian_stok_item.id_satuan')
			->join('ref_satuan AS a', 'a.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_penyesuaian_stok_item.id_penyesuaian_stok', $id)
			->select(
				'data_penyesuaian_stok_item.*', 
				'data_barang.nm_barang', 
				'data_barang.kode', 
				'ref_satuan.nm_satuan',
				'a.nm_satuan AS satuan_default'
			);
	}
}
