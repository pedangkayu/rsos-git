@extends('Master.Template')

@section('meta')
	
@endsection

@section('title')
	No. {{ $gr->no_spbm }}
@endsection

@section('content')
	<div class="row">
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="row">
						<div class="col-sm-6">
							<h3>{{ $gr->nm_vendor }}</h3>

							<table style="width:100%;" cellpadding="7">
								<tr>
									<td width="35%">No PO</td>
									<td width="75%">: {{ $gr->no_po }}</td>
								</tr>
								<tr>
									<td>No Surat Jalan</td>
									<td>: {{ $gr->no_surat_jalan }}</td>
								</tr>
								<tr>
									<td>Pengiriman</td>
									<td>: {{ $kirim[$gr->id_kirim] }}</td>
								</tr>

								<tr>
									<td>Oleh</td>
									<td>: {{ $gr->nm_pengirim }}</td>
								</tr>
							</table>
						</div>

						<div class="col-sm-6 text-right">
							<address>
								<strong>Tanggal Terima</strong>
								<p>{{ Format::indoDate($gr->tgl_terima_barang) }}</p>
								<strong>Tanggal Periksa</strong>
								<p>{{ Format::indoDate($gr->tgl_periksa_barang) }}</p>
								<strong>Pemeriksa</strong>
								<p>{{ $gr->pemeriksa1 }}</p>
								<strong>Pengawas</strong>
								<p>{{ $gr->pemeriksa2 }}</p>
							</address>
						</div>
					</div>
					<br />
					<div class="well well-sm">
						{{ empty($gr->keterangan) ? 'Tidak ada keterangan' : $gr->keterangan }}
					</div>

				</div>
			</div>

			<!-- item barang -->
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ count($items) }} ditemukan</h4>
				</div>

				<div class="grid-body no-border">
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Barang</th>
									<th>Qty</th>
									<th>Merek</th>
									<th>Tgl. Exp</th>
									<th>Keterangn</th>
								</tr>
							</thead>

							<tbody>
								@foreach($items as $item)
								<tr>
									<td>
										{{ $item->nm_barang }}<br />
										<small class="text-muted">{{ $item->kode }}</small>
										@if($item->bonus > 0)
										<small class="text-danger">[BONUS]</small>
										@endif
									</td>
									<td>
										{{ number_format($item->qty_lg,0,',','.') }} {{ $item->nm_satuan }}
										<div class="text-muted"><small>{{ number_format($item->qty,0,',','.') }} {{ $item->satuan_default }}</small></div>
									</td>
									<td>{{ $item->merek }}</td>
									<td>{{ Format::indoDate2($item->tgl_exp) }}</td>
									<td>{{ $item->keterangan }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>

				</div>
			</div>

		</div>

		<div class="col-sm-3">
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>Keterangan</h4>
				</div>

				<div class="grid-body no-border">
					<address>
						<strong>Oleh</strong>
						<p>{{ $gr->nm_depan }} {{ $gr->nm_belakang }}</p>
						<strong>Tanggal Buat</strong>
						<p>
							{{ Format::indoDate($gr->created_at) }}<br />
							<small class="text-muted">
								{{ Format::hari($gr->created_at) }}, {{ Format::jam($gr->created_at) }}
							</small>
						</p>
					</address>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<a href="{{ url('/gr/') }}" class="btn btn-primary btn-block">Kembali</a>
					<a target="_blank" href="{{ url('/gr/print/' . $gr->id_spbm) }}" class="btn btn-primary btn-block">Print</a>
				</div>
			</div>
		</div>
	</div>
@endsection