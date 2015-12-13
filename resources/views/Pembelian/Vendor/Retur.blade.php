@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){

			allretur = function(page){

				var $no_retur 	= $('[name="no_retur"').val();
				var $no_po	 	= $('[name="no_po"').val();
				var $tgl 		= $('[name="tanggal"').val();
				var $id 		= $('[name="id"').val();
				var $limit 		= $('[name="limit"').val();

				$('.content-retur').css('opacity', .3);

				$.getJSON(_base_url + '/vendor/allretur', {

					page 		: page,
					no_retur 	: $no_retur,
					no_po 		: $no_po,
					tanggal 	: $tgl,
					id 			: $id,
					limit 		: $limit

				}, function(json){
					
					$('.content-retur').css('opacity', 1);

					$('.content-retur').html(json.content);
					$('.retur-pagin').html(json.pagin);

					onDataCancel();

					$('div.retur-pagin > ul.pagination > li > a').click(function(e){
						e.preventDefault();
						var $link 	= $(this).attr('href');
						var $split 	= $link.split('?page=');
						var $page 	= $split[1];
						allretur($page);
					});
				});

			}

			$('div.retur-pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allretur($page);
			});

			$('.cari').click(function(){
				allretur(1);
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
	            <li><a href="{{ url('/vendor/po/' . $vendor->id_vendor) }}">Purcase Order</a></li>
	            <li class="active"><a href="{{ url('/vendor/retur/' . $vendor->id_vendor) }}">Retur Barang</a></li>
	        </ul>

	        <div class="tab-content">
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<h4>{{ $items->total() }} retur <strong>ditemukan</strong></h4>
						<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th width="5%">No</th>
									<th width="20%">No. Retur</th>
									<th width="15%">No. PO</th>
									<th width="30%">&nbsp;</th>
									<th width="20%">Tanggal</th>
									<th width="10%"></th>
								</tr>
							</thead>

							<tbody class="content-retur">
								<?php $no = 1; ?>
								@forelse($items as $item)
									<tr>
										<td>{{ $no }}</td>
										<td>
											{{ $item->no_retur }}
										</td>
										<td>{{ $item->no_po }}</td>
										<td>
											&nbsp;
										</td>
										<td>
											{{ Format::indoDate2($item->created_at) }}<br />
											<small class="text-muted">{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small>
										</td>
										<td>
											<a class="btn btn-white" href="{{ url('returvendor/print/' . $item->id_retur) }}" target="_blank"><i class="fa fa-print"></i></a>
										</td>
									</tr>
									<?php $no++; ?>
								@empty
									<tr>
										<td colspan="5">Tidak ditemukan!</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					<div class="text-right retur-pagin">
						{!! $items->render() !!}
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
					
					<div class="form-group">
						<label>No. Retur</label>
						<input type="text" name="no_retur" class="form-control">
					</div>

					<div class="form-group">
						<label>No. PO</label>
						<input type="text" name="no_po" class="form-control">
					</div>
					
					<input type="hidden" value="{{ $vendor->id_vendor }}" name="id">

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
						<label for="limit">Limit / Page</label>
						<select name="limit" class="form-control">
							<option value="5">5</option>
							<option value="10" selected="selected">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
					</div>

					<div class="form-group">
						<button class="cari btn btn-block btn-primary"><i class="fa fa-search"></i> Cari</button>
					</div>

				</div>
			</div>
			
		</div>
	</div>
@endsection
	