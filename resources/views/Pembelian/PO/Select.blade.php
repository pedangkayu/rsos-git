@extends('Master.Template')


@section('csstop')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection

@section('meta')
	<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/js/modpembelian/po/create.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('.tgl').datepicker({
				format : 'yyyy-mm-dd'
			});

			$('.btn-tanggal').click(function(){
				$('[name="tanggal"]').val('');
			});

			$('.btn-deadline').click(function(){
				$('[name="deadline"]').val('');
			});

			$('.selected-item').slimscroll({
				height : '200px',
				alwaysVisible: true
			});

			$.getJSON(_base_url + '/sph/vendors', {}, function(json){
				$('[name="vendor"]').html(json.content);
				$('[name="vendor"]').select2();
			});

			$('[name="titipan"]').click(function(){
				var status = $(this).prop('checked');
				if(status == true){
					$('.suplier-titipan').show();
					dellall();
					getprq(1);
				}else{
					$('.suplier-titipan').hide();
					dellall();
				}
			});

		});
	</script>
	<style type="text/css">
		.suplier-titipan{
			display: none;
		}
	</style>
@endsection

@section('title')
	Pilih Barang
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><span class="total-find">0</span> pengajuan <b>ditemukan</b></h4>
					<div class="tools">
						<a href="javascript:getprq(1);" class="reload"></a>
					</div>
				</div>
				<div class="grid-body no-border">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="35%">Barang</th>
									<th width="15%" class="text-right">qty</th>
									<th width="20%">No. Pengajuan</th>
									<th width="20%">Deadline</th>
									<th width="10%"></th>
								</tr>
							</thead>

							<tbody class="item-prq">
								<tr>
									<td colspan="5">Memuat...</td>
								</tr>
							</tbody>

						</table>

						<div class="text-right item-pagin"> </div>
					</div>
				</div>
			</div>

		</div>
		<div class="col-sm-3">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><span class="select-total">0</span> terpilih</h4>
					<button type="button" class="btn btn-white dellAll pull-right btn-mini btn-xs" type="button" title="Hapus Semua Pilihan" data-toggle="tooltip" data-placement="left" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>"><i class="fa fa-trash"></i></button>
				</div>
				<div class="grid-body no-border">
					<div class="selected-item"></div>
					<br />
					<div class="form-group btn-po hide">
						<a href="{{ url('/po/create') }}" class="btn btn-block btn-primary">Buat PO</a>
					</div>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="form-group">
						<div class="checkbox check-info">
							<input type="checkbox" name="titipan" id="titipan">
							<label for="titipan">Barang Titipan</label>
						</div>
					</div>	

					<div class="form-group suplier-titipan">
						<label for="penyedia">Penyedia</label>
						<select style="width:100%;" name="vendor" id="penyedia" required onchange="getprq(1);">
							<option value="">Loading...</option>
						</select>
					</div>

					<div class="form-group">
						<label for="barang">Barang</label>
						<input type="text" name="barang" id="barang" class="form-control">
					</div>

					<div class="form-group">
						<label for="no_prq">No. Pengajuan</label>
						<input type="text" name="no_prq" id="no_prq" class="form-control">
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
						<label for="tanggal">tanggal</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="tanggal" id="tanggal" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-tanggal" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="status">Status</label>
						<select name="status" id="status" class="form-control">
							<option value="0">Semua</option>
							<option value="1">Baru</option>
							<option value="2">Proses</option>
							<option value="3">Selesai</option>
						</select>
					</div>

					<div class="form-group">
						<label for="limit">Limit / Page</label>
						<select name="limit" id="limit" class="form-control">
							<option value="5">5</option>
							<option value="10" selected="selected">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
					</div>

					<div class="form-group">
						<button class="btn btn-block btn-primary btn-cari-prq"><i class="fa fa-search"></i> Cari</button>
						<a href="{{ url('/po') }}" class="btn btn-primary btn-block">Batal</a>
					</div>

				</div>
			</div>

		</div>
	</div>

@endsection

