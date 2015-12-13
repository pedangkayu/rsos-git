@extends('Master.Template')

@section('csstop')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection
@section('meta')
	<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$(function(){
			
			getusers = function(){
				$("#source").html('<option value="">Loading...</option>');
				$.getJSON(_base_url + '/subgudang/accessusers', {}, function(json){
				 	$("#source").html(json.content);
				 	$("#source").select2();
				});
			}

			edit = function(user, gudang){
				$.post(_base_url + '/subgudang/editaksesuser', {
					gudang : gudang,
					id : user
				}, function(json){
					onDataCancel();
				}, 'json');
			}

			del = function(id){
				$('.user-' + id).css('opacity', .3);
				$.post(_base_url + '/subgudang/delaccesgudang', {
					id : id
				}, function(json){
					$('.user-' + json.id).remove();
					getusers();
				}, 'json');
			}


			allusergudang = function(page){
		
				var $nama 	= $('[name="nama"]').val();
				var $gd 	= $('[name="gd"]').val();
				var $limit 	= $('[name="limit"]').val();
				
				$('.items-users').css('opacity', .3);
				$('body').css('cursor', 'wait');

				$.ajax({
					type 	: 'GET',
					url 	: _base_url + '/subgudang/getusergudang',
					data 	: {
						page 	: page,
						nama 	: $nama,
						gd 		: $gd,
						limit 	: $limit
					},
					cache 	: false,
					dataType : 'json',
					success : function(res){
						$('.items-users').html(res.content);
						$('.pagin').html(res.pagin);
						$('.items-users').css('opacity', 1);
						$('body').css('cursor', 'default');
						onDataCancel();
						
						$('div.pagin > ul.pagination > li > a').click(function(e){
							e.preventDefault();
							var $link 	= $(this).attr('href');
							var $split 	= $link.split('?page=');
							var $page 	= $split[1];
							allusergudang($page);
						});
					}
				});

			}

			$('div.pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allusergudang($page);
			});

			$('.cari').click(function(){
				allusergudang(1);
			});

			getusers();

		});
	</script>

	<style type="text/css">
		td > .link{
			display: none;
		}
		table.daftar-user tr:hover td .link{
			display: block;
		}
	</style>
@endsection

@section('title')
	Akses Users Gudang
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-8">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ $users->total() }} ditemukan</h4>
				</div>
				<div class="grid-body no-border">
					
					<div class="table-responsive">
						<table class="table table-striped daftar-user">
							<thead>
								<tr>
									<th>No</th>
									<th>Nama</th>
									<th>Akses Gudang</th>
									<th>Tanggal</th>
								</tr>
							</thead>

							<tbody class="items-users">
								<?php $no = 1; ?>
								@forelse($users as $user)
									<tr class="user-{{ $user->id_gudang_user }}">
										<td>{{ $no }}</td>
										<td>
											{{ $user->name }}
											<div class="link">
												<small>[
													<a href="javascript:;" onclick="del({{ $user->id_gudang_user }});" class="text-danger">Hapus</a>
												]</small>
											</div>
										</td>
										<td>
											<select style="width:100%;" onchange="edit({{ $user->id_gudang_user }}, this.value);" >
												@foreach($gudangs as $gudang)
												<option value="{{ $gudang->id_gudang }}" {{ $gudang->id_gudang == $user->id_gudang ? 'selected="selected"' : '' }}>{{ $gudang->nm_gudang }}</option>
												@endforeach
											</select>
										</td>
										<td>
											{{ Format::indoDate($user->created_at) }}
											<div><small class="text-muted">{{ Format::hari($user->created_at) }}, {{ Format::jam($user->created_at) }}</small></div>
										</td>
									</tr>
									<?php  $no++; ?>
									@empty
									<tr>
										<td colspan="4">Tidak ditemukan</td>
									</tr>
								@endforelse
							</tbody>
						</table>

						<div class="text-right pagin">
							{!! $users->render() !!}
						</div>
					</div>

				</div>
			</div>

		</div>
		<div class="col-sm-4">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>Tambah Kases <b>User</b></h4>
				</div>
				<div class="grid-body no-border">

					<form method="post" action="">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="form-group">
							<select id="source" style="width:100%" required name="user">
			                    <option value="">Loading...</option>
		                  	</select>
						</div>

	                  	<div class="form-group">
	                  		<select style="width:100%;" name="gudang" required>
								@foreach($gudangs as $gudang)
								<option value="{{ $gudang->id_gudang }}">{{ $gudang->nm_gudang }}</option>
								@endforeach
							</select>
	                  	</div>

	                  	<div class="form-group">
	                  		<button type="submit" class="btn btn-block btn-primary">Simpan</button>
	                  	
	                  		<a href="{{ url('/subgudang') }}" class="btn btn-block btn-primary">Kembali</a>
	                  	</div>


                  	</form>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">

					<div class="form-group">
						<label>Nama</label>
						<input type="text" name="nama" class="form-control">
					</div>

					<div class="form-group">
                  		<select style="width:100%;" name="gd">
                  			<option value="0">Semua</option>
							@foreach($gudangs as $gudang)
							<option value="{{ $gudang->id_gudang }}">{{ $gudang->nm_gudang }}</option>
							@endforeach
						</select>
                  	</div>

					<div class="form-group">
						<label>Limit / Page</label>
						<select name="limit" class="form-control">
							<option value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="500">500</option>
						</select>
					</div>

					<div class="form-group">
						<butto class="btn btn-block btn-primary cari"><i class="fa fa-search"></i> Cari</button>
					</div>

				</div>
			</div>

		</div>
	</div>

@endsection