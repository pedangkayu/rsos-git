@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/subgudang/stockadj.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('.parent-item-selected').slimscroll();
		});
	</script>
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
			padding-top: 8px;
			display: none;
		}
	</style>
@endsection

@section('title')
	{{ $title }}
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-9">
			
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
											<div class="col-xs-12">
												<select name="limit" class="form-control">
													<option value="10">Limit 10</option>
													<option value="50">Limit 50</option>
													<option value="100">Limit 100</option>
													<option value="500">Limit 500</option>
												</select>
												<input type="hidden" name="jenis" value="1">
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
				</div>
			</div>

			<div class="grid simple hide cart">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<a href="{{ url('/subgudang/createadj') }}" class="btn btn-block btn-primary"><i class="fa fa-plus"></i> Buat Baru</a>
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