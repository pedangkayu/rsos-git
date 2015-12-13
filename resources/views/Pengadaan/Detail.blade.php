@extends('Master.Template')

@section('csstop')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
	<style>
		.datepicker{z-index:1151 !important;}
	</style>
@endsection
@section('meta')
	<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/js/pengadaan/logistik.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('#tab-01 a').click(function (e) {
				e.preventDefault();
				$(this).tab('show');
			});

			// date pic
			var checkin = $('#dpd1').datepicker({
				format : 'yyyy-mm-dd'
			}).on('changeDate', function(ev) {
			  	if (ev.date.valueOf() > checkout.date.valueOf()) {
			    	var newDate = new Date(ev.date)
			    	newDate.setDate(newDate.getDate() + 1);
			    	checkout.setValue(newDate);
			  	}
			  	checkin.hide();
			  	$('#dpd2')[0].focus();
			}).data('datepicker');

			var checkout = $('#dpd2').datepicker({
				  	onRender: function(date) {
				    	return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
				  	},
					format : 'yyyy-mm-dd'
			}).on('changeDate', function(ev) {
			  	checkout.hide();
			}).data('datepicker');

			///////////////////////////////////////////

			$('.waktu-src').click(function(){
				var val = $(this).val();
				if(val == 1){
					$('.pertanggal').addClass('hide');
					$('.perbulan').removeClass('hide');
				}else{
					$('.perbulan').addClass('hide');
					$('.pertanggal').removeClass('hide');
				}
			});

			$('.select').select2();
			$('form').submit(function(){
				$('[type="submit"]').button('reset');
				$('body').css('cursor', 'default');
			});

			logexp(1, {{ $item->id_barang }});
		});
	</script>
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-7">
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><span class="semi-bold">Kode</span> {{ $item->kode }}</h4>
					@if(Auth::user()->permission > 1)
					<div class="pull-right">
		          		<a href="{{ url('/logistik/update/' . $item->id_barang ) }}" title="Perbaharui {{ $item->kode }}" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-pencil"></i></a> 
		          	</div>
		          	@endif
				</div>
				<div class="grid-body no-border">
					
					<div class="row">
						<div class="col-sm-6">
							<h4><span class="semi-bold">Keterangan</span> Barang</h4>
							<address>
								<strong>Nama Barang</strong>
								<p>{{ $item->nm_barang }}</p>

								<strong>Kategori</strong>
								<p>{{ $item->nm_kategori }}</p>

								<strong>Jenis Barang</strong>
								<p>{{ $tipes[$item->tipe] }}</p>

								@if($item->tipe == 1)
									<strong>Klasifikasi</strong>
									<p>{{ $item->nm_klasifikasi }}</p>
								@endif

							</address>
						</div>
						<div class="col-sm-6">
							<h4><span class="semi-bold">Keterangan</span> Stok</h4>
							<address>
								<strong>Stok Awal</strong>
								<p>{{ number_format($item->stok_awal,0,',','.') }} {{ $item->nm_satuan }}</p>
								<strong>Stok Minimal</strong>
								<p>{{ number_format($item->stok_minimal,0,',','.') }} {{ $item->nm_satuan }}</p>
								<strong>Sisa Stok</strong>
								<p>{{ number_format(( $item->in - $item->out ),0,',','.') }} {{ $item->nm_satuan }} {!! $item->stok_minimal >= ( $item->in - $item->out ) ? '<span class="text-danger semi-bold">(Stok Limit)</span>' : '' !!}</p>
								@if($item->tipe == 1)
									<strong>Harga Beli / Item</strong>
									<p>{{ number_format($item->harga_beli,0,',','.') }} <b><a href="#" class="text-danger" data-toggle="modal" data-target="#modal-harga" onclick="log_harga({{ $item->id_barang }},1,1);">[Log Harga]</a></b></p>

									<strong>Harga Jual / Item</strong>
									<p>{{ number_format($item->harga_jual,0,',','.') }} <b><a href="#" class="text-danger" data-toggle="modal" data-target="#modal-harga" onclick="log_harga({{ $item->id_barang }},1,2);">[Log Harga]</a></b></p>
								@endif
							</address>
						</div>
					</div>
					<br />
					<div class="text-right">
						<a href="{{ url('/logistik') }}" class="btn btn-default pull-left">Kembali</a>
						<button class="btn btn-primary" data-toggle="modal" data-target="#kartustok">Kartu Stok</button>
					</div>

				</div>
			</div>


			@if(count($details) > 0)
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4><strong>Detail</strong> Barang</h4>
						<div class="tools">
			          		<a href="javascript:;" class="collapse"></a> 
			          	</div>
					</div>
					<div class="grid-body no-border">
						<address>
							@foreach($details as $detail)
								<p><div class="row">
									<div class="col-sm-6 semi-bold">{{ $detail->label }}</div>
									<div class="col-sm-6">: {{ $detail->nm_detail }}</div>
								</div></p>
							@endforeach
						</address>
					</div>
				</div>
			@endif

			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><strong>List</strong> Expired</h4>
				</div>
				<div class="grid-body no-border">
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>No. Good Receive</th>
									<th class="text-right">Tgl. Exp</th>
								</tr>
							</thead>
							<tbody class="list-exp">
								<tr>
									<td colspan="2"><i class="fa fa-circle-o-notch fa-spin"></i> Memuat...</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="text-right pagin-exp"></div>

				</div>
			</div>

		</div>

		<div class="col-sm-5">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><strong>Keterangan</strong> Pembuat</h4>
					<div class="tools">
		          		<a href="javascript:;" class="collapse"></a> 
		          	</div>
				</div>
				<div class="grid-body no-border">
					<address>
						<strong>Oleh</strong>
						<p>{{ $item->nm_depan }} {{ $item->nm_belakang }}</p>
						<strong>Tanggal Buat</strong>
						<p>{!! Format::hari($item->created_at) . ', ' . Format::indoDate($item->created_at) . ' jm ' . Format::jam($item->created_at) !!}</p>
					</address>
				</div>
			</div>

			@if($item->tipe == 1)
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><strong>Stok</strong> Gudang</h4>
					<div class="tools">
		          		<a href="javascript:;" class="collapse"></a> 
		          	</div>
				</div>
				<div class="grid-body no-border stok-poli">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Gudang</th>
								<th class="text-right">Sisa Stok</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="2"><i class="fa fa-circle-o-notch fa-spin"></i> Memuat...</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			@endif
		</div>
	</div>

@endsection

@section('footer')
	@if($item->tipe == 1)
	<script type="text/javascript">
		$(function(){
			$.ajax({
				type : 'POST',
				url : _base_url + '/logistik/poli',
				data : {id : '{{ $item->id_barang }}'},
				cache : false,
				dataType : 'json',
				success : function(res){
					if(res.result == true)
						$('.stok-poli').find('tbody').html(res.content);

				}
			});
		});
	</script>
	@endif


	<!-- Modal -->
	<div class="modal fade" id="modal-harga" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	        <h4 class="modal-title" id="myModalLabel">Log Harga {{ $item->kode }}</h4>
	      </div>
	      <div class="modal-body">
	        
	      	<div class="grid simple">
	      		<div class="grid-title no-border"></div>
	      		<div class="grid-body no-border">
	      			<div class="table-responsive content-harga">
	      				Memuat...
	      			</div>

	      			<div class="pagin-harga text-right"></div>
	      		</div>
	      	</div>

	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
	      </div>
	    </div>
	  </div>
	</div>

	<!-- Kartu Stok -->
	<!-- Modal -->
	<div class="modal fade" id="kartustok" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">

	    		<form method="get" action="{{ url('/reportlogistik/ks') }}" target="_blank">

		      		<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		        		<h4 class="modal-title" id="myModalLabel">Periode</h4>
		      		</div>
		      		<div class="modal-body">
		        		
		        		<div class="grid simple">
		        			<div class="grid-title no-border"></div>
		        			<div class="grid-body no-border">
		        				
				      				<input type="hidden" value="{{ csrf_token() }}" name="_token">
				      				<input type="hidden" name="barang" value="{{ $item->id_barang }}">
				      				<div class="form-group">
			        					<label>Waktu</label>
			            				<div class="radio">
			            					<input type="radio" name="waktu" class="waktu-src" value="1" id="bln" checked>
			            					<label for="bln">Per Bulan</label>

			            					<input type="radio" name="waktu" class="waktu-src" value="2" id="tgl">
			            					<label for="tgl">Per Tanggal</label>

			            				</div>
			        				</div>

			        				<div class="perbulan">
					            		<div class="form-group">
					            			<label for="bulan">Bulan</label>
					            			<select class="select" style="width:100%;" name="bulan" id="bulan">
					            				@for($i=1;$i<13;$i++)
					            					<option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>{{ Format::nama_bulan($i) }}</option>
					            				@endfor
					            			</select>
					            		</div>	 
					            		

					            		<div class="form-group">
					            			<label for="tahun">Tahun</label>
					            			<select class="select text-right" style="width:100%;" name="tahun" id="tahun">
					            				@for($i = 2000; $i <= date('Y'); $i++)
					            					<option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
					            				@endfor
					            			</select>
					            		</div>
				            		</div>

				            		<div class="pertanggal hide">
				            			<div class="form-group">
					            			<label>Tentukan tanggal</label>
					            			<div class="input-group">
											  <input type="text" name="dari" class="form-control" readonly="readonly" value="{{ date('Y-m-d') }}" id="dpd1">
											  <span class="input-group-addon">s/d</span>
											  <input type="text" name="sampai" class="form-control" readonly="readonly" value="{{ date('Y-m-d', strtotime('+30 day', time())) }}" id="dpd2">
											</div>
											<small class="text-muted">Hari tidak boleh lebih dari 30 hari</small>
										</div>
				            		</div>
				      				
		        			</div>
		        		</div>

		      		</div>
		      		<div class="modal-footer">
		      			<button type="button" class="pull-left btn btn-default" data-dismiss="modal">Batal</button>
		        		<button type="submit" class="btn btn-primary">Proses</button>
		      		</div>

	      		</form>	

	    	</div>
	  	</div>
	</div>
@endsection