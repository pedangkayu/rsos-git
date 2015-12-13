@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){

			allpo = function(page){

				var $no_po 		= $('[name="no_po"]').val();
				var $tanggal 	= $('[name="tanggal"]').val();
				var $status 	= $('[name="status"]').val();
				var $limit 		= $('[name="limit"]').val();
				var $deadline 	= $('[name="deadline"]').val();
				var $id 	= $('[name="id"]').val();

				$('.content-po').css('opacity', .3);

				$.getJSON(_base_url + '/vendor/allpo', {

					page	: page,
					no_po 	: $no_po,
					tanggal : $tanggal,
					status 	: $status,
					limit 	: $limit,
					deadline : $deadline,
					id 		: $id

				}, function(json){
					
					$('.content-po').html(json.content);
					$('.pagin').html(json.pagin);

					$('.content-po').css('opacity', 1);
					onDataCancel();

					$('div.pagin > ul.pagination > li > a').click(function(e){
						e.preventDefault();
						var $link 	= $(this).attr('href');
						var $split 	= $link.split('?page=');
						var $page 	= $split[1];
						allpo($page);
					});

				});

			}

			$('div.pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allpo($page);
			});

			$('.cari').click(function(){
				allpo(1);
			});

			$('.tgl').datepicker({
				format : 'yyyy-mm-dd'
			});
			$('.btn-tanggal').click(function(){
				$('#tanggal').val('');
			});
			$('.btn-deadline').click(function(){
				$('#deadline').val('');
			});
		});
	</script>
@endsection

@section('title')
	Kode. {{ $vendor->kode }}
@endsection

@section('content')
	<div class="row">
		<div class="col-sm-9">
			
			<ul class="nav nav-tabs" id="tab-01">
	            <li><a href="{{ url('/vendor/review/' . $vendor->id_vendor) }}">Detail</a></li>
	            <li class="active"><a href="{{ url('/vendor/po/' . $vendor->id_vendor) }}">Purcase Order</a></li>
	            <li><a href="{{ url('/vendor/retur/' . $vendor->id_vendor) }}">Retur Barang</a></li>
	        </ul>

	        <div class="tab-content">
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						
						<div class="table-responsive">
							<table class="table table-striped daftar-po">
								<thead>
									<tr>
										<th width="7%">No.</th>
										<th width="20%">No PO</th>
										<th width="40%">Tanggal</th>
										<th width="23%">Deadline</th>
										<th width="10%">Status</th>
									</tr>
								</thead>

								<tbody class="content-po">
									<?php $no = 1; ?>
									@forelse($items as $item)
									<tr>
										<td>{{ $no }}</td>
										<td>
											{{ $item->no_po }}
											<div class="link">
												<small>
													[<a href="{{ url('/po/print/' . $item->id_po) }}" target="_balnk">Print</a>]
												</small>
											</div>	
										</td>
										<td>
											<span>{{ Format::indoDate($item->created_at) }}</span>
											<div><small class="text-muted">{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small></div>
										</td>
										<td>
											<span>{{ Format::indoDate($item->deadline) }}</span>
										</td>
										<td>{{ $status[$item->status] }}</td>
									</tr>
									<?php $no++; ?>
									@empty
										<tr>
					    					<td colspan="5">Tidak ditemukan</td>
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
			</div>

		</div>

		<div class="col-sm-3">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<address>
						<strong>Dibuat Oleh</strong>
						<p>{{ $vendor->nm_depan }} {{ $vendor->nm_belakang }}</p>
						<strong>Tanggal</strong>
						<p>
							{{ Format::indoDate($vendor->created_at) }}<br />
							<small class="text-muted">{{ Format::hari($vendor->created_at) }}, {{ Format::jam($vendor->created_at) }}</small>
						</p>
					</address>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
				<input type="hidden" name="id" value="{{ $vendor->id_vendor }}">

					<div class="form-group">
						<label for="no_po">No. PO</label>
						<input type="text" id="no_po" name="no_po" class="form-control">
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
						<select class="form-control" name="status" id="status">
							<option value="0">Semua</option>
							<option value="1">Baru</option>
							<option value="2">Proses</option>
							<option value="3">Selesai</option>
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
	