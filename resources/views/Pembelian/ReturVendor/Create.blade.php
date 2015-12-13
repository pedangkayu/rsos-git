@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){

			$('[type="number"]').change(function(){
				var val = $(this).val();
				var id = $(this).data('id');
				var max = $(this).data('max');
				if(val < 0 || val.length == 0){
					$(this).val(0);
				}

				if(max > 0 && val > max)
					$(this).val(max);

				if(val > 0){
					$('.select-' + id).attr('required', 'required');
					$('.ket-' + id).attr('required', 'required');
				}else{
					$('.select-' + id).removeAttr('required');
					$('.ket-' + id).removeAttr('required');
				}
			});

			getref = function(){
				$('.keterangan_retur').html('<option>Loading...</option>');
				$.getJSON(_base_url + '/returvendor/refretur', {}, function(json){
					$('.keterangan_retur').html(json.content);
				});
			}

			getref();

			$('.btn-add_retur').click(function(){
				var val = $('#add_retur').val();
				if(val.length > 0){
					$(this).button('loading');
					$.post(_base_url + '/returvendor/addketretur', { keterangan : val }, function(json){
						console.log(json);
						$('.btn-add_retur').button('reset');
						$('#add_retur').val('');
						getref();
					}, 'json');
				}
			});
		});
	</script>
@endsection

@section('title')
	Retur Pembelian
@endsection

@section('content')
	
	<form method="post" action="{{ url('/returvendor/cretereture/') }}">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="row">
			<!-- left -->
			<div class="col-sm-9">
				
				<div class="grid simple">
					<div class="grid-title no-border">
                        <span class="pull-right label label-important">{{ $status[$po->status] }}</span>
						<h4>{{ count($items) }} ditemukan</h4>
					</div>
					<div class="grid-body no-border">
						
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th width="25%">Barang</th>
										<th width="15%" class="text-right">Qty</th>
										<th width="15%" class="text-right">Qty Retur</th>
										<th width="20%" class="text-right">Jenis Retur</th>
										<th width="25%">Ket / Merek</th>
									</tr>
								</thead>

								<tbody>
									@foreach($items as $item)
									<tr>
										<td title="{{ $item->nm_barang }}">
											{{ Format::substr($item->nm_barang,20) }}<br />
											<small class="text-muted">{{ $item->kode }}</small>

											<input type="hidden" name="id_barang[]" value="{{ $item->id_item }}">
											<input type="hidden" name="id_satuan[]" value="{{ $item->id_satuan_default }}">
										</td>
										<td class="text-right">{{ number_format($item->req_qty,0,',','.') }} {{ $item->satuan_default }}</td>
										<td class="text-right">
											<input type="number" name="qty[]" data-id="{{ $item->id_po_item }}" data-max="{{ $po->status > 2 ? 0 : $item->req_qty }}" class="form-control text-right" required value="0">
										</td>
										<td>
											<select name="ket_retur[]" class="form-control keterangan_retur select-{{ $item->id_po_item }}">
												<option>Loading...</option>
											</select>
										</td>
										<td>
											<input type="text" name="kets[]" class="form-control ket-{{ $item->id_po_item }}">
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>

					</div>
				</div>

			</div>

			<!-- right -->
			<div class="col-sm-3">
				
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						
						<input type="hidden" name="id_po" value="{{ $po->id_po }}">
						<input type="hidden" name="id_vendor" value="{{ $po->id_vendor }}">

						<address>
							<strong>No. Purchase Order</strong>
							<p><h3>{{ $po->no_po }}</h3></p>
							<strong>Supplier</strong>
							<p>{{ $po->nm_vendor }}</p>
							<strong>Alamat</strong>
							<p>{{ $po->alamat }}</p>
							<strong>Telpon</strong>
							<p>{{ $po->telpon }}</p>
						</address>
						<strong class="text-danger">Keterangan :</strong>
						<p>
							Biarkan Qty <strong>NOL "0"</strong> untuk item yang tidak akan dikembalikan.
						</p>
					</div>
				</div>

				<div class="grid simple">
					<div class="grid-title no-border">
						<h4></h4>
						<div class="tools">
			          		<a href="javascript:;" class="collapse"></a> 
			          	</div>
					</div>
					<div class="grid-body no-border">

						<div class="form-group">
							<label for="add_retur">Tambah Jenis Retur</label>
							<div class="input-group">
						      	<input type="text" name="add_retur" id="add_retur" class="form-control" placeholder="e.g Rusak">
						      	<span class="input-group-btn">
						        	<button class="btn btn-white btn-add_retur" type="button" data-loading-text="<i class='fa fa-cog fa-spin'></i>"><i class="fa fa-plus"></i></button>
						      	</span>
						    </div><!-- /input-group -->
						    <small class="text-muted">* disimpan secara permanen</small>
						</div>

					</div>
				</div>

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						
						@if(Auth::user()->permission > 1)
						<div class="form-group">
							<label>
								<input type="checkbox" name="agree" required>
								Saya yakin data sudah benar.
							</label>
						</div>

						<button type="submit" class="btn btn-block btn-primary">Simpan</button>
						@endif
						<a href="{{ url('/returvendor/po') }}" class="btn btn-block btn-primary">Batal</a>

					</div>
				</div>

			</div>
			
		</div>

	</form>

@endsection
