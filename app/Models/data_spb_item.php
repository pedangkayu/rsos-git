<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_spb_item extends Model{
    
	protected $table 		= 'data_spb_item';
	protected $primaryKey 	= 'id_spb_item';
	protected $fillable 	= [
		'id_spb',
		'id_item',
		'qty_awal',
		'qty',
		'keterangan',
		'status', /* 1:proses|2:selesai */
		'id_gudang', /* Default 0 */
		'id_satuan',
		'qty_lg'
	];


	public function scopeByspb($query, $id){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_spb_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_spb_item.id_satuan')
			->join('ref_satuan AS b', 'b.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_spb_item.id_spb', $id)
			->where('data_spb_item.status', 1)
			->select(
				'data_barang.kode', 
				'data_barang.nm_barang',
				'data_barang.in', 
				'data_barang.out', 
				'ref_satuan.nm_satuan', 
				'data_spb_item.*',
				'b.nm_satuan AS satuan_barang',
				'data_barang.id_satuan AS id_satuan_barang'
			);
	}

	public function scopeDetailspb($query){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_spb_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_spb_item.id_satuan')
			->join('ref_satuan AS b', 'b.id_satuan', '=', 'data_barang.id_satuan')
			->select(
				'data_barang.kode', 
				'data_barang.nm_barang',
				'data_barang.in', 
				'data_barang.out', 
				'ref_satuan.nm_satuan', 
				'data_spb_item.*',
				'b.nm_satuan AS satuan_barang',
				'data_barang.id_satuan AS id_satuan_barang'
			);
	}

}
