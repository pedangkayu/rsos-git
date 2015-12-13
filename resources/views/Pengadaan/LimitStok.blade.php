@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/logistik.js') }}"></script>
	<style type="text/css">
		.items:hover td .tbl-opsi{
			display: block !important;
		}
	</style>
@endsection

@section('title')
	Stok Limit
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-9">
			<div class="grid simple">
				<div class="grid-title no-border">
		          	<h4>{{ number_format($items->total(),0,',','.') }} Barang <span class="semi-bold">ditemukan</span></h4>
		          	<div class="tools">
		          		<a href="javascript:;" class="collapse"></a> 
		          		<a href="{{ url('/logistik/limit') }}" class="reload"></a>
		          	</div>
		        </div>
				<div class="grid-body no-border table-responsive table-hover">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th><a href="javascript:;">Nama Barang</a></th>
								<th><a href="javascript:;">Kode</a></th>
								<th><a href="javascript:;">Kategori</a></th>
								<th><a href="javascript:;">Tipe</a></th>
								<th class="text-right"><a href="javascript:;">Stok Min</a></th>
								<th class="text-right"><a href="javascript:;">Sisa Stok</a></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php $no = $items->currentPage() == 1 ? 1 : ($items->perPage() + $items->currentPage()) -1 ; ?>
							@forelse($items as $item)
								<tr class="item_{{ $item->id_barang }} items">
									<td>{{ $no }}<br />&nbsp;</td>
									<td>
										<a href="javascript:;" title="{{ $item->nm_barang }}" data-toggle="tooltip" data-placement="bottom">{{ Format::substr($item->nm_barang,15) }}</a>
										<div style="display:none;" class="tbl-opsi">
											<small>[
												<a href="#" data-toggle="modal" data-target="#review" onclick="review({{ $item->id_barang }})">Lihat</a>
												| <a href="{{ url('/logistik/detail/' . $item->id_barang) }}">Rinci</a>
												@if(Auth::user()->permission > 2)
													<!-- hanya admin yang memiliki akses Write dan Execute -->
													| <a href="{{ url('/logistik/update/' . $item->id_barang ) }}">Edit</a> 
												@endif
											]</small>
										</div>
									</td>
									<td>{{ $item->kode }}</td>
									<td>{{ $item->nm_kategori }}</td>
									<td>{{ $tipes[$item->tipe] }}</td>
									<td class="text-right">{{ number_format($item->stok_minimal,0,',','.') }} {{ $item->nm_satuan }}</td>
									<td class="text-right">{{ number_format(($item->in - $item->out),0,',','.') }} {{ $item->nm_satuan }}</td>
									<td class="text-right">
										@if(Auth::user()->permission > 2)
										<button type="button" class="close hapus" data-nama="{{ $item->nm_barang }}" data-id="{{ $item->id_barang }}"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										@endif
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

					<div class="text-right nav-pagin">
						{!! $items->appends([
							'src' => $src,
							'kat' => $kat,
							'sort' => $filed,
							'orderby' => $order,
							'limit' => $limit,
							'tipe' => $tipe
						])->render() !!}
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-3">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					@if(Auth::user()->permission > 1)
						<a class="btn btn-block btn-primary" href="{{ url('/logistik/add') }}"><i class="fa fa-plus"></i> Tambah Barang</a>
					@endif
						<a class="btn btn-block btn-default" href="{{ url('/logistik') }}"><span>Kembali ke Master</span></a>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>Cari <span class="semi-bold">Barang</span></h4>
					<div class="tools">
		          		<a href="javascript:;" class="collapse"></a> 
		          	</div>
				</div>
				<div class="grid-body no-border">
					<form method="get">
						<p>
							<div class="input-group transparent">
							  <span class="input-group-addon ">
								<i class="fa fa-search"></i>
							  </span>
							  <input type="text" class="form-control" placeholder="Cari Barang..." value="{{ !empty($src) ? $src : '' }}" name="src">
							</div>
							
							<div class="form-group">
								<label>&nbsp;</label>
								<select id="source" style="width:100%" name="kat">
									<option value="">Pilih Kategori</option>
									@foreach($kategoris as $kategori)
										<option value="{{ $kategori->id_kategori }}" {{ $kategori->id_kategori == $kat ? 'selected' : '' }}>{{ $kategori->nm_kategori }}</option>
									@endforeach
								</select>
							</div>

							<div class="form-group">
								<label></label>
								<select id="source" style="width:100%" name="tipe">
									<option value="0">Pilih Tipe Barang</option>
									<option value="1" {{ $tipe == 1 ? 'selected' : '' }}>Obat</option>
									<option value="2" {{ $tipe == 2 ? 'selected' : '' }}>Barang</option>
								</select>
							</div>

							<div class="form-group">
								<label>Urutkan Berdasarkan</label>
								<select id="source" style="width:100%" name="sort">
									<option value="barang" {{ $filed == 'barang' ? 'selected' : '' }}>Nama Barang</option>
									<option value="kode" {{ $filed == 'kode' ? 'selected' : '' }}>Kode</option>
									<option value="kategori" {{ $filed == 'kategori' ? 'selected' : '' }}>Kategori</option>
									<option value="waktu" {{ $filed == 'waktu' ? 'selected' : '' }}>Waktu</option>
								</select>
							</div>

							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Urutan</label>
										<select id="source" style="width:100%" name="orderby">
											<option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>A - Z</option>
											<option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Z - A</option>
										</select>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Limit</label>
										<select id="source" style="width:100%" name="limit">
											<option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
											<option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
											<option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
											<option value="500" {{ $limit == 500 ? 'selected' : '' }}>500</option>
										</select>
									</div>
								</div>
							</div>
						</p>
						<br />
						<button class="btn btn-block btn-primary" type="submit"><i class="fa fa-search"></i> Cari</button>	
					</form>
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