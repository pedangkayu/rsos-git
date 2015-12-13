@extends('Master.Print')

@section('meta')
	<style type="text/css">
		h3{
			font-weight: normal;
			margin: 0;
		}
	</style>
@endsection

@section('content')
	<div>
		<table width="100%">
			<tr>
				<td width="50%" valign="top">
					<h3>{{ $gr->nm_vendor }}</h3>
					<em>
						<div>{{ $gr->alamat }}</div>
						<div>{{ $gr->telpon }}</div>
					</em>
				</td>
				<td width="50%">
					<table>
						<tr>
							<td><strong>No. GR</strong></td>
							<td>: {{ $gr->no_spbm }}</td>
						</tr>
						<tr>
							<td><strong>No. PO</strong></td>
							<td>: {{ $gr->no_po }}</td>
						</tr>
						<tr>
							<td><strong>No. Invoice</strong></td>
							<td>: {{ $gr->no_surat_jalan }}</td>
						</tr>
						<tr>
							<td><strong>Pengiriman</strong></td>
							<td>: {{ $kirim[$gr->id_kirim] }}</td>
						</tr>
					</table>

				</td>
			</tr>
		</table>

		
	</div>

	<center>
		{!! $gr->titipan > 0 ? '<strong>[ Barang titipan ]</strong>' : '' !!}
	</center>

	<div>
		<table class="table table-bordered" cellspacing="0">
			<thead>
				<tr>
					<th width="15%">KODE</th>
					<th width="30%">BARANG</th>
					<th width="10%" class="text-right">QTY</th>
					<th width="10%">TGL. EXP</th>
					<th width="15%">MEREK</th>
					<th width="20%">KETERANGAN</th>
				</tr>

			</thead>
			<tbody>
				<?php $no = 1; ?>
				@foreach($items as $item)
					<tr>
						<td>{{ $item->kode }}</td>
						<td>{{ $item->nm_barang }}</td>
						<td class="text-right">{{ number_format($item->qty_lg,0,',','.') }} {{ $item->nm_satuan }}</td>
						<td>{{ date('d/m/Y', strtotime($item->tgl_exp)) }}</td>
						<td>{{ $item->merek }}</td>
						<td>{{ $item->keterangan }}</td>
					</tr>
					<?php $no++; ?>
				@endforeach
			</tbody>
		</table>
	</div>

	<div>
		<table class="ttd">
			<tr>
				<td>Pengirim,</td>
				<td>Menerima,</td>
				<td>Dibuat oleh,</td>
				<td>Mengetahui,</td>
			</tr>
			<tr>
				<td colspan="3"><br/><br/></td>
			</tr>
			<tr>
				<td><strong>{{ $gr->nm_pengirim }}</strong></td>
				<td>(...............................)</td>
				<td>(...............................)</td>
				<td>(...............................)</td>
			</tr>
			<tr>
				<td>(Supplier)</td>
				<td>(Logistik)</td>
				<td>(Staff Unit)</td>
				<td>(Kepala Unit)</td>
			</tr>
		</table>
	</div>
@endsection