@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){
			$('.hapus').click(function(){
				var id = $(this).data('id');
				var c = confirm('Anda yakin ingin menghapus user ini ?');
				if(c == true){
					$('.item_' + id).css('opacity', .3);
					$.ajax({
						type : 'POST',
						data : {id : id},
						url : _base_url + '/users/deluser',
						cache : false,
						dataType : 'json',
						success : function(res){
							if(res.result == true){
								$('.item_' + id).fadeIn('slow', function(){
									$(this).remove();
								});
							}
						}
					});
				}
			});
		});
	</script>
@endsection

@section('title')
	Daftar Pengguna
@endsection

@section('content')
	<div class="grid simple">
		<div class="grid-title no-border">
			<h3>{{ $users->total() }} hasil <span class="semi-bold">ditemukan</span></h3>
		</div>
		<div class="grid-body no-border">
			<p>
				@if(Auth::user()->permission == 3)
				<a class="pull-right btn btn-primary" href="{{ url('/users/add') }}"><i class="fa fa-plus"></i> Tambah Pengguna</a>
				@endif
				<form method="get" action="">
					<input type="text" name="src" placeholder="Cari pengguna...">
				</form>
			</p>
			<br />
			<div class="table-responsive">
				<table class="table table-striped table-hover">
					<tr>
						<th width="5%" class="text-center"><i class="glyphicon glyphicon-camera"></i></th>
						<th width="25%">Nama Lengkap</th>
						<th width="30%">Username</th>
						<th width="15%">Akses</th>
						<th width="15%">Status</th>
						<th width="10%"></th>
					</tr>
					@forelse($users as $user)
						<tr class="item_{{ $user->id_user }}">
							<td class="text-center"><img width="20" class="img-circle" src="{{ asset('/img/avatars/xs/' . $user->avatar) }}"></td>
							<td>{{ $user->name }}</td>
							<td>{{ $user->username }}</td>
							<td>{{ $permission[$user->permission] }}</td>
							<td>{!! Format::online($user->id_user) ? '<i class="fa fa-circle" style="color:green;"></i> Online' : '<i class="fa fa-circle text-muted"></i> Offline' !!}</td>
							<td class="text-right">
								<div class="btn-group">
									@if(Auth::user()->permission > 1 && Me::data()->id_karyawan != $user->id_karyawan)
									<a href="{{ url('/users/edit/' . $user->id_user) }}" class="btn btn-mini btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
									@endif
									@if(Auth::user()->permission == 3 && Me::data()->id_karyawan != $user->id_karyawan)
									<button class="btn btn-danger btn-xs btn-mini hapus" data-id="{{ $user->id_user }}"><i class="fa fa-times"></i></button>
									@endif
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="3">
								<div class="well">Tidak ditemukan</div>
							</td>
						</tr>
					@endforelse

				</table>
			</div>

			<div class="text-right">
				{!! $users->appends(['src' => $src])->render() !!}
			</div>
		</div>
	</div>
@endsection