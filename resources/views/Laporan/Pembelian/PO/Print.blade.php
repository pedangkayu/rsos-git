@extends('Master.Print')

@section('content')
	<center>
		<h2 style="margin:0;">LAPORAN PURCHASE ORDER</h2>
		<span>Priode
		@if($req->waktu == 1)
			{{ Format::nama_bulan($req->bulan) }} {{ $req->tahun }}
		@else
			{{ Format::indoDate2($req->dari) }} - {{ Format::indoDate2($req->sampai) }}
		@endif</span>
	</center>

	<div>
		<table class="table table-bordered" cellspacing="0">
			<thead>
				<tr>
					<th rowspan="2" class="text-middle">No.</th>
					<th rowspan="2" class="text-middle">No. PRQ</th>
					<th rowspan="2" class="text-middle">No. PO</th>
					<th rowspan="2" class="text-middle">No. GR</th>
					<th rowspan="2" class="text-middle">Supplier</th>
					<th rowspan="2" class="text-middle">Barang</th>
					<th rowspan="2" class="text-middle">Qty</th>
					<th rowspan="2" class="text-middle">Harga/item</th>
					<th colspan="2" class="text-center">PO (%)</th>
					<th colspan="2" class="text-center">Item (%)</th>
					<th rowspan="2" class="text-middle">Total</th>
					<th rowspan="2" class="text-middle">Deadline</th>
				</tr>

				<tr>
					<th>Disk</th>
					<th>PPN</th>
					<th>Disk</th>
					<th>PPN</th>
				</tr>
			</thead>

			<tbody>
				<?php $no = 1; ?>
				@forelse($items as $item)
					<?php
						$diskonitem	= ($item->harga * $item->diskon) / 100;
						$aftdiskon 	= $item->harga - $diskonitem;
						$ppnitem	= ($aftdiskon * $item->ppn) / 100;
						$pphitem	= ($aftdiskon * $item->pph) / 100;
						$totalitem 	= $aftdiskon + $ppnitem + $pphitem;

						$gdiskon 	= ($totalitem * $item->gdiskon) / 100;
						$gaftdisk	= $totalitem - $gdiskon;
						$gppn		= ($gaftdisk * $item->gppn) / 100;
						$gpph		= ($gaftdisk * $item->gpph) / 100;

						$grandtotal = ($gaftdisk + $gppn + $gpph) * $item->qty;
					?>
					<tr>
						<td>{{ $no }}</td>
						<td>{{ $item->no_prq }}</td>
						<td>{{ $item->no_po }}</td>
						<td>{{ $item->no_spbm }}</td>
						<td>{{ $item->nm_vendor }}</td>
						<td>
							{{ $item->nm_barang }}<br />
							<small class="text-muted">{{ $item->kode }}</small>
						</td>
						<td class="text-right">{{ number_format($item->qty,0,',','.') }} {{ $item->nm_satuan }}</td>
						<td class="text-right">{{ number_format($item->harga,0,',','.') }}</td>
						<td class="text-right">{{ number_format($item->gdiskon,0,',',',') }}</td>
						<td class="text-right">{{ number_format($item->gppn,0,',',',') }}</td>
						<td class="text-right">{{ number_format($item->diskon,0,',',',') }}</td>
						<td class="text-right">{{ number_format($item->ppn,0,',',',') }}</td>
						<td class="text-right">{{ number_format($grandtotal,0,',','.') }}</td>
						<td>{{ date('d/m/Y', strtotime($item->deadline)) }}</td>
					</tr>
					<?php $no++; ?>
				@empty
					<tr>
						<td colspan="14">Tidak ditemukan</td>
					</tr>
				@endforelse
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
				<td>...............................</td>
				<td>...............................</td>
				<td>...............................</td>
			</tr>
		</table>
	</div>

@endsection