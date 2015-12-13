@extends('Master.Print')

@section('content')
<center>
	<h2 style="margin:0;">KARTU STOK {{ $barang->tipe == 1 ? 'OBAT' : 'BARANG' }}</h2>
	<span>Periode
	@if($req->waktu == 1)
		{{ Format::nama_bulan($req->bulan) }} {{ $req->tahun }}
	@else
		{{ Format::indoDate2($req->dari) }} - {{ Format::indoDate2($req->sampai) }}
	@endif</span>
</center>

<div>
	<table>
		<tr>
			<td>Kode {{ $barang->tipe == 1 ? 'Obat' : 'Barang' }}</td>
			<td>:</td>
			<td><b>{{ $barang->kode }}</b></td>
		</tr>
		<tr>
			<td>Nama {{ $barang->tipe == 1 ? 'Obat' : 'Barang' }}</td>
			<td>:</td>
			<td><b>{{ $barang->nm_barang }}</b></td>
		</tr>
		<tr>
			<td>Satuan</td>
			<td>:</td>
			<td><strong>{{ $barang->nm_satuan }}</strong></td>
		</tr>
		<tr>
			<td>Stok awal / Periode</td>
			<td>:</td>
			<td><strong>{{ number_format($lastsisa,0,',','.') }} {{ $barang->nm_satuan }}</strong></td>
		</tr>
	</table>
</div>
<div>
	<table class="table table-bordered" cellspacing="0">
		<thead>
			<tr>
				<th width="5%">No</th>
				<th width="15%">No Bon</th>
				<th width="15%">Tanggal</th>
				<th width="13%">Transaksi</th>
				<th width="22%">Oleh</th>
				<th class="text-right" width="10%">Masuk</th>
				<th class="text-right" width="10%">Keluar</th>
				<th class="text-right" width="10%">Sisa</th>
			</tr>

		</thead>

		<tbody>
			<?php $no = 1; ?>
			@forelse($items as $item)
			<?php $jorok = $item['kondisi'] == 2 ? '&nbsp;&nbsp;&nbsp;&nbsp;' : ''; ?>
			<tr title="{{ $item['parent']->keterangan }}">
				<td>{{ $no }}</td>
				@if($item['tipe'] == 1)
				<td>
					{{ $jorok }} {{ $item['parent']->no_skb }}
				</td>
				<td title="{{ Format::jam($item['parent']->created_at) }}">
					{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
				</td>
				@elseif($item['tipe'] == 2)
				<td>
					{{ $jorok }} {{ $item['parent']->no_spbm }}
				</td>
				<td title="{{ Format::jam($item['parent']->created_at) }}">
					{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
				</td>
				@elseif($item['tipe'] == 3)
				<td>
					{{ $jorok }} {{ $item['parent']->no_penyesuaian_stok }}
				</td>
				<td title="{{ Format::jam($item['parent']->created_at) }}">
					{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
				</td>
				@elseif($item['tipe'] == 4)
				<td>
					{{ $jorok }} {{ $item['parent']->no_retur }}
				</td>
				<td title="{{ Format::jam($item['parent']->created_at) }}">
					{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
				</td>
				@elseif($item['tipe'] == 5)
				<td>
					{{ $jorok }} {{ $item['parent']->no_retur }}
				</td>
				<td title="{{ Format::jam($item['parent']->created_at) }}">
					{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
				</td>
				@endif
				<td>{{ $jenis[$item['tipe']] }}</td>
				<td>{{ $item['oleh'] }}</td>
				<td class="text-right">{{ $item['kondisi'] == 1 ? number_format($item['qty'],0,',','.') : '-' }}</td>
				<td class="text-right">{{ $item['kondisi'] == 2 ? number_format($item['qty'],0,',','.') : '-' }}</td>
				<td class="text-right">{{ number_format($item['sisa'],0,',','.') }}</td>
			</tr>
			<?php $no++; ?>
			@empty
			<tr>
				<td colspan="8">Tidak ditemukan</td>
			</tr>
			@endforelse
		</tbody>
	</table>
</div>
@endsection