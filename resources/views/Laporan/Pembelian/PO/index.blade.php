@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/Laporan/pembelian/po/index.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			close_sidebar();

			$('[name="limit"]').change(function(){
				var val = $(this).val();
				if(val < 5)
					$(this).val(5);
			});

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

			// date pic /////////////////////////////////////////
			var checkinb = $('#dpd3').datepicker({
				format : 'yyyy-mm-dd'
			}).on('changeDate', function(ev) {
			  	if (ev.date.valueOf() > checkoutb.date.valueOf()) {
			    	var newDate = new Date(ev.date)
			    	newDate.setDate(newDate.getDate() + 1);
			    	checkoutb.setValue(newDate);
			  	}
			  	checkinb.hide();
			  	$('#dpd4')[0].focus();
			}).data('datepicker');

			var checkoutb = $('#dpd4').datepicker({
				  	onRender: function(date) {
				    	return date.valueOf() <= checkinb.date.valueOf() ? 'disabled' : '';
				  	},
					format : 'yyyy-mm-dd'
			}).on('changeDate', function(ev) {
			  	checkoutb.hide();
			}).data('datepicker');

			$('form').submit(function(){
				$('[type="submit"]').button('reset');
				$('body').css('cursor', 'default');
			});
		});
	</script>
@endsection

@section('title')
	Laporan Purchase Order (PO)
@endsection

@section('content')
	
	<form method="get" action="{{ url('/laporanpo/print') }}" target="_blank">
		<div class="row">
			<!-- left -->
			<div class="col-sm-8">
				
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>Pencarian</h4>
					</div>
					<div class="grid-body no-border">
						
						<div class="row">
							<div class="col-sm-9">

								<div class="perbulan">
									<div class="row">
										<div class="col-sm-8">
											<div class="form-group">
						            			<label for="bulan">Bulan</label>
						            			<select class="select" style="width:100%;" name="bulan" id="bulan">
						            				@for($i=1;$i<13;$i++)
						            					<option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>{{ Format::nama_bulan($i) }}</option>
						            				@endfor
						            			</select>
						            		</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
						            			<label for="tahun">Tahun</label>
						            			<select class="select text-right" style="width:100%;" name="tahun" id="tahun">
						            				@for($i = 2000; $i <= date('Y'); $i++)
						            					<option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
						            				@endfor
						            			</select>
						            		</div>
										</div>
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
									</div>
			            		</div>

							</div>

							<div class="col-sm-3">
								
								<div class="form-group">
									<label>Tipe item</label>
									<select name="tipe" class="form-control">
										<option value="0">Semua Tipe</option>
										<option value="1">Obat</option>
										<option value="2">Barang</option>
									</select>
								</div>

							</div>

						</div>

						

					</div>
				</div>

			</div>

			<div class="col-sm-4">
				
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border text-center">
						<div class="row">
							<div class="col-sm-8">
								<div class="form-group text-left">
									<label>Waktu</label>
			        				<div class="radio">
			        					<input type="radio" name="waktu" class="waktu-src" value="1" id="bln" checked>
			        					<label for="bln">Per Bulan</label>

			        					<input type="radio" name="waktu" class="waktu-src" value="2" id="tgl">
			        					<label for="tgl">Per Tanggal</label>
			        				</div>
			    				</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group text-left">
									<label>Limit / Page</label>
									<input type="number" name="limit" value="10" id="limit" class="form-control">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-8">
								<button type="button" class="btn btn-primary btn-block btn-proses" data-loading-text="Loading...">Proses</button>
							</div>
							<div class="col-sm-4">
								<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-print"></i></button>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</form>

	<div class="row">
		<!-- left -->
		<div class="col-sm-12">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><span class="total">0</span> ditemukan</h4>
				</div>
				<div class="grid-body no-border">
					
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th rowspan="2" class="text-middle">No.</th>
								<th rowspan="2" class="text-middle">No. PRQ</th>
								<th rowspan="2" class="text-middle">No. PO</th>
								<th rowspan="2" class="text-middle">No. GR</th>
								<th rowspan="2" class="text-middle">Supplier</th>
								<th rowspan="2" class="text-middle">Barang</th>
								<th rowspan="2" class="text-middle">Qty</th>
								<th rowspan="2" class="text-middle">Harga/item</th>
								<th colspan="2" class="text-center">PO (%)</th>
								<th colspan="2" class="text-center">Item (%)</th>
								<th rowspan="2" class="text-middle">Total</th>
								<th rowspan="2" class="text-middle">Deadline</th>
							</tr>

							<tr>
								<th>Disk</th>
								<th>PPN</th>
								<th>Disk</th>
								<th>PPN</th>
							</tr>
						</thead>

						<tbody class="item-po">
							<tr>
								<td colspan="14">Pencarian belum ditentukan!</td>
							</tr>
						</tbody>

					</table>
				</div>

				<div class="pagin text-right"></div>

				</div>
			</div>

		</div>
		
	</div>

@endsection