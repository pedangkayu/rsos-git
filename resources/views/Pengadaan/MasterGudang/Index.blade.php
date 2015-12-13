@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){

			allgudang = function(page){

				var $kode 		= $('[name="kode"]').val();
				var $gudang 	= $('[name="gudang"]').val();
				var $limit 		= $('[name="limit"]').val();
				
				

				$('.content-gudang').css('opacity', .3);

				$.getJSON(_base_url + '/gudang/allgudang', {

					page	: page,
					kode 	: $kode,
					gudang 	: $gudang,
					limit 	: $limit

				}, function(json){
					
					$('.content-gudang').html(json.content);
					$('.pagin').html(json.pagin);

					$('.content-gudang').css('opacity', 1);
					onDataCancel();

					$('div.pagin > ul.pagination > li > a').click(function(e){
						e.preventDefault();
						var $link 	= $(this).attr('href');
						var $split 	= $link.split('?page=');
						var $page 	= $split[1];
						allgudang($page);
					});

				});

			}

			$('div.pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allgudang($page);
			});

			$('.cari').click(function(){
				allgudang(1);
			});

			/* -----------------------------------------  */

			del = function(id){
				swal({   
					title: "PERINGATAN!",   
					text: "Anda yakin ingin menghapusnya ?",   
					type: "warning",   
					showCancelButton: true,   
					confirmButtonColor: "#DD6B55",   
					confirmButtonText: "Yes, delete it!"
				}, function(){
					$('.item-' + id).css('opacity', .3);					
					$.post(_base_url + '/gudang/del', {
						id : id
					}, function(json){

						$('.item-' + json.id).remove();

					}, 'json');

				});
			}
		});
	</script>
@endsection

@section('title')
	Master Gudang
@endsection

@section('content')
	
	<div class="row">
		<!-- left -->
		<div class="col-sm-7">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ $items->total() }} gudang <strong>ditemukan</strong></h4>
				</div>
				<div class="grid-body no-border">
					
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Kode</th>
								<th>Nama Gudang</th>
								<th>Tanggal</th>
							</tr>
						</thead>
						<tbody class="content-gudang">
							<?php $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1; ?>
							@forelse($items as $item)
								<tr class="item-{{ $item->id_gudang }}">
									<td>{{ $no }}</td>
									<td>
										{{ $item->kode_gudang }}<br />
										@if(Auth::user()->permission > 2)
										<small>[
											<a href="{{ url('/gudang/edit/' . $item->id_gudang) }}">Rubah</a> |
											<a href="javascript:void(0);" onclick="del({{ $item->id_gudang }});" class="text-danger">Hapus</a> 
										]</small>
										@endif
									</td>
									<td>
										{{ $item->nm_gudang }}
									</td>
									<td>
										{{ Format::indoDate($item->created_at) }}<br />
										<small class="text-muted">{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small>
									</td>
									<?php $no++; ?>
								</tr>
							@empty
								<tr>
									<td colspan="4">Tidak ditemukan</td>
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

		<!-- right -->
		<div class="col-sm-5">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>Tambah <strong>Gudang</strong></h4>
				</div>
				<div class="grid-body no-border">
					@if($gudang == null)
						<form method="post" action="">
					@else
						<form method="post" action="{{ url('/gudang/edit') }}">
						<input type="hidden" name="id_gudang" value="{{ $gudang->id_gudang }}">
					@endif
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="status" value="1">
						<div class="form-group">
							<label for="kode_gudang">Kode Gudang</label>
							<input type="text" name="kode_gudang" value="{{ old('kode_gudang') ? old('kode_gudang') : !empty($gudang->kode_gudang) ? $gudang->kode_gudang : '' }}" required class="form-control" {{ !empty($gudang->kode_gudang) ? 'readonly="readonly"' : '' }}>
						</div>

						<div class="form-group">
							<label for="nm_gudang">Nama Gudang</label>
							<input type="text" name="nm_gudang" value="{{ old('nm_gudang') ? old('nm_gudang') : !empty($gudang->nm_gudang) ? $gudang->nm_gudang : '' }}" required class="form-control">
						</div>

						<div class="form-group">
							<button class="btn btn-primary" type="submit">Simpan</button>
						</div>

					</form>

				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>Pencarian</h4>
				</div>
				<div class="grid-body no-border">
					<div class="form-group">
						<label for="kode">Kode</label>
						<input type="text" name="kode" id="kode" class="form-control">
					</div>

					<div class="form-group">
						<label for="gudang">Nama Gudang</label>
						<input type="text" name="gudang" id="gudang" class="form-control">
					</div>

					<div class="form-group">
						<label for="limit">Limit / Page</label>
						<select id="limit" style="width:100%" name="limit">
							<option value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="500">500</option>
						</select>
					</div>
					
					<div class="form-group">
						<button class="btn btn-primary btn-block cari" type="button">Cari</button>
					</div>
				</div>
			</div>

		</div>
		
	</div>

@endsection