@extends('Master.Print')

@section('meta')

@endsection

@section('content')
	<center>
		<h2>LAPORAN STOK</h2>
		<strong>Berdasarkan {{ $req->tipe }}</strong>
	</center>

	<table class="table table-bordered" cellspacing="0">
		<thead>
			<tr>
				<th>No.</th>
				<th>Kode</th>
				<th>Nama Barang</th>
				
				@if(($req->kat != ''))
					<th>Kategori</th>
				@endif

				@if(($req->klasifikasi != ''))
					<th>Klasifikasi</th>
				@endif

				@if(($req->jenis_barang != ''))
					<th>Jenis Barang</th>
				@endif

				<th class="text-right">Sisa Stok</th>

				@if(($req->satuan != ''))
					<th>Satuan</th>
				@endif

				<th class="text-right">Harga/item</th>
				<th class="text-right">Total</th>
			</tr>
		</thead>
		<tbody>
			<cfloop index="intRow" from="1" to="100" step="1">
				<?php
					$no = 1;
					$sisa = 0;
					$harga = 0;
					$total = 0;
				?>
				@forelse($items as $item)
				<tr>
					<td>{{ $no }}</td>
					<td>{{ $item->kode }}</td>
					<td>{{ $item->nm_barang }}</td>
					
					@if(($req->kat != ''))
						<td>{{ $item->nm_kategori }}</td>
					@endif

					@if(($req->klasifikasi != ''))
						<td>{{ $item->nm_klasifikasi }}</td>
					@endif

					@if(($req->jenis_barang != ''))
						<td>{{ $tipe[$item->tipe] }}</td>
					@endif

					<td class="text-right">{{ number_format(($item->in - $item->out), 0, ',','.') }}</td>

					@if(($req->satuan != ''))
						<td>{{ $item->nm_satuan }}</td>
					@endif

					<td class="text-right">{{ number_format($item->harga_beli,0,',','.') }}</td>
					<td class="text-right">{{ number_format((($item->in - $item->out) * $item->harga_beli),0,',','.') }}</td>
				</tr>
				<?php
					$no++;
					$sisa += ($item->in - $item->out);
					$harga += $item->harga_beli;
					$total += (($item->in - $item->out) * $item->harga_beli);
				 ?>
				@empty
					<tr>
						<td colspan="10">Tidak ditemukan</td>
					</tr>
				@endforelse
				<tr>
					<td></td>
					<td></td>
					<td class="text-center semi-bold">TOTAL</td>
					@if(($req->kat != ''))
					<td></td>
					@endif
					@if(($req->klasifikasi != ''))
					<td></td>
					@endif
					@if(($req->jenis_barang != ''))
					<td></td>
					@endif

					<td class="semi-bold text-right">{{ number_format($sisa,0,',','.') }}</td>
					@if(($req->satuan != ''))
					<td></td>
					@endif
					<td class="semi-bold text-right">{{ number_format($harga,0,',','.') }}</td>
					<td class="semi-bold text-right">{{ number_format($total,0,',','.') }}</td>
				</tr>
			</cfloop>
		</tbody>
	</table>
@endsection
