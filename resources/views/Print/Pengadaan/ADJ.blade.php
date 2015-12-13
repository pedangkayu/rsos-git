@extends('Master.Print')

@section('content')
	
	<div>
		<table>
			<tr>
				<td><strong>No. Form</strong></td>
				<td>: {{ $adj->no_penyesuaian_stok }} / {{ $adj->tipe == 1 ? 'Obat' : 'Barang' }}</td>
			</tr>
			<tr>
				<td><strong>Tanggal</strong></td>
				<td>: {{ Format::indoDate($adj->created_at) }}</td>
			</tr>
			<tr>
				<td><strong>Oleh</strong></td>
				<td>: {{ $adj->nm_depan }} {{ $adj->nm_belakang }}</td>
			</tr>
			@if($adj->id_gudang > 0)
			<tr>
				<td><strong>Gudang</strong></td>
				<td>: {{ $gudang->nm_gudang }}</td>
			</tr>
			@endif
			<tr>
				<td><strong>Keterangan</strong></td>
				<td>: {{ $adj->keterangan }}</td>
			</tr>
		</table>
	</div>
	<br />
	<div>
		<table class="table table-bordered" cellspacing="0">
			<thead>
				<tr>
					<th width="5%">No</th>
					<th width="15%">Kode Barang</th>
					<th width="20%">Nama Barang</th>
					<th width="10%">Current Qty</th>
					<th width="10%">New Qty</th>
					<th width="10%">Selisih</th>
					<th width="20%">Ketarngan</th>
				</tr>
				
			</thead>
			<tbody>
				<?php $no = 1; ?>
				@foreach($items as $item)
					<?php $operator = $item->current_qty < $item->new_qty ? '+' : '-'; ?>
					<tr>
						<td class="text-center">{{ $no }}</td>
						<td>{{ $item->kode }}</td>
						<td>{{ $item->nm_barang }}</td>
						<td class="text-right">{{ number_format($item->current_qty,0,',','.') }} {{ $item->satuan_default }}</td>
						<td class="text-right">{{ number_format($item->new_qty,0,',','.') }} {{ $item->satuan_default }}</td>
						<td class="text-right">{{ $operator }}{{ number_format(abs($item->current_qty - $item->new_qty),0,',','.') }} {{ $item->satuan_default }}</td>
						<td>{{ $item->keterangan }}</td>
					</tr>
					<?php $no++; ?>
				@endforeach
			</tbody>
		</table>
	</div>

@endsection