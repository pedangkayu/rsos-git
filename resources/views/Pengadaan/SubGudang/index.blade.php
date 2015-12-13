@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/subgudang/index.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('input').keyup(function(e){
				if(e.keyCode == 13)
					getItems(1);
			});
		});
	</script>
	<style type="text/css">
		.items:hover td .tbl-opsi{
			display: block !important;
		}
	</style>
@endsection

@section('title')
	{{ $title }}
@endsection

@section('content')
	
	<div class="row">
		<!-- left -->
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><span class="total-item">{{ $items->total() }}</span> ditemukan</h4>
				</div>
				<div class="grid-body no-border">
					
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>No</th>
									<th><a href="javascript:void(0);">Nama Barang</a></th>
									<th><a href="javascript:void(0);">Kode</a></th>
									<th><a href="javascript:void(0);">Kategori</a></th>
									<th><a href="javascript:void(0);">Tipe</a></th>
									<th class="text-right"><a href="javascript:;">Sisa Stok</a></th>
								</tr>
							</thead>
							<tbody class="contents-items">
								<?php $no = 1; ?>
								@forelse($items as $item)
									<tr class="item_{{ $item->id_barang }} items">
										<td>{{ $no }}</td>
										<td>
											<a href="javascript:;" title="{{ $item->nm_barang }}" data-toggle="tooltip" data-placement="bottom">{{ Format::substr($item->nm_barang,15) }}</a>
											<div style="display:none;" class="tbl-opsi">
												<small>[
													<a href="#" data-toggle="modal" data-target="#review" onclick="review({{ $item->id_barang }})">Lihat</a>
												]</small>
											</div>
										</td>
										<td>
											{{ $item->kode }}<br />
											<small class="text-muted">{{ Format::hari($item->created_at) }}, {{ Format::indoDate($item->created_at) }}</small>
										</td>
										<td title="{{ $item->nm_kategori }}">{{ Format::substr($item->nm_kategori,20) }}</td>
										<td>{{ $tipes[$item->tipe] }}</td>
										<td class="text-right">
											{{ number_format(($item->in - $item->out),0,',','.') }} {{ $item->nm_satuan }}
											<div>{!! $item->stok_minimal >= ( $item->in - $item->out ) ? '<small class="text-danger semi-bold">(Stok Limit)</small>' : '' !!}</div>
										</td>
									</tr>
									<?php $no++; ?>
								@empty
									<tr>
										<td colspan="7">Tidak ditemukan</td>
									</tr>
								@endforelse
							</tbody>
						</table>

						<div class="text-right pagins">
							{!! $items->render() !!}
						</div>
					</div>

				</div>
			</div>

		</div>

		<!-- right -->
		<div class="col-sm-3">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">

					<div class="form-group">
						<a href="{{ url('/subgudang/adjustment') }}" class="btn btn-block btn-primary">Penyesuaian Stok</a>
					</div>
					
					@if(Auth::user()->permission > 2)
					<div class="form-group">
						<a href="{{ url('/subgudang/access') }}" class="btn btn-block btn-primary"><i class="fa fa-unlock-alt"></i> Hak Akses</a>
					</div>
					<p>
						<i>Anda memiliki akses untuk memberikan <b>Hak Akses</b> kepada pengguna <b>Modul Sub Gudang</b>.</i>
					</p>
					@endif

				</div>
			</div>
			
			@if($me->access || \Auth::user()->permission > 2)
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>Cari <span class="semi-bold">Barang</span></h4>
					<div class="tools">
		          		<a href="javascript:;" class="collapse"></a> 
		          	</div>
				</div>
				<div class="grid-body no-border">
						<p>
							@if($me->id_gudang == 0 && \Auth::user()->permission > 2)
							<div class="form-group">
								<label>Gudang</label>
								<select id="source" style="width:100%" name="gudang">
									<option value="0">Semua</option>
									@foreach($gudangs as $gudang)
									<option value="{{ $gudang->id_gudang }}">{{ $gudang->nm_gudang }}</option>
									@endforeach
								</select>
							</div>
							@endif
						
							<div class="form-group">
								<div class="input-group transparent">
								  <span class="input-group-addon ">
									<i class="fa fa-search"></i>
								  </span>
								  <input type="text" class="form-control" placeholder="Cari Barang..." name="src">
								</div>
							</div>

							<div class="form-group">
								<label>Kode Barang</label>
							  <input type="text" class="form-control" name="kode">
							</div>
							
							<div class="form-group">
								<div class="checkbox check-success">
									<input type="checkbox" id="limit-stok" name="limit_stok">
									<label for="limit-stok"><b>Lihat Stok Habis &nbsp;&nbsp;</b> <span class="badge habis"></span></label>
								</div>
							</div>

							<div class="form-group">
								<select id="source" style="width:100%" name="kat">
									<option value="">Semua Kategori</option>
									@foreach($kategoris as $kategori)
										<option value="{{ $kategori->id_kategori }}" >{{ $kategori->nm_kategori }}</option>
									@endforeach
								</select>
							</div>

							<div class="form-group">
								<label></label>
								<select id="source" style="width:100%" name="tipe">
									<option value="0">Semua Tipe Barang</option>
									<option value="1">Obat</option>
									<option value="2">Barang</option>
								</select>
							</div>
							

							<div class="form-group">
								<label>Limit / Page</label>
								<select id="source" style="width:100%" name="limit">
									<option value="5">5</option>
									<option value="10" selected="selected">10</option>
									<option value="50">50</option>
									<option value="100">100</option>
									<option value="500">500</option>
								</select>
							</div>

						</p>
						<br />
						<button class="btn btn-block btn-primary cari-barang" type="button"><i class="fa fa-search"></i> Cari</button>
				</div>
			</div>
			@endif

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