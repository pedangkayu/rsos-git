@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/prq.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('.tgl').datepicker({
				format : 'yyyy-mm-dd'
			}).on('changeDate', function(ev){
				getallprq(1);
			});

			$('.btn-tgl').click(function(){
				$('[name="tanggal"]').val('');
			});
			$('.btn-deadline').click(function(){
				$('[name="deadline"]').val('');
			});
		});
	</script>

	<style type="text/css">
		td > .links{
			display: none;
		}
		table.daftar-prq tr:hover td .links{
			display: block;
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
					<h4>{{ $items->total() }} pengajuan <b>ditemukan</b></h4>
				</div>
				<div class="grid-body no-border">
					<div class="table-responsive">
						<table class="table table-hover table-striped daftar-prq">
							<thead>
								<tr>
									<th class="text-middle text-center" width="5%">No.</th>
									<th class="text-middle text-center" width="25%">No. PRQ</th>
									<th class="text-middle text-center" width="20%">Deadline</th>
									<th class="text-middle text-center" width="20%">Oleh</th>
									<th class="text-middle text-center" width="10%">Pengajuan</th>
									<th class="text-middle text-center" width="20%">Tanggal Approval</th>
									<th class="text-middle text-center" width="10%">Status</th>
								</tr>
							</thead>

							<tbody class="contentPRQ">
								<?php $no = 1; ?>
								@forelse($items as $item)
									<tr class="item-prq-{{ $item->id_prq }}">
										<td>{{ $no }}</td>
										<td>
											<div>
												{{ $item->no_prq }}
												{!! empty($item->id_acc) ? '<i class="fa fa-times text-muted pull-right" title="Belum terverifikasi"></i>' : '<i title="Terverifikasi" class="fa fa-check-circle text-success pull-right"></i>' !!}
											</div>
											<div class="links">
												<small>
													[
														<a href="#" data-toggle="modal" data-target="#detailprq" onclick="detailprq({{ $item->id_prq }})">Lihat</a>
														@if($item->status < 2)
														| <a href="{{ url('/prq/edit/' . $item->id_prq) }}">Edit</a>
															@if(Auth::user()->permission > 1)
															| <a href="javascript:;" onclick="hapusprq({{ $item->id_prq }});" class="text-danger">Hapus</a>
															@endif
														@endif
													]
												</small>
											</div>
										</td>
										<td>
											<div {{ strtotime($item->target) > strtotime(date('Y-m-d')) ? '' : 'class=text-danger' }}>{{ Format::indoDate2($item->target) }}</div>
											@if(strtotime($item->target) > strtotime(date('Y-m-d')))
											<small class="text-muted">{{ Format::selisih_hari($item->target, date('Y-m-d')) }} hari dari sekarang</small>
											@endif
										</td>
										<td title="{{ $item->nm_depan }} {{ $item->nm_belakang }}">
											{{ Format::substr($item->nm_depan . '  ' . $item->nm_belakang,15) }}
											<div><small class="text-muted">{{ Format::indoDate($item->created_at) }}</small></div>
										</td>
										<td>{{ $item->tipe == 1 ? 'Obat' : 'Barang' }}</td>
										<td>
											@if(empty($item->tgl_approval) || $item->tgl_approval == '0000-00-00 00:00:00')
												<center>-</center>
											@else
												<div>{{ Format::indoDate2($item->tgl_approval) }}</div>
												<div class="text-muted"><small>{{ Format::hari($item->tgl_approval) }}, {{ Format::jam($item->tgl_approval) }}</small></div>
											@endif
										</td>
										<td>{{ $status[$item->status] }}</td>
									</tr>
									<?php $no++; ?>
								@empty
									<tr>
										<td colspan="7">Tidak ditemukan</td>
									</tr>
								@endforelse
							</tbody>
						</table>

						<div>
							<i title="Terverifikasi" class="fa fa-check-circle text-success"></i> Terverifikasi |
							<i class="fa fa-times text-muted" title="Belum terverifikasi"></i> Belum Terverifikasi
						</div>

						<div class="prqpagin text-right">
							{!! $items->render() !!}
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="col-sm-3">
			
			@if(Auth::user()->permission > 1 && $akses > 0)

				@if($akses > 2)
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border text-center">
							
							<div class="btn-group" style="width:100%;">
							  <button type="button" class="btn btn-primary btn-block dropdown-toggle" data-toggle="dropdown">
							    <i class="fa fa-plus"></i>  Buat Pengajuan <span class="caret"></span>
							  </button>
							  <ul class="dropdown-menu" role="menu">
							    <li><a href="{{ url('/prq/select/2') }}">Barang</a></li>
							    <li><a href="{{ url('/prq/select/1') }}">Obat</a></li>
							    
							  </ul>
							</div>

						</div>
					</div>
				@else
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
							<a class="btn btn-block btn-primary" href="{{ url('/prq/select/' . $akses) }}"><i class="fa fa-plus"></i> Buat Pengajuan <span class="spb-notif"></span></a>
						</div>
					</div>
				@endif

			@endif

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="form-group">
						<label for="no_prq">No Permohonan</label>
						<input type="text" name="no_prq" id="no_prq" class="form-control">
					</div>

					<div class="form-group">
						<div class="checkbox check-info">
							<input type="checkbox" name="titipan" id="titipan">
							<label for="titipan">Barang Titipan</label>
						</div>
					</div>
					
					<div class="form-group">
						<label for="tanggal">Tanggal</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="tanggal" id="tanggal" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-tgl" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="deadline">Deadline</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="deadline" id="deadline" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-deadline" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="status">Status</label>
						<select name="status" id="status" class="form-control">
							<option value="0">Semua Status</option>
							<option value="1" selected="selected">Baru</option>
							<option value="2">Proses</option>
							<option value="3">Selesai</option>
						</select>
					</div>

					<div class="form-group">
						<label for="limits">Limit / Page</label>
						<select name="limit" id="limits" class="form-control">
							<option value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="500">500</option>
						</select>
					</div>
					@if($akses > 0)
					<div class="form-group">
						<button class="btn btn-block btn-primary cari"><i class="fa fa-search"></i> Cari</button>
					</div>
					@endif

				</div>
			</div>

		</div>
	</div>

@endsection

@section('footer')
	<!-- Modal -->
	<div class="modal fade" id="detailprq" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	        <h4 class="modal-title" id="myModalLabel">No. <span class="modal-noprq"></span></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="grid simple">
	      		<div class="grid-title no-border"></div>
	      		<div class="grid-body no-border">
	      			<div class="modal-contentprq table-responsive">
			        	Memuat...
			        </div>
	      		</div>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Keluar</button>
	        <span class="btn-acc"></span>
	      </div>
	    </div>
	  </div>
	</div>
@endsection