@extends('Master.Template')

@section('meta')
	
@endsection

@section('title')
	Laporan Stok berdasarkan {{ $req->tipe }}
@endsection

@section('content')
	
	<div class="row">
		<!-- left -->
		<div class="col-sm-12">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<div class="pull-right">
						<form method="get" action="{{ url('/reportlogistik/laporanstokprint') }}" target="_blank">
							<a href="{{ url('/reportlogistik/laporanstok') }}" class="btn btn-primary btn-small">Kembali</a>
							<button class="btn btn-primary btn-small">Print</button>
							@if(($req->tipe == 'klasifikasi'))
								@foreach($req->id_klasifikasi as $id)
									<input type="hidden" name="id_klasifikasi[]" value="{{ $id }}">
								@endforeach
								<input type="hidden" name="tipe" value="klasifikasi">
							@elseif($req->tipe == 'kategori')
								@foreach($req->id_kategori as $id)
									<input type="hidden" name="id_kategori[]" value="{{ $id }}">
								@endforeach
								<input type="hidden" name="tipe" value="kategori">
							@endif
							<input type="hidden" name="kat" value="{{ $req->kat }}" />
							<input type="hidden" name="jenis_barang" value="{{ $req->jenis_barang }}" />
							<input type="hidden" name="klasifikasi" value="{{ $req->klasifikasi }}" />
							<input type="hidden" name="satuan" value="{{ $req->satuan }}" />
						</form>

					</div>
					<h4><span class="total">{{ count($items) }}</span> items <strong>ditemukan</strong></h4>
				</div>
				<div class="grid-body no-border">
					
					<table class="table">
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
								@if(($req->klasi != ''))
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
						</tbody>
					</table>

				</div>
			</div>

		</div>

	</div>

@endsection