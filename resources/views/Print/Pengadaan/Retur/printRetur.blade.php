@extends('Master.Print')
@section('meta')
	<style type="text/css">
		.panel{
			border:solid 1px #000;
			margin-bottom: 10px;
		}
		.panel-body{
			padding: 8px;
		}
		h3{
			font-weight: normal;
			margin: 0;
		}
		hr.dotted{
			border: dashed 1px #000;
		}
		table.detail{
			width: 100%;
		}
		table.detail tr td{
			border-right: dashed 1px #000;
			border-bottom: dashed 1px #000;
			padding: 5px;
		}
		table.detail tr:last-child td{
			border-bottom: none;
		}
		table.detail tr td:last-child{
			border-right: none;
		}

		table.detail2{
			width: 100%;
		}
		table.detail2 tr td{
			border-bottom: dashed 1px #000;
			padding: 5px;
		}
		table.detail2 tr:last-child td{
			border-bottom: none;
		}

		.coret{
			text-decoration:line-through;
			color: red;
		}
	</style>
@endsection
@section('content')
<section>
		<table>
			<tr valign="top">
				<td><strong>No. Retur</strong></td>
				<td>: {{ $retur->no_retur }}</td>
			</tr>

			<tr valign="top">
				<td><strong>Gudang</strong></td>
				<td>: {{ $retur->nm_gudang }}</td>
			</tr>

			<tr valign="top">
				<td><strong>Tanggal</strong></td>
				<td>: {{ Format::indoDate2($retur->created_at) }}</td>
			</tr>
		</table>
	</section>

	<div>
		<table class="table table-bordered" cellspacing="0">
			<thead>
				<tr>
					<th width="15%">KODE</th>
					<th width="30%">BARANG</th>
					<th width="10%" class="text-right">QTY</th>
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
						<td class="text-right">{{ number_format($item->qty,0,',','.') }} {{ $item->nm_satuan }}</td>
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
				<td>Dibuat oleh,</td>
				<td>Mengetahui,</td>
				<td>Menerima,</td>
			</tr>
			<tr>
				<td colspan="2"><br/><br/></td>
			</tr>
			<tr>
				<td></td>
				<td>...............................</td>
				<td></td>
			</tr>
			<tr>
				<td>(Staff Unit)</td>
				<td>(Kepala Unit)</td>
				<td>(Logistik)</td>
			</tr>
		</table>
	</div>
@endsection