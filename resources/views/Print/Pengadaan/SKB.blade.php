@extends('Master.Print')

@section('content')
	<div>
		<table>
			<tr>
				<td><strong>No. Permintaan</strong></td>
				<td>: {{ $spb->no_spb }}</td>
			</tr>
			<tr>
				<td><strong>No. Keluar Barang</strong></td>
				<td>: {{ $spb->no_skb }}</td>
			</tr>
			<tr>
				<td><strong>Tanggal</strong></td>
				<td>: {{ Format::indoDate($spb->created_at) }}</td>
			</tr>
			<tr>
				<td><strong>Bagian</strong></td>
				<td>: {{ $spb->nm_departemen }}</td>
			</tr>
		</table>
	</div>

	<div>
		<table class="table table-bordered" cellspacing="0">
			<thead>
				<tr>
					<th width="5%" rowspan="2">No</th>
					<th width="12%" rowspan="2">Kode Barang</th>
					<th width="30%" rowspan="2">Nama Barang</th>
					<th width="8%" rowspan="2">Jumlah</th>
					<th colspan="2">Penyerahan</th>
					<th colspan="2">Penerimaan</th>
					<th width="5%" rowspan="2">Sisa<br/>Hutang</th>
					<th width="20%" rowspan="2">Keterangan</th>
				</tr>
				
				<tr>
					<th width="5%">Jumlah</th>
					<th width="5%">Check</th>
					<th width="5%">Jumlah</th>
					<th width="5%">Check</th>
				</tr>

			</thead>
			<tbody>
				<?php $no = 1; ?>
				@foreach($items as $item)
					<tr>
						<td class="text-center">{{ $no }}</td>
						<td>{{ $item->kode }}</td>
						<td>{{ $item->nm_barang }}</td>
						<td class="text-right">{{ $item->qty + $item->sisa }} {{ $item->nm_satuan }}</td>

						<td class="text-right">{{ number_format($item->qty) }} {{ $item->nm_satuan }}</td>
						<td></td>
						<td></td>
						<td></td>

						<td class="text-right">{{ number_format(($item->sisa),0,',','.') }} {{ $item->nm_satuan }}</td>
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
				<td>{{ $spb->nm_depan }} {{ $spb->nm_belakang }}</td>
				<td>...............................</td>
				<td>{{ $spb->petugas_depan }} {{ $spb->petugas_belakang }}</td>
			</tr>
			<tr>
				<td>(Staff Unit)</td>
				<td>(Kepala Unit)</td>
				<td>(Logistik)</td>
			</tr>
		</table>
	</div>
@endsection