@extends('Master.Template')

@section('csstop')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection
@section('meta')
	<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/js/modpembelian/spbm/index.js') }}"></script>
@endsection

@section('title')
	Good Receive
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ $items->total() }} ditemukan</h4>
				</div>
				<div class="grid-body no-border">
					
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>No.</th>
									<th>No. GR</th>
									<th>Tanggal Terima</th>
									<th>Supplier</th>
									<th></th>
								</tr>
							</thead>

							<tbody class="content-gr">
								<?php $no = 1; ?>
								@forelse($items as $item)
								<tr>
									<td>{{ $no }}</td>
									<td>
										<a href="{{ url('/gr/detail/' . $item->id_spbm) }}">{{ $item->no_spbm }}</a>
										<div class="text-muted">
											<small>PO No. {{ $item->no_po }}</small>
										</div>
									</td>
									<td>
										{{ Format::indoDate($item->tgl_terima_barang) }}
										<div class="text-muted">
											<small>Periksa {{ Format::indoDate($item->tgl_periksa_barang) }}</small>
										</div>
									</td>
									<td>
										{{ $item->nm_vendor }}
										<div class="text-muted">
											<small>oleh {{ $item->nm_pengirim }}</small>
										</div>
									</td>
									<td>
										<a target="_blank" href="{{ url('/gr/print/' . $item->id_spbm) }}" class="btn btn-white"><i class="fa fa-print"></i></a>
									</td>
								</tr>
								<?php $no++; ?>
								@empty
									<tr>
										<td colspan="5">Tidak ditemukan</td>
									</tr>
								@endforelse
							</tbody>
						</table>

						<div class="text-right pagin">
							{!! $items->render() !!}
						</div>
					</div>

				</div>
			</div>

		</div>

		<div class="col-sm-3">
			@if(Auth::user()->permission > 2)
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<a class="btn btn-block btn-primary" href="{{ url('/gr/po') }}">Dari Daftar PO</a>
				</div>
			</div>
			@endif

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="form-group">
						<label>No. Good Receive</label>
						<input type="text" name="no_spbm" class="form-control">
					</div>

					<div class="form-group">
						<label>No. PO</label>
						<input type="text" name="no_po" class="form-control">
					</div>

					<div class="form-group">
						<label>No. Surat Jalan</label>
						<input type="text" name="no_surat_jalan" class="form-control">
					</div>

					<div class="form-group">
						<label for="penyedia">Penyedia</label>
						<select style="width:100%;" name="id_vendor" id="penyedia" required>
							<option value="">Loading...</option>
						</select>
					</div>

					<div class="form-group">
						<div class="checkbox check-info">
							<input type="checkbox" name="titipan" id="titipan">
							<label for="titipan">Barang Titipan</label>
						</div>
					</div>
					
					<div class="form-group">
						<label for="tgl_terima_barang">Tanggal Buat</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="tgl_terima_barang" id="tgl_terima_barang" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-tgl_terima_barang" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="id_kirim">Status</label>
						<select name="id_kirim" class="form-control">
							<option value="0">Semua</option>
							<option value="1">Dikirim Supplier</option>
							<option value="2">Dikirim Ekspedisi</option>
							<option value="3">Diambil Onkologi</option>
						</select>
					</div>

					<div class="form-group">
						<label for="limit">Limit / Page</label>
						<select name="limit" class="form-control">
							<option value="5">5</option>
							<option value="10" selected="selected">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
					</div>

					<div class="form-group">
						<button class="cari btn btn-block btn-primary"><i class="fa fa-search"></i> Cari</button>
					</div>

				</div>
			</div>

		</div>
	</div>

@endsection