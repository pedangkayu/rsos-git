@extends('Master.Template')

@section('meta')
	@if($tipe == 1)
	<script type="text/javascript" src="{{ asset('/js/pengadaan/pmo.js') }}"></script>
	@else
	<script type="text/javascript" src="{{ asset('/js/pengadaan/pmb.js') }}"></script>
	@endif
	<script type="text/javascript" src="{{ asset('/js/pengadaan/spb.js') }}"></script>
	<style type="text/css">
		.oneitem a{
			color :#fff;
		}
		.oneitem{
			position: absolute;
			right: 0;
			top: 0;
			bottom: 0;
			background: #ff0000;
			width: 50px;
			padding-top: 20px;
			display: none;
		}
	</style>
@endsection

@section('title')
	Stok Gudang
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-9">
			<input type="hidden" name="tipegudang" value="{{ $tipe }}">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ number_format($items->total(),0,',','.') }} <span class="semi-bold">barang ditemukan</span></h4>
					<div class="tools">
		          		<a href="javascript:getItems(1);" class="reload" data-toggle="tooltip" data-placement="bottom" title="Refresh"></a> 
		          	</div>
				</div>
				<div class="grid-body no-border">
					<div class="table-responsive">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<td width="20%">
										<input type="text" name="kode" placeholder="Kode" class="form-control">
									</td>
									<td width="55%">
										<input type="text" name="nm_barang" placeholder="Nama Obat / Barang" class="form-control">
									</td>
									<td width="30%" colspan="2">
										<button class="btn btn-block btn-primary Searching" title="Advance Searching"><i class="fa fa-search"></i> Cari</button>
									</td>
								</tr>
								<tr class="advance-src">
									<td colspan="4">
										<div class="row">
											<!-- <div class="col-xs-6">
												<select name="id_kategori" style="width:100%;">
													<option value="0">Semua Kategori</option>
												@foreach($kats as $kat)
													<option value="{{ $kat->id_kategori }}">{{ $kat->nm_kategori }}</option>
												@endforeach
												</select>
											</div> -->
											
											<div class="col-xs-12">
												<select name="limit" style="width:100%;">
													<option value="10">Limit 10</option>
													<option value="50">Limit 50</option>
													<option value="100">Limit 100</option>
													<option value="500">Limit 500</option>
												</select>
											</div>
										</div>
									</td>
								</tr>
							</thead>
							<tbody class="content-barang">
								
							@forelse($items as $item)
								<tr class="item_{{ $item->id_barang }}">
									<td width="20%">
										<a href="#" data-toggle="modal" data-target="#review" onclick="review({{ $item->id_barang }})">{{ $item->kode }}</a>
									</td>
									<td width="55%" colspan="2">{{ $item->nm_barang }}</td>
									<!-- <td width="15%" class="text-right">{{ number_format(($item->in - $item->out),0,',','.') }} {{ $item->nm_satuan }}</td> -->
									<td width="15%">
										<button onclick="add({{ $item->id_barang }});" class="btn btn-white btn-block btn-xs btn-mini" title="Tambahkan"><i class="fa fa-plus"></i></button>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="4"><div class="well">Tidak ditemukan</div></td>
								</tr>
							@endforelse

							</tbody>
						</table>
					</div>

					<div class="pagins text-right">
						{!! $items->render() !!}
					</div>
				</div>
			</div>

		</div>
		<div class="col-sm-3">
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>&nbsp;</h4>
					<div class="tools">
		          		<a href="javascript:;" class="collapse"></a> 
		          	</div>
				</div>
				<div class="grid-body no-border">
					<address>
						<strong>Oleh</strong>
						<p>{{ Me::fullname() }}</p>
						<strong>Tanggal</strong>
						<p>{{ Format::indoDate(date('Y-m-d')) }}</p>
						<strong>Departemen</strong>
						<p>{{ Me::departemen() }}</p>
					</address>

					<div class="form-group">
						<a href="{{ url('/pmbumum') }}" class="btn btn-primary btn-block">Kembali</a>
					</div>
				</div>
			</div>

			<div class="grid simple hide cart">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<a href="{{ url('/pmbumum/create/' . $tipe) }}" class="btn btn-block btn-primary"><i class="fa fa-shopping-cart"></i> Buat Baru</a>
					<a data-toggle="tooltip" data-placement="bottom" title="Menambahkan item yang sudah terpilih ke dalam permohonan yang sudah dibuat sebelumnya" href="{{ url('/pmbumum/additemspb/' . $tipe) }}" class="btn btn-block btn-primary"><i class="fa fa-plus"></i> Penambahan</a>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>
						<span class="total">0</span> <span class="semi-bold">Terpilih</span>
					</h4>
					<button type="button" class="btn btn-white dellAll pull-right btn-mini btn-xs" type="button" title="Hapus Semua Pilihan" data-toggle="tooltip" data-placement="left" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>"><i class="fa fa-trash"></i></button>
				</div>
				<div class="grid-body no-border">
					<div class="parent-item-selected">
						<table class="table table-bordered">
							<tbody class="item-selected">
								<tr>
									<td>Tidak ada</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('footer')
	
	<!-- Modal -->
	<div class="modal fade" id="review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	        <h4 class="modal-title" id="myModalLabel"><span class="semi-bold">Kode </span> <span class="review-kode"></span></h4>
	      </div>
	      <div class="modal-body review-detail">
	        <i class="fa fa-circle-o-notch fa-spin"></i> Memuat...
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Keluar</button>
	        <span class="link"></span>
	      </div>
	    </div>
	  </div>
	</div>

@endsection