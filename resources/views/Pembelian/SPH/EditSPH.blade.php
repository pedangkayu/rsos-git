@extends('Master.Template')
@section('csstop')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection
@section('meta')
	<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/js/modpembelian/pembelian.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('[data-number="true"]').change(function(){
				var val = $(this).val();
				if(val < 0)
					$(this).val(0);
			});

			$('.tgl').datepicker({
				format : 'yyyy-mm-dd'
			});

			$('.simpan').click(function(){

				var vendor = $('[name="vendor"]').val();

				if(vendor.length > 0){
					swal({   
						title: "Anda yakin ?",   
						text: "Anda yakin dengan data yang Anda masukan ?",   
						type: "warning",   
						showCancelButton: true,   
						confirmButtonColor: "#DD6B55",   
						confirmButtonText: "Yes",   
						closeOnConfirm: true
					}, function(){
						$('#simpan').submit();
					});
				}else{
					swal('OOps!','Penyedia tidak boleh kosong!');
					$('[name="vendor"]').focus();
				}
				
			});

			$.getJSON(_base_url + '/sph/vendors', {

				idselect : {!! $sph->id_vendor !!}

			}, function(json){
				$('[name="vendor"]').html(json.content);
				$('[name="vendor"]').select2();
			});

			$('[name="vendor"]').change(function(){

				var $id = $(this).val();
				var $ids = {!! $ids !!};

				if($id > 0){
					$('.btn-proses').hide();
					$('input').attr('disabled', 'disabled');
					$('.list-harga').html('');
					$.getJSON(_base_url + '/sph/lastprice', {

						id : $id,
						id_barang : $ids
					}, function(json){
						$('input').removeAttr('disabled');
						if(json.count > 0)
							for(var i = 0; i < json.content.length;i++){
								$('.harga-' + json.content[i].id).val(json.content[i].harga);
								$('.diskon-' + json.content[i].id).val(json.content[i].diskon);
							}
						else
							for(var i = 0; i < $ids.length;i++){
								$('.harga-' + $ids[i]).val(0);
								$('.diskon-' + $ids[i]).val(0);
							}

						// LIST HARGA
						for(var i = 0; i < $ids.length;i++){
							$('.last-harga-' + $ids[i]).html('<button class="btn btn-primary btn-mini btn-lg" data-toggle="modal" onclick="logharga(' + $id + ', ' + $ids[i] + ',1);" data-target="#myModal">Log Harga</button>');
						}		

						$('.btn-proses').show();
					});

				}else{
					$('.list-harga').html('');
					for(var i = 0; i < $ids.length;i++){
						$('.harga-' + $ids[i]).val(0);
						$('.diskon-' + $ids[i]).val(0);
					}
				}

			});

		});
	</script>
@endsection

@section('title')
	Edit No. {{ $sph->no_sph_item }}
@endsection

@section('content')
	<form method="post" action="{{ url('/sph/edit') }}" id="simpan">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="id" value="{{ $id }}">

		<div class="row">
			<div class="col-sm-9">
				
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>{{ count($items) }} barang <strong>ditemukan</strong></h4>
					</div>
					<div class="grid-body no-border">
						
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th width="40%">Keterangan</th>
									<th width="20%" class="text-right">Qty</th>
									<th width="40%" class="text-center">Opsi</th>
								</tr>
							</thead>

							<tbody>
								@forelse($items as $item)
									<tr title="{{ $item->nm_barang }}">
										<td>
											<h4 class="semi-bold">{{ $item->nm_barang }}</h4>
											<strong>{{ $item->kode }}</strong><br />
											<small class="text-muted">{{ $item->no_prq }}</small>
											<small class="text-muted">Satuan : {{ $item->default_satuan }} = </small>
											<small class="text-muted">1 {{ $item->nm_satuan }} = {{ Format::convertSatuan($item->id_barang, $item->id_satuan, $item->default_id_satuan) }} {{ $item->default_satuan }}</small><br />
											<span class="text-muted list-harga last-harga-{{ $item->id_barang }}">
											<button class="btn btn-primary btn-mini btn-lg" data-toggle="modal" onclick="logharga({{ $sph->id_vendor }}, {{ $item->id_barang }},1);" data-target="#myModal">Log Harga</button>
											</span>
											<input type="hidden" name="id_sph_item[]" value="{{ $item->id_sph_item }}">
											<input type="hidden" name="id_prq[]" value="{{ $item->id_prq }}">
											<input type="hidden" name="id_barang[]" value="{{ $item->id_barang }}">
											<input type="hidden" name="qty[]" value="{{ $item->qty }}">
											<input type="hidden" name="satuan[]" value="{{ $item->id_satuan }}">

										</td>
										<td class="text-right">{{ $item->qty }} {{ $item->nm_satuan }}</td>
										<td>
											<div class="input-group transparent">
											 	<span class="input-group-addon">
													<small>Harga/{{ $item->nm_satuan }}.</small>
											  	</span>
											  	<input class="form-control input-sm text-right harga-{{ $item->id_barang }}" data-number="true" type="number" name="harga[]" value="{{ (INT) $item->harga }}" title="Harga / {{ $item->nm_satuan }}">
											</div>

											<div class="input-group transparent">
											 	<span class="input-group-addon">
													<small>Disc.</small>
											  	</span>
											  	<input class="form-control input-sm text-right diskon-{{ $item->id_barang }}" data-number="true" type="number" name="diskon[]" value="{{ $item->diskon }}" title="Diskon / {{ $item->nm_satuan }}">
											  	<span class="input-group-addon">
													% &nbsp;&nbsp;
											  	</span>
											</div>

											<!-- <div class="input-group transparent">
											 	<span class="input-group-addon">
													<small>PPN.</small>
											  	</span>
											  	<input class="form-control input-sm text-right" data-number="true" type="number" name="ppn[]" pattern="^\d*\.?\d*$" step="0.01" value="{{ $item->ppn }}" title="PPN / {{ $item->nm_satuan }}">
											  	<span class="input-group-addon">
													% &nbsp;&nbsp;
											  	</span>
											</div>
											
											<div class="input-group transparent">
											 	<span class="input-group-addon">
													<small>PPH.</small>
											  	</span>
											  	<input class="form-control input-sm text-right" data-number="true" type="number" name="pph[]" pattern="^\d*\.?\d*$" step="0.01" value="{{ $item->pph }}" title="PPH / {{ $item->nm_satuan }}">
											  	<span class="input-group-addon">
													% &nbsp;&nbsp;
											  	</span>
											</div> -->

											<div class="form-group">
												<label><small>Keterangan :</small></label>
												<input type="text" name="kets[]" class="form-control" value="{{ $item->keterangan }}">
											</div>

										</td>
									</tr>
								@empty
									<tr>
										<td class="5">Tidak ditemukan</td>
									</tr>
								@endforelse
							</tbody>
						</table>

					</div>

					</div>
				</div>

			</div>
			<div class="col-sm-3">
				
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						
						<div class="form-group">
							<label for="gdiskon">Diskon Global</label>
							<div class="input-group transparent">
							 	<span class="input-group-addon"> </span>
							  	<input data-number="true" type="number" name="gdiskon" id="gdiskon" class="form-control text-right" title="Diskon keseluruhan item" value="{{ (INT) $sph->diskon }}">
							  	<span class="input-group-addon">
									% &nbsp;&nbsp;
							  	</span>
							</div>
						</div>

						<div class="form-group">
							<label for="gppn">PPN</label>
							<div class="input-group transparent">
							 	<span class="input-group-addon"></span>
							  	<input data-number="true" type="number" name="gppn" id="gppn" class="form-control text-right" title="PPN keseluruhan item" value="{{ $sph->ppn }}" pattern="^\d*\.?\d*$" step="0.01">
							  	<span class="input-group-addon">
									% &nbsp;&nbsp;
							  	</span>
							</div>
						</div>
						
						<!-- <div class="form-group">
							<label for="gpph">PPH</label>
							<div class="input-group transparent">
							 	<span class="input-group-addon"></span>
							  	<input data-number="true" type="number" name="gpph" id="gpph" class="form-control text-right" title="PPH keseluruhan item" value="{{ $sph->pph }}" pattern="^\d*\.?\d*$" step="0.01">
							  	<span class="input-group-addon">
									% &nbsp;&nbsp;
							  	</span>
							</div>
						</div> -->

						<div class="form-group">
							<label for="adjustment">Adjustment</label>
							<div class="input-group transparent">
							 	<span class="input-group-addon">
							 		<sup>-</sup>/<sub>+</sub>
							 	</span>
							  	<input type="number" name="adjustment" id="adjustment" class="form-control text-right" title="Penyesuaian" value="{{ $sph->adjustment }}" pattern="^\d*\.?\d*$" step="0.01">
							</div>
						</div>

					</div>
				</div>

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						
						<div class="form-group">
							<label for="penyedia">Penyedia</label>
							<select style="width:100%;" name="vendor" id="penyedia" required>
								<option value="">Loading...</option>
							</select>
						</div>

						<div class="form-group">
							<label><small>Catatan :</small></label>
							<textarea name="ket" class="form-control">{{ $sph->keterangan }}</textarea>
						</div>

						<div class="form-group">
							<label for="deadline">Deadline</label>
							<div class="input-group">
						      <input type="text" class="form-control tgl" name="deadline" id="deadline" readonly="readonly" value="{{ $sph->deadline }}">
						    </div><!-- /input-group -->
						</div>

						<div class="form-group btn-proses">
							@if(Auth::user()->permission > 1)
							<button type="button" class="btn btn-block btn-primary simpan">Simpan</button>
							@endif
							<a class="btn btn-block btn-primary" href="{{ url('/sph/review/' . $sph->id_sph_grup) }}">Kembali</a>
						</div>
						
						
					</div>
				</div>

			</div>
		</div>
	</form>

@endsection

@section('footer')
	
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	        <h4 class="modal-title" id="myModalLabel">Kode. <span class="harga-kode"></span></h4>
	      </div>
	      <div class="modal-body">
	        
	        <div class="grid simple">
	        	<div class="grid-title no-border">
	        		<h4 class="harga-head"></h4>
	        	</div>
	        	<div class="grid-body no-border">
	        		<div class="table-responsive harga-body">Memuat</div>
	        		<div class="harga-pagin text-right"></div>
	        	</div>
	        </div>


	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
	      </div>
	    </div>
	  </div>
	</div>

@stop