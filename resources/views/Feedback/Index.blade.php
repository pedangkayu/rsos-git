@extends('Master.Template')

@section('csstop')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection
@section('meta')
	<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$(function(){
			$('.tgl').datepicker({
				format : 'yyyy-mm-dd'
			});
			$('.btn-tanggal').click(function(){
				$('#tanggal').val('');
			});

			$.getJSON(_base_url + '/feedback/dev', {}, function(json){
				$('[name="departemen"]').html(json.content);
				$('[name="departemen"]').select2();
			});


			allfeedback = function(page){

				var $nama 		= $('[name="nama"]').val();
				var $tanggal 	= $('[name="tanggal"]').val();
				var $status 	= $('[name="status"]').val();
				var $departemen = $('[name="departemen"]').val();
				var $limit 		= $('[name="limit"]').val();
				

				$('.content-feedback').css('opacity', .3);

				$.getJSON(_base_url + '/feedback/allfeedback', {

					page		: page,
					nama 		: $nama,
					tanggal 	: $tanggal,
					status 		: $status,
					departemen 	: $departemen,
					limit 		: $limit

				}, function(json){
					
					$('.content-feedback').html(json.content);
					$('.pagin').html(json.pagin);

					$('.content-feedback').css('opacity', 1);
					onDataCancel();

					$('div.pagin > ul.pagination > li > a').click(function(e){
						e.preventDefault();
						var $link 	= $(this).attr('href');
						var $split 	= $link.split('?page=');
						var $page 	= $split[1];
						allfeedback($page);
					});

				});

			}

			$('div.pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allfeedback($page);
			});

			$('.cari').click(function(){
				allfeedback(1);
			});



		});
	</script>
@endsection

@section('title')
	Feedback
@endsection

@section('content')
	
	<div class="row">
		<!-- left -->
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ $items->total() }} feedback <strong>ditemukan</strong></h4>
				</div>
				<div class="grid-body no-border">
					
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th width="5%">No.</th>
									<th width="5%">Kode</th>
									<th width="40%">Title</th>
									<th width="10%">Status</th>
									<th width="20%">Tanggal</th>
									<th width="15%">Oleh</th>
									<th width="5%">Komen</th>
								</tr>
							</thead>

							<tbody class="content-feedback">
								<?php $no = 1; ?>
								@forelse($items as $item)
									<tr>
										<td>{{ $no }}</td>
										<td>
											<a href="{{ url('/feedback/jawab/' . $item->id_feedback) }}">#{{ $item->kode }}</a>
										</td>
										<td>
											<a href="{{ url('/feedback/jawab/' . $item->id_feedback) }}">{{ $item->title }}</a>
										</td>
										<td>{{ $item->status == 1 ? 'Open' : 'Closed' }}</td>
										<td>
											{{ Format::indoDate($item->created_at) }}<br />
											<small class="text-muted">{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small>
										</td>
										<td>{{ $item->nm_depan }}</td>
										<td class="text-right"><span class="badge">{{ $item->notif }}</span></td>
									</tr>
									<?php $no++; ?>
								@empty
									<tr>
										<td colspan="7">Tidak ditemukan</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					<div class="text-right pagin">
						{!! $items->render() !!}
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
						<label for="nama">Nama Karyawan</label>
						<input type="text" id="nama" name="nama" class="form-control">
					</div>

					<div class="form-group">
						<label for="tanggal">Tanggal Buat</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="tanggal" id="tanggal" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-tanggal" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="departemen">Departemen</label>
						<select style="width:100%;" name="departemen" id="departemen">
							<option value="">Memuat...</option>
						</select>
					</div>

					<div class="form-group">
						<label for="status">Status</label>
						<select class="form-control" name="status" id="status">
							<option value="0">Semua</option>
							<option value="1">Open</option>
							<option value="2">Closed</option>
						</select>
					</div>

					<div class="form-group">
						<label for="limit">Limit / Page</label>
						<select class="form-control" name="limit" id="limit">
							<option value="5">5</option>
							<option value="10" selected="selected">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
					</div>
					
					<div class="form-group">
						<button class="btn btn-primary btn-block cari"><i class="fa fa-search"></i> Cari</button>
					</div>

				</div>
			</div>

		</div>
		
	</div>

@endsection