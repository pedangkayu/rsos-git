@extends('Master.Print')

@section('meta')
	
@endsection

@section('content')
	
	<center><h2><strong>FAKTUR PEMBAYARAN</strong></h2></center>

	<table width="100%">
		<tr>
			<td width="65%" valign="top">
				<address>
					<strong>Terima dari.</strong>
					<h3 style="margin:0;"><strong>{{ $faktur->nm_vendor }}</strong></h3>
					<div><u>KODE #{{ $faktur->kode }}</u></div>
					<div>{{ $faktur->alamat }}</div>
					<div>Telpon: {{ $faktur->telpon }}</div>
					<div>Email: {{ $faktur->email }}</div>
				</address>
				<p><em>"{{ $faktur->keterangan }}"</em></p>
			</td>
			<td width="35%" valign="top">
				<table class="table table-bordered" cellspacing="0" cellpadding="3" width="100%">
					<tr>
						<td width="50%" class="bold">No. Faktur</td>
						<td width="50%" align="right">#{{ $faktur->nomor_faktur }}</td>
					</tr>
					@if($faktur->id_po > 0)
					<tr>
						<td width="50%" class="bold">No. PO</td>
						<td width="50%" align="right">{{ $faktur->nomor_type }}</td>
					</tr>
					@endif
					<tr>
						<td width="50%" class="bold">Status</td>
						<td width="50%" align="right">{{ $status[$faktur->status]['err'] }}</td>
					</tr>
					<tr>
						<td width="50%" class="bold">Tanggal Faktur</td>
						<td width="50%" align="right">{{ Format::indoDate($faktur->tgl_faktur) }}</td>
					</tr>
					<tr>
						<td width="50%" class="bold">Duo Date</td>
						<td width="50%" align="right">{{ Format::indoDate($faktur->duodate) }}</td>
					</tr>
					<tr>
						<td width="50%" class="bold">Payment Terms</td>
						<td width="50%" align="right">{{ $faktur->payment_terms }}</td>
					</tr>
					<tr>
						<td width="50%" class="bold">Amount Due</td>
						<td width="50%" align="right">{{ number_format(($faktur->total - $faktur->amount_due),2,',','.') }}</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<br />

	<table class="table table-bordered" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th width="5%">No.</th>
				<th width="40%">Barang</th>
				<th width="15%">Qty</th>
				<th width="10%">Diskon</th>
				<th width="15%">Harga</th>
				<th width="15%">Total</th>
			</tr>
		</thead>

		<tbody>
			<?php $no = 1; ?>
			@foreach($items as $item)
			<tr>
				<td align="center">{{ $no }}</td>
				<td>{{ $item->deskripsi }}</td>
				<td align="right">{{ $item->qty }} {{ $item->nm_satuan }}</td>
				<td align="right">{{ number_format($item->diskon,0,',','.') }}%</td>
				<td align="right">{{ number_format($item->harga,2,',','.') }}</td>
				<td align="right">{{ number_format($item->total,2,',','.') }}</td>
			</tr>
			<?php $no++; ?>
			@endforeach
			
			<?php 
				/* Matematika */
				$disikon = ($faktur->subtotal * $faktur->diskon) / 100;
				$aftdiskon = $faktur->subtotal - $disikon;
				$ppn = ($aftdiskon * $faktur->ppn) / 100;
			?>

			<tr>
				<td colspan="4" rowspan="5"></td>
				<td align="right" class="bold">Subtotal</td>
				<td align="right">{{ number_format($faktur->subtotal,2,',','.') }}</td>
			</tr>
			<tr>
				<td align="right" class="bold">Diskon {{ number_format($faktur->diskon,1,',','.') }}%</td>
				<td align="right">{{ number_format($disikon,2,',','.') }}</td>
			</tr>
			<tr>
				<td align="right" class="bold">PPN {{ number_format($faktur->ppn,1,',','.') }}%</td>
				<td align="right">{{ number_format($ppn,2,',','.') }}</td>
			</tr>
			<tr>
				<td align="right" class="bold">Adjustment</td>
				<td align="right">{{ number_format($faktur->adjustment,2,',','.') }}</td>
			</tr>
			<tr>
				<td align="right" class="bold">Total</td>
				<td align="right" class="bold">{{ number_format($faktur->total,2,',','.') }}</td>
			</tr>
		</tbody>
	</table>
	
	<h4><strong>Relasi Pembayaran</strong></h4>
	<table class="table table-bordered" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th width="15%">Tanggal</th>
				<th width="30%">Akun</th>
				<th width="35%">Deskripsi</th>
				<th width="20%">Total</th>
			</tr>
		</thead>

		<tbody>

			@forelse($jurnals as $jurnal)
			<tr>
				<td>{{  Format::indoDate2($jurnal->tanggal) }}</td>
				<td>{{ $jurnal->akun }}</td>
				<td>{{ $jurnal->deskripsi }}</td>
				<td class="text-right">{{ number_format($jurnal->total,2,',',',') }}</td>
			</tr>
			@empty
			<tr>
				<td colspan="4">Tidak ditemukan</td>
			</tr>
			@endforelse
		</tbody>
	</table>
@endsection