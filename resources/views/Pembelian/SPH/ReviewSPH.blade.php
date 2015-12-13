@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){
			hapus = function(id){

				swal({
					title: "Anda yakin ?",   
					text: "Anda yakin ingin menghapusnya ini?",
					type: "warning",   
					showCancelButton: true,   
					confirmButtonColor: "#DD6B55",   
					confirmButtonText: "Yes, disable it!",   
					closeOnConfirm: true
				}, function(){
					$('.item-' + id).css('opacity', .3);
					$.post(_base_url + '/sph/delitemsph', {id : id}, function(json){
						$('.item-' + json.id).remove();
					}, 'json');
				});

			}

			copy = function(){
				$('.daftar-sph').css('opacity', .3);
			}

			$('.topo').click(function(){

				swal({
					title: "Anda yakin ?",   
					text: "Pastikan data-data sudah terisi secara lengkap, Anda tidak dapat melakukan perubahan PO yang melalui proses SPH sebelumnya!",
					type: "warning",   
					showCancelButton: true,   
					confirmButtonColor: "#DD6B55",   
					confirmButtonText: "Yes!",   
					closeOnConfirm: true
				}, function(){
					$('#topo').submit();
				});
			});

		});
	</script>
	<style type="text/css">
		td > .link{
			display: none;
		}
		table.daftar-sph tr:hover td .link{
			display: block;
		}

		.coret{
			text-decoration:line-through;
			color: red;
		}
	</style>
@endsection

@section('title')
	Daftar SPH
@endsection

@section('content')
	
	<form method="post"	action="{{ url('/sph/topo') }}" id="topo">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="id_sph_grup" value="{{ $grup->id_sph_grup }}">

		<div class="row">
			<div class="col-sm-9">

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						
						<table class="table table-striped daftar-sph">
							<thead>
								<tr>
									<th width="25%">No</th>
									<th width="45%">Penyedia</th>
									<th width="20%">deadline</th>
									@if($grup->status == 1)
									<th width="10%"></th>
									@endif
								</tr>
							</thead>

							<tbody>
								@foreach($items as $item)
								<tr class="item-{{ $item->id_sph }}  {{ $grup->status == 2 && $item->status == 1 ? 'coret' : '' }}">
									<td {{ $item->status == 3 ? 'style=background:#ffcccc;' : '' }}>
										{{ $item->no_sph_item }}
										{!! $danger = $item->status == 3 ? '<i class="fa fa-warning pull-right text-danger"></i>' : '' !!}
										<div class="link">
											<small>
												[
													<a href="{{ url('/sph/print/' . $item->id_sph) }}" target="_blank">Print</a>
													@if($grup->status == 1)
														@if(Auth::user()->permission > 1)
														| <a href="{{ url('/sph/editsph/' . $item->id_sph) }}">Edit</a>
														@endif
														| <a href="{{ url('/sph/copy/' . $item->id_sph) }}" onclick="copy();">Copy</a>
														@if(Auth::user()->permission > 1)
														| <a href="javascript:;" onclick="hapus({{ $item->id_sph }});" class="text-danger">Hapus</a>
														@endif
													@elseif($grup->status == 2 && $item->status == 2)
														| <a href="{{ url('/sph/editsphsystem/' . $item->id_sph) }}">Edit</a>
													@endif
												]
											</small>
										</div>
									</td>
									<td {{ $item->status == 3 ? 'style=background:#ffcccc;' : '' }}>
										{{ $item->nm_vendor }}<br />
										<small class="text-muted">{{ $item->telpon }}</small>
									</td>
									<td {{ $item->status == 3 ? 'style=background:#ffcccc;' : '' }}>
										{{ Format::indoDate($item->deadline) }}
										<div>
											<small>{{ $item->status == 3 ? 'Delete System' : '' }}</small>
										</div>
									</td>

									@if($grup->status == 1)
									<td class="text-right" {{ $item->status == 3 ? 'style=background:#ffcccc;' : '' }}>
										<div class="radio">
											<input type="radio" name="id_sph" id="id_sph{{ $item->id_sph }}" value="{{ $item->id_sph }}" required>
											<label for="id_sph{{ $item->id_sph }}">&nbsp;</label>
										</div>
									</td>
									@endif
								</tr>
								@endforeach
							</tbody>
						</table>

					</div>
				</div>

			</div>
			<div class="col-sm-3">

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<address>
							<strong>No. SPH</strong>
							<p>{{ $grup->no_sph }}</p>
							<strong>Tanggal Buat</strong>
							<p>{{ Format::indoDate($grup->created_at) }}</p>
						</address>
					</div>
				</div>

				@if($grup->status == 1)
					@if(Auth::user()->permission > 1)
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
							<p>Pilih salah satu dari ke {{ count($items) }} pengajuan untuk dijadikan PO</p>
							<button type="button" class="btn btn-block btn-primary topo">Jadikan PO</button>

							<a href="{{ url('/sph') }}" class="btn btn-block btn-primary">Kembali</a>
						</div>
					</div>
					@endif
				@else
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
							<p>Yang tercoret adalah SPH yang tidak dipilih</p>
							<a href="{{ url('/sph') }}" class="btn btn-block btn-primary">Kembali</a>
						</div>
					</div>
				@endif

			</div>
		</div>
	</form>

@endsection