<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_skb_item extends Model {

    protected $table 		= 'data_skb_item';
	protected $primaryKey 	= 'id_skb_item';
	protected $fillable 	= [
		'id_spb_item',
		'id_skb',
		'id_spb',
		'id_item',
		'id_gudang',
		'qty',
		'keterangan',
		'status',
		'id_satuan',
		'qty_lg',
		'sisa'
	];
	
	public function scopeByskb($query, $id){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_skb_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
			->join('data_spb_item', 'data_spb_item.id_spb_item', '=', 'data_skb_item.id_spb_item')
			->where('data_skb_item.status', 1)
			->where('data_skb_item.id_skb', $id)
			->select(
				'data_barang.kode', 
				'data_barang.nm_barang',
				'data_barang.in', 
				'data_barang.out', 
				'ref_satuan.nm_satuan', 
				'data_spb_item.qty_awal',
				'data_skb_item.*'
			);
	}

	public function scopeRetur($query, $id_skb){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_skb_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_skb_item.id_skb', $id_skb)
			->select(
				'data_skb_item.*', 
				'data_barang.id_barang',
				'data_barang.nm_barang',
				'data_barang.kode',
				'ref_satuan.nm_satuan',
				'ref_satuan.id_satuan'
			);
	}

	public function scopeLpb($query, $req = [], $tipe){
		$item = $query->join('data_skb', 'data_skb.id_skb', '=', 'data_skb_item.id_skb')
			->join('data_barang', 'data_barang.id_barang', '=', 'data_skb_item.id_item')
			->where('data_skb_item.id_gudang', $req['id_gudang'])
			->where('data_barang.tipe', $tipe);

		if(!empty($req['waktu']) && $req['waktu'] == 1)
			$item->where(\DB::raw('MONTH(data_skb.created_at)'), $req['bulan'])
				->where(\DB::raw('YEAR(data_skb.created_at)'), $req['tahun']);
		else if(!empty($req['waktu']) && $req['waktu'] == 2)
			$item->whereBetween(\DB::raw('DATE(data_skb.created_at)'), [$req['dari'], $req['sampai']]);

		$item->select(
			'data_barang.nm_barang',
			'data_barang.harga_beli',
			// 'data_po.titipan',
			\DB::raw('SUM(data_skb_item.qty) AS total'),
			\DB::raw('SUM(data_barang.harga_beli * data_skb_item.qty) AS harga')
		)
		->groupby('data_barang.nm_barang');
	}

}
