@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){
			$('[name="ask"]').focus();

			$('[name="status"]').change(function(){
				var $val = $(this).val();
				var $id = $(this).data('id');
				$.post(_base_url + '/feedback/status', {val : $val, id : $id}, function(json){
					if(json.result == false)
						swal('','Gagal merubah status!');
				}, 'json');
			});

			del = function(id){
				swal({   
					title: "PERINGATAN!",   
					text: "akan dihapus Pertanyaan ini ?",   
					type: "warning",   
					showCancelButton: true,   
					confirmButtonColor: "#DD6B55",   
					confirmButtonText: "Yes, delete it!",
				}, function(){
						
					$.post(_base_url + '/feedback/del', {id : id}, function(json){
						if(json.result == false)
							swal('','Gagal menghapus data!');
						else
							$('.opsi').remove();
					}, 'json');
				});
			}

			delcommen = function(id){
				swal({   
					title: "PERINGATAN!",   
					text: "akan dihapus komentar ini ?",   
					type: "warning",   
					showCancelButton: true,   
					confirmButtonColor: "#DD6B55",   
					confirmButtonText: "Yes, delete it!",
				}, function(){
					
					$('.comment-' + id).css('opacity', .3);
					$.post(_base_url + '/feedback/delcommen', {id : id}, function(json){
						$('.comment-' + json.id).remove();
					}, 'json');
				});
			}

		});
	</script>	
@endsection

@section('content')
	
	<div class="row">
		<!-- left -->
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h3>{{ $feed->title }}</h3>
					<span class="text-muted"><em>&nbsp;&nbsp;<strong>Kode :</strong> #{{ $feed->kode }} | <strong>Tanggal :</strong> {{ Format::indoDate($feed->created_at) }}</em></span>
				</div>
				<div class="grid-body no-border">
					<p>{{ $feed->ask }}</p>
					@if(!empty($feed->link))
					<p>
						<strong>Tautan Link : </strong><a href="{{ $feed->link }}" target="_blank">{{ $feed->link }}</a>
					</p>
					@endif
					@if(!empty($feed->file))
					<button type="button" class="btn btn-white btn-small" data-toggle="collapse" data-target="#lampiran">
					  	<i class="fa fa-paperclip"></i> Tampilkan Lampiran
					</button>

					<div id="lampiran" class="collapse">
						<br />
						<img src="{{ asset('/img/feedback/' . $feed->file) }}" class="img-responsive img-thumbnail">
					</div>
					@endif

					<div class="text-right">
						Jawaban {{ count($items) }}
					</div>
					<hr />

					<div>
						@foreach($items as $item)
							<div class="comment-{{ $item->id_feedback }}">
								<div>
									@if($item->id_karyawan == $id_karyawan)
										<div class="btn-group pull-right">
										  <button type="button" class="btn btn-white btn-mini dropdown-toggle" data-toggle="dropdown">
										    <i class="fa fa-cog"></i>
										  </button>
										  <ul class="dropdown-menu" role="menu">
										    <li>
										    	<a href="{{ url('/feedback/edit/' . $item->id_feedback) }}">Perbaharui</a>
										    </li>
										    <li><a href="javascript:void(0);" onclick="delcommen({{ $item->id_feedback }});">Hapus</a></li>
										  </ul>
										</div>
									@endif
									<h5 style="margin:0;"><strong>{{ $item->nm_depan }} {{ $item->nm_belakang }}</strong></h5>
									<small><strong>Tanggal : </strong> {{ Format::indoDate($item->created_at) }} jm: {{ Format::jam($item->created_at) }}</small>
								</div>
								
								<p>{{ $item->ask }}</p>

								@if(!empty($item->file))
								<button type="button" class="btn btn-white btn-small" data-toggle="collapse" data-target="#lampiran{{ $item->id_feedback }}">
								  	<i class="fa fa-paperclip"></i> Tampilkan Lampiran
								</button>

								<div id="lampiran{{ $item->id_feedback }}" class="collapse">
									<br />
									<img src="{{ asset('/img/feedback/' . $item->file) }}" class="img-responsive img-thumbnail">
								</div>
								@endif
								<hr />
							</div>
						@endforeach
					</div>

					@if($feed->status == 1)
					
					<div>
						<form method="post" action="{{ url('/feedback/jawab') }}" enctype="multipart/form-data">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<div class="form-group">
								<textarea class="form-control" rows="6" name="feed_ask" required></textarea>
							</div>
							<div class="form-group">
								<button class="btn btn-primary pull-right" type="submit">Kirim Jawaban</button>

								<label for="feed_file">
		    						<span class="btn btn-white"><i class="fa fa-paperclip"></i> Lampiran</span> <span class="feed_file"></span>
		    					</label>
		    					<input type="file" accept="image/*" name="feed_file" class="sr-only" id="feed_file">
							</div>

		    				<input type="hidden" name="feed_title" value="">
		    				<input type="hidden" name="feed_link" value="">
		    				<input type="hidden" name="parent_id" value="{{ $feed->id_feedback }}">

						</form>
					</div>
					@endif
				</div>
			</div>

		</div>

		<!-- right -->
		<div class="col-sm-3">
			
			<div class="grid simple">
				<div class="grid-title text-right no-border">
				@if($feed->id_karyawan == Me::data()->id_karyawan)
					<h4><a title="Edit" href="{{ url('/feedback/edit/' . $feed->id_feedback) }}"><i class="fa fa-pencil"></i></a></h4>
				@endif
				</div>
				<div class="grid-body no-border">
					
					<address>
						<strong>Oleh</strong>
						<p>{{ $feed->nm_depan }} {{ $feed->nm_belakang }}</p>
						<strong>Departemen</strong>
						<p>{{ $feed->nm_departemen }}</p>
						<strong>Tanggal</strong>
						<p>
							{{ Format::indoDate($feed->create_at) }}<br />
							<small class="text-muted">{{ Format::hari($feed->create_at) }}, {{ Format::jam($feed->create_at) }}</small>
						</p>

						<div class="opsi">
							@if($feed->status > 0 && $feed->id_karyawan == Me::data()->id_karyawan)
							<strong>Status</strong>
							<p>
								<select name="status" class="form-control" data-id="{{ $feed->id_feedback }}">
									<option value="1" {{ $feed->status == 1 ? 'selected="selected"' : '' }}>Open</option>
									<option value="2" {{ $feed->status == 2 ? 'selected="selected"' : '' }}>Closed</option>
								</select>
							</p>
							@endif
						</div>
					</address>
					<div class="opsi">
						@if($feed->status == 1 && $feed->id_karyawan == Me::data()->id_karyawan)
						<a href="javascript:void(0);" onclick="del({{ $feed->id_feedback }});" class="btn btn-block btn-danger"><i class="fa fa-trash"></i> Hapus</a>
						@endif
					</div>
					<hr />
					<a href="{{ url('/feedback') }}" class="btn btn-block btn-white">Kembali</a>
				</div>
			</div>

		</div>
		
	</div>

@endsection