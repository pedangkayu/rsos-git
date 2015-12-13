<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_po_item extends Model {

    protected $table 		= 'data_po_item';
	protected $primaryKey 	= 'id_po_item';
	protected $fillable 	= [
		'id_po',
		'id_item',
		'qty',
		'harga',
		'diskon',
		'ppn',
		'pph',
		'status', /* 1:baru|2:proses|3:selesai */
		'keterangan',
		'id_satuan',
		'req_qty',
		'id_prq'
	];

	public function scopeForspbmbypo($query, $id){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_po_item.id_satuan')
			->join('ref_satuan AS b', 'b.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_po_item.id_po', $id)
			->whereIn('data_po_item.status', [1,2,3])
			->select(
				'data_po_item.*', 
				'data_barang.nm_barang', 
				'data_barang.kode',
				'data_barang.tipe AS tipe_barang', 
				'ref_satuan.nm_satuan',
				'b.nm_satuan AS satuan_default',
				'b.id_satuan AS id_satuan_default'
			);
	}

	public function scopeBypo($query, $id){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_po_item.id_satuan')
			->join('ref_satuan AS b', 'b.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_po_item.id_po_item', $id)
			->whereIn('data_po_item.status', [1,2,3])
			->select(
				'data_po_item.*', 
				'data_barang.nm_barang', 
				'data_barang.kode', 
				'ref_satuan.nm_satuan',
				'b.nm_satuan AS satuan_default',
				'b.id_satuan AS id_satuan_default'
			)
			->first();
	}

	public function scopeForretur($query, $id){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_po_item.id_satuan')
			->join('ref_satuan AS b', 'b.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_po_item.id_po', $id)
			->whereIn('data_po_item.status', [2,3])
			->select(
				'data_po_item.*', 
				'data_barang.nm_barang', 
				'data_barang.kode', 
				'ref_satuan.nm_satuan',
				'b.nm_satuan AS satuan_default',
				'b.id_satuan AS id_satuan_default'
			);
	}


	/* Laporan Purchase Order */
	public function scopeLaporan($query, $req = []){

		$item = $query->join('data_po', 'data_po.id_po', '=', 'data_po_item.id_po')
			->join('data_vendor', 'data_vendor.id_vendor', '=', 'data_po.id_vendor')
			->join('data_spbm', 'data_spbm.id_po', '=', 'data_po.id_po')
			->join('data_prq', 'data_prq.id_prq', '=', 'data_po_item.id_prq')
			->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_po_item.id_satuan');
		if(count($req) > 0){
			if($req['waktu'] == 1)
				$item->where(\DB::raw('MONTH(data_po.created_at)'), '=', $req['bulan'])
					->where(\DB::raw('YEAR(data_po.created_at)'), '=', $req['tahun']);
			else if($req['waktu'] == 2)
				$item->whereBetween(\DB::raw('DATE(data_po.created_at)'), [$req['dari'], $req['sampai']]);

			if(!empty($req['tipe']))
				$item->where('data_barang.tipe', $req['tipe']);
		}
		$item->select(
			'data_po.created_at AS tanggal_po',
			'data_prq.created_at AS tanggal_prq',
			'data_spbm.created_at AS tanggal_gr',

			'data_prq.no_prq',
			'data_po.no_po',
			'data_spbm.no_spbm',
			'data_vendor.nm_vendor',
			'data_barang.nm_barang',
			'data_barang.kode',
			'data_po_item.req_qty AS qty',
			'data_po_item.harga',
			'data_po.diskon AS gdiskon',
			'data_po.ppn AS gppn',
			'data_po.pph AS gpph',
			'data_po_item.diskon',
			'data_po_item.ppn',
			'data_po_item.pph',
			'data_po.deadline',
			'ref_satuan.nm_satuan',

			'data_prq.id_prq',
			'data_po.id_po',
			'data_spbm.id_spbm'
		);
	}


	/* -------------------GRAFIKS ---------------- */
	public function scopeGrafikobatlimit($query, $limit = 5){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->where('data_barang.tipe', 1)
			->where('data_po_item.status', '>', 2)
			->where(\DB::raw('YEAR(data_po_item.created_at)'), date('Y'))
			->select('data_po_item.id_item','data_barang.kode', 'data_barang.nm_barang', \DB::raw('COUNT(data_po_item.id_item) AS total'))
			->groupby('data_po_item.id_item')
			->orderby('total', 'desc')
			->limit($limit);

	}

	public function scopeGrafikbaranglimit($query, $limit = 5){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->where('data_barang.tipe', 2)
			->where('data_po_item.status', '>', 2)
			->where(\DB::raw('YEAR(data_po_item.created_at)'), date('Y'))
			->select('data_po_item.id_item','data_barang.kode', 'data_barang.nm_barang', \DB::raw('COUNT(data_po_item.id_item) AS total'))
			->groupby('data_po_item.id_item')
			->orderby('total', 'desc')
			->limit($limit);
	}

	public function scopeGrafikpembelian($query,$tipe, $tahun){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->where('data_po_item.status', 3)
			->where(\DB::raw('YEAR(data_po_item.created_at)'), $tahun)
			->where('data_barang.tipe', $tipe)
			->select(\DB::raw('DATE(data_po_item.created_at) AS tanggal, COUNT(data_po_item.id_po_item) AS total'))
			->groupby(\DB::raw('DATE(data_po_item.created_at)'))
			->orderby(\DB::raw('DATE(data_po_item.created_at)'), 'asc');
	}
	
	/* --- RETUR --- */
	public function scopeItemforretur($query, $req = []){
		$items = $query->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->join('data_po', 'data_po.id_po', '=', 'data_po_item.id_po')
			->whereIn('data_po.status', [2,3]);

			if(!empty($req['kode']))
				$items->where('data_barang.kode', $req['kode']);
			if(!empty($req['nm_barang']))
				$items->where('data_barang.nm_barang', 'LIKE', '%' . $req['nm_barang'] . '%');

		$items->select('data_barang.kode', 'data_barang.nm_barang', 'data_po.no_po', 'data_po.id_po', 'data_po.status', 'data_po.created_at');
	}

	/* FAKTUR */
	public function scopeAllpo($query, $id){
		return $query->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_po_item.id_satuan')
			->where('data_po_item.id_po', $id)
			->whereIn('data_po_item.status', [1,2,3])
			->select(
				'data_po_item.*', 
				'data_barang.nm_barang', 
				'data_barang.kode', 
				'ref_satuan.nm_satuan'
			);
	}

	/* LAPORAN PEMBELIAN BARANG DAN OBAT */
	public function scopeLpbdo($query, $req = [], $tipe = 0){
		$item = $query->join('data_po', 'data_po.id_po', '=', 'data_po_item.id_po')
			->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->join('ref_kategori', 'ref_kategori.id_kategori', '=', 'data_barang.id_kategori')
			->where('data_po.status', 3);

		if(!empty($req['titipan']))
			$item->where('data_po.titipan', '>', 0);
		else
			$item->where('data_po.titipan', 0);

		if(!empty($tipe))
			$item->where('data_barang.tipe', $tipe);

		if(!empty($req['waktu']) && $req['waktu'] == 1)
			$item->where(\DB::raw('MONTH(data_po.created_at)'), $req['bulan'])
				->where(\DB::raw('YEAR(data_po.created_at)'), $req['tahun']);
		else if(!empty($req['waktu']) && $req['waktu'] == 2)
			$item->whereBetween(\DB::raw('DATE(data_po.created_at)'), [$req['dari'], $req['sampai']]);
				

		$item->select(
			'ref_kategori.id_kategori',
			'ref_kategori.nm_kategori',
			// 'data_po.titipan',
			\DB::raw('SUM(data_po_item.req_qty) AS total'),
			\DB::raw('SUM((data_po_item.harga - (data_po_item.harga * data_po_item.diskon / 100)) * data_po_item.req_qty) AS harga')
		)
		->groupby('ref_kategori.nm_kategori');
	}

	/* LAPORAN PEMBELIAN */
	public function scopeRekapbelanja($query, $req = []){
		$item = $query->join('data_po', 'data_po.id_po', '=', 'data_po_item.id_po')
			->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->join('ref_kategori', 'ref_kategori.id_kategori', '=', 'data_barang.id_kategori')
			->where('data_po.status', 3);

		if(!empty($req['titipan']))
			$item->where('data_po.titipan', '>', 0);
		else
			$item->where('data_po.titipan', 0);

		if(!empty($req['tipe']))
			$item->where('data_barang.tipe', $req['tipe']);

		if(!empty($req['waktu']) && $req['waktu'] == 1)
			$item->where(\DB::raw('MONTH(data_po.created_at)'), $req['bulan'])
				->where(\DB::raw('YEAR(data_po.created_at)'), $req['tahun']);
		else if(!empty($req['waktu']) && $req['waktu'] == 2)
			$item->whereBetween(\DB::raw('DATE(data_po.created_at)'), [$req['dari'], $req['sampai']]);

		$item->select(
			'ref_kategori.id_kategori',
			'ref_kategori.nm_kategori',
			// 'data_po.titipan',
			\DB::raw('SUM(data_po_item.req_qty) AS total'),
			\DB::raw('SUM((data_po_item.harga - (data_po_item.harga * data_po_item.diskon / 100)) * data_po_item.req_qty) AS harga')
		)
		->groupby('ref_kategori.nm_kategori');
	}

	public function scopeRekapbelanjadetail($query, $req = []){
		$item = $query->join('data_po', 'data_po.id_po', '=', 'data_po_item.id_po')
			->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->where('data_po.status', 3)
			->where('data_barang.id_kategori', $req['id_kategori']);

		if(!empty($req['titipan']))
			$item->where('data_po.titipan', '>', 0);
		else
			$item->where('data_po.titipan', 0);

		if(!empty($req['tipe']))
			$item->where('data_barang.tipe', $req['tipe']);

		if(!empty($req['waktu']) && $req['waktu'] == 1)
			$item->where(\DB::raw('MONTH(data_po.created_at)'), $req['bulan'])
				->where(\DB::raw('YEAR(data_po.created_at)'), $req['tahun']);
		else if(!empty($req['waktu']) && $req['waktu'] == 2)
			$item->whereBetween(\DB::raw('DATE(data_po.created_at)'), [$req['dari'], $req['sampai']]);

		$item->select(
			'data_barang.nm_barang',
			// 'data_po.titipan',
			\DB::raw('SUM(data_po_item.req_qty) AS total'),
			\DB::raw('SUM((data_po_item.harga - (data_po_item.harga * data_po_item.diskon / 100)) * data_po_item.req_qty) AS harga')
		)
		->groupby('data_barang.nm_barang');
	}
	/* END LAPORAN PEMBELIAN */

	// public function scopeRekapatk($query, $req = []){
	// 	$item = $query->join('data_po', 'data_po.id_po', '=', 'data_po_item.id_po')
	// 		->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
	// 		->where('data_po.status', 3)
	// 		->where('data_barang.id_kategori',11);

	// 	if(!empty($req['titipan']))
	// 		$item->where('data_po.titipan', '>', 0);
	// 	else
	// 		$item->where('data_po.titipan', 0);

	// 	if(!empty($req['tipe']))
	// 		$item->where('data_barang.tipe', $req['tipe']);

	// 	if(!empty($req['waktu']) && $req['waktu'] == 1)
	// 		$item->where(\DB::raw('MONTH(data_po.created_at)'), $req['bulan'])
	// 			->where(\DB::raw('YEAR(data_po.created_at)'), $req['tahun']);
	// 	else if(!empty($req['waktu']) && $req['waktu'] == 2)
	// 		$item->whereBetween(\DB::raw('DATE(data_po.created_at)'), [$req['dari'], $req['sampai']]);

	// 	$item->select(
	// 		'data_barang.nm_barang',
	// 		// 'data_po.titipan',
	// 		\DB::raw('SUM(data_po_item.req_qty) AS total'),
	// 		\DB::raw('SUM((data_po_item.harga - (data_po_item.harga * data_po_item.diskon / 100)) * data_po_item.req_qty) AS harga')
	// 	)
	// 	->groupby('data_barang.nm_barang');
	// }

	public function scopeRekapdistributor($query, $req = []){
		$item = $query->join('data_po', 'data_po.id_po', '=', 'data_po_item.id_po')
			->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->join('data_vendor','data_vendor.id_vendor','=','data_po.id_vendor')
			->where('data_po.status', 3);


		if(!empty($req['titipan']))
			$item->where('data_po.titipan', '>', 0);
		else
			$item->where('data_po.titipan', 0);

		if(!empty($req['tipe']))
			$item->where('data_barang.tipe', $req['tipe']);

		if(!empty($req['waktu']) && $req['waktu'] == 1)
			$item->where(\DB::raw('MONTH(data_po.created_at)'), $req['bulan'])
				->where(\DB::raw('YEAR(data_po.created_at)'), $req['tahun']);
		else if(!empty($req['waktu']) && $req['waktu'] == 2)
			$item->whereBetween(\DB::raw('DATE(data_po.created_at)'), [$req['dari'], $req['sampai']]);

		$item->select(
			'data_barang.nm_barang',
			'data_vendor.nm_vendor',
			// 'data_po.titipan',
			\DB::raw('SUM(data_po_item.req_qty) AS total'),
			\DB::raw('SUM((data_po_item.harga - (data_po_item.harga * data_po_item.diskon / 100)) * data_po_item.req_qty) AS harga')
		)
		->groupby('data_po.id_vendor');
	}

	public function scopeRekapprodusesn($query, $req = []){
		$item = $query->join('data_po', 'data_po.id_po', '=', 'data_po_item.id_po')
			->join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
			->join('data_vendor','data_vendor.id_vendor','=','data_po.id_vendor')
			->where('data_po.status', 3);
			
		if(!empty($req['titipan']))
			$item->where('data_po.titipan', '>', 0);
		else
			$item->where('data_po.titipan', 0);

		if(!empty($req['tipe']))
			$item->where('data_barang.tipe', $req['tipe']);

		if(!empty($req['waktu']) && $req['waktu'] == 1)
			$item->where(\DB::raw('MONTH(data_po.created_at)'), $req['bulan'])
				->where(\DB::raw('YEAR(data_po.created_at)'), $req['tahun']);
		else if(!empty($req['waktu']) && $req['waktu'] == 2)
			$item->whereBetween(\DB::raw('DATE(data_po.created_at)'), [$req['dari'], $req['sampai']]);

		$item->select(
			'data_barang.nm_barang',
			'data_vendor.nm_vendor',
			// 'data_po.titipan',
			\DB::raw('SUM(data_po_item.req_qty) AS total'),
			\DB::raw('SUM((data_po_item.harga - (data_po_item.harga * data_po_item.diskon / 100)) * data_po_item.req_qty) AS harga')
		)
		->groupby('data_po.id_vendor');
	}

}
