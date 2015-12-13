@extends('Master.Template')

@section('csstop')
<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection
@section('meta')
<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{ asset('/js/akunting/fakturpembelian/baru.js') }}"></script>
<script type="text/javascript">
	$(function(){
		// auto load
		vendors({{ $faktur->id_vendor }});	
	});
</script>
@endsection

@section('title')
Faktur Pembelian
@endsection

@section('content')
<form method="post" action="{{ url('/fakturpembelian/edit') }}">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="id" value="{{ $faktur->id_faktur }}">
	<div class="row">
		<!-- left -->
		<div class="col-sm-12">

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<!-- Input Header -->
					<div class="row">
						<div class="col-sm-6">
                            <div class="form-group">
								<label for="supplier">Supplier *</label>
								<div class="input-group">
									<select name="supplier" id="supplier" style="width:100%;" required>
										<option value="">Memuat...</option> 
									</select>

								    <span class="input-group-btn">
								        <button class="btn btn-white" data-toggle="modal" data-target="#vendor" title="Tambahkan Supplier bila tidak ada"><i class="fa fa-plus"></i></button><!-- /input-data-supplier -->
								    </span>
							    </div>
							</div>

							<div class="form-group">
								<label for="alamat">Alamat *</label>
								<textarea class="form-control" name="alamat" rows="4" readonly="readonly"></textarea>
							</div>

							<div class="form-group">
								<label for="po">No. Purchase Order</label>
								<div class="input-group">
									<input type="text" name="no_po" value="{{ $faktur->nomor_type }}" class="form-control" readonly="readonly">
								      <span class="input-group-btn">
								        	<button class="btn btn-white dell-po" type="button" title="hapus PO"><i class="fa fa-trash"></i></button>
								      </span>
							    </div><!-- /input-group -->
								<!-- data Purchase Order -->
								<input type="hidden" name="id_po" value="0">
							</div>
						</div>

						<div class="col-sm-6">
							<div class="row form-row">
								<div class="col-md-6">
									<label for="no_faktur">Nomor Faktur *</label>
									<input name="no_faktur" id="no_faktur" value="{{ $faktur->nomor_faktur }}" type="text"  class="form-control" value="-" readonly="readonly">
								</div>
								<div class="col-md-6">
									<label for="prefix">Prefix Faktur</label>
									<input name="prefix" id="prefix" value="{{ $faktur->prefix }}" type="text"  class="form-control">
								</div>
							</div>

							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="tanggal">Tanggal Faktur *</label>
										<div class="input-group transparent">
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											<input type="text" value="{{ date('m/d/Y', strtotime($faktur->tgl_faktur)) }}" name="tanggal" id="tanggal" class="form-control" readonly="readonly">
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="duodate">Tanggal Jatuh Tempo *</label>
										<div class="input-group transparent">
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											<input type="text" value="{{ date('m/d/Y', strtotime($faktur->duodate)) }}" name="duodate" id="duodate" class="form-control" readonly="readonly">
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="diskon">Diskon (%)</label>
										<input type="number" name="diskon" value="{{ number_format($faktur->diskon,0,'','') }}" id="diskon" class="form-control text-right">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="terms">Payment Terms *</label>
										<select name="terms" id="terms" class="form-control">
											@foreach($terms as $term)
											<option value="{{ $term->id_payment_terms }}" {{ $term->id_payment_terms == $faktur->id_payment_terms ? 'selected="selected"' : ''}}>{{ $term->payment_terms }}</option>
											@endforeach
										</select>
									</div>		
								</div>
							</div>

							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="ppn">PPN (%)</label>
										<input type="number" name="ppn" id="ppn" value="{{ number_format($faktur->ppn,0,'','') }}" class="form-control text-right">
									</div>
								</div>
								<div class="col-sm-6">
									<!-- <div class="form-group">
										<label for="pph">PPh (%)</label>
										<input type="number" name="pph" id="pph" class="form-control text-right">
									</div> -->
								</div>
							</div>


						</div>
					</div>
					<!-- End Input Header -->
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th width="15%">Kode</th>
									<th width="35%" class="text-left">Barang</th>
									<th width="20%" class="text-right">Qty</th>
									<th width="10%" class="text-right">Disc (%)</th>
									<th width="10%" class="text-right">Harga</th>
									<th width="10%" class="text-right">Total</th>
								</tr>
							</thead>

							<tbody class="content-item">
								@foreach($items as $item)
								<tr {{ $item->id_po > 0 ? 'data-po=true' : 'onclick="id_delete(\'items-' . $item->id_faktur_item . '\');"' }} class="item-barang" data-item="{{ $item->id_faktur_item }}">
									<td>
										{{ $item->kode }}
										<input type="hidden" value="{{ $item->id_item }}" name="id_barang[]">
									</td>
									<td><input {{ $item->id_item > 0 ? 'readonly="readonly"' : '' }} type="text" name="deskripsi[]" value="{{ $item->deskripsi }}" class="form-control" required></td>
									<td>
										<div class="input-group input-group-sm">
											<input type="number" data-form="qty" value="{{ $item->qty }}" name="qty[]" class="form-control text-right" required>
										  	<span class="input-group-addon">{{ $item->nm_satuan }}</span>
										  	<input type="hidden" name="id_satuan[]" value="{{ $item->id_satuan }}" />
										</div>
									</td>
									<td><input type="number" data-form="diskons" value="{{ number_format($item->diskon,0,',','.') }}" name="diskons[]" class="form-control text-right" required></td>
									<td><input type="number" data-form="harga" value="{{ $item->harga }}" name="harga[]" class="form-control text-right" required></td>
									<td><input type="number" data-form="total" value="{{ $item->total }}" name="total[]" class="form-control text-right" readonly="readonly" required></td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					<!-- footer  -->
					<div class="row" style="padding:10px 0;">
						<div class="col-sm-7">
							<div class="form-group">
								<button type="button" class="btn btn-primary add-new-blank"><i class="fa fa-plus"></i> Tambah Item Faktur</button>
								<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#produks"><i class="fa fa-search"></i> Cari Produk & PO</button>
								<button type="button" class="btn btn-danger btn-hapus" style="display:none;"><i class="fa fa-trash"></i> Hapus</button>
								<input type="hidden" name="id_delete" value="0">
							</div>

							<div class="form-group">
								<label for="keterangan">Catatan :</label>
								<textarea name="keterangan" id="keterangan" class="form-control" rows="5">{{ $faktur->keterangan }}</textarea>
							</div>

						</div>
						<div class="col-sm-5">
							<?php 
								/* Matematika */
								$disikon = ($faktur->subtotal * $faktur->diskon) / 100;
								$aftdiskon = $faktur->subtotal - $disikon;
								$ppn = ($aftdiskon * $faktur->ppn) / 100;
							?>

							<table class="table table-striped">
								<tr>
									<td width="30%" class="text-right"><strong>Sub Total :</strong></td>
									<td width="70%" class="faktur-subtotal text-right">{{ number_format($faktur->subtotal,2,',','.') }}</td>
								</tr>
								<tr>
									<td class="text-right"><strong>Diskon :</strong></td>
									<td class="faktur-diskon text-right">{{ number_format($disikon,2,',','.') }}</td>
								</tr>
								<tr>
									<td class="text-right"><strong>PPN :</strong></td>
									<td class="faktur-ppn text-right">{{ number_format($ppn,2,',','.') }}</td>
								</tr>
								<!-- <tr>
									<td class="text-right"><strong>PPh :</strong></td>
									<td class="faktur-pph text-right">-</td>
								</tr> -->
								<tr valign="center">
									<td class="text-right"><strong>Adjustment :</strong></td>
									<td class="faktur-adjustment text-right">
										<input type="number" value="{{ number_format($faktur->adjustment,0,'','') }}" name="adjustment" class="form-control text-right">
									</td>
								</tr>
								<tr>
									<td class="text-right"><strong>Total :</strong></td>
									<td class="faktur-total text-right">{{ number_format($faktur->total,2,',','.') }}</td>
								</tr>
							</table>
							<input type="hidden" name="subtotal" value="{{ $faktur->subtotal }}">
							<input type="hidden" name="grandtotal" value="{{ $faktur->total }}">
						</div>
					</div>

					<div class="grid-footer">
						<div class="row">
							<div class="col-sm-2">
								<a href="{{ url('/fakturpembelian') }}" class="btn btn-default btn-block">Batal</a>
							</div>
							<div class="col-sm-offset-7 col-sm-3">
								<button class="btn btn-primary btn-block" type="submit">Simpan</button>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</form>
@endsection

@section('footer')
<!-- Modal -->
<div class="modal fade" id="produks" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Semua Produk & PO</h4>
			</div>
			<div class="modal-body">
				
				<ul class="nav nav-tabs" id="tab-4">
					<li class="active" data-toggle="link-tab"><a href="#items">Barang / Obat</a></li>
					<li data-toggle="link-tab"><a href="#po">Purchase Order (PO)</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="items">
						<div class="row">
							<div class="col-sm-4">
								<input type="text" name="modal-kode-item" class="form-control" placeholder="Kode Barang">
							</div>
							<div class="col-sm-6">
								<input type="text" name="modal-barang-item" class="form-control" placeholder="Nama Barang / Obat">
							</div>
							<div class="col-sm-2">
								<div class="btn-group">
									<button class="btn btn-white btn-search-item"><i class="fa fa-search"></i></button>
									<button title="Refresh" class="btn btn-white btn-search-item"><i class="fa fa-refresh"></i></button>
								</div>
							</div>
						</div>
						<br />
						<div>
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Kode</th>
										<th>Barang</th>
										<th></th>
									</tr>
								</thead>
								<tbody class="modal-items-list">
									<tr>
										<td colspan="3">Memuat...</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="modal-items-pagin text-center"></div>
					</div>

					<div class="tab-pane" id="po">
						<div class="row">
							<div class="col-sm-7">
								<input type="text" name="modal-no_po" class="form-control" placeholder="No. Purchase Order"><br />
							</div>
							<div class="col-sm-3">
								<select class="form-control" name="status-po">
									<option value="0">Semua Status</option>
									<option value="1">Baru</option>
									<option value="2">Proses</option>
									<option value="3">Selesai</option>
								</select>
							</div>
							<div class="col-sm-2">
								<div class="btn-group">
									<button class="btn btn-white btn-search-po"><i class="fa fa-search"></i></button>
									<button title="Refresh" class="btn btn-white btn-search-po"><i class="fa fa-refresh"></i></button>
								</div>
							</div>
						</div>
						
						<div>
							<table class="table table-striped">
								<thead>
									<tr>
										<th>No. PO</th>
										<th>Tanggal</th>
										<th>Status</th>
										<th></th>
									</tr>
								</thead>
								<tbody class="modal-po-list">
									<tr>
										<td colspan="4">Memuat...</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="modal-po-pagin text-center"></div>

					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
				<input type="hidden" name="home-tab" value="#items">
			</div>
		</div>
	</div>
</div>



<!-- Modal -->
<div class="modal fade" id="vendor" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Tambah Supplier</h4>
			</div>
			<div class="modal-body">
				<!-- content -->
				<div class="grid simple">
					<div class="grid-title no-border"><h4></h4></div>
					<div class="grid-body no-border">
						<div class="form-group">
							<label class="form-label" for="nm_vendor">Nama Penyedia *</label>
							<span class="help">e.g. "PT. Maju Mundur"</span>
							<div class="controls">
								<input type="text" class="form-control" name="nm_vendor" data-toggle="input" data-toggle="input" data-name="nm_vendor" id="nm_vendor" value="{{ old('nm_vendor') }}" required>
								<small class="text-muted">Mohon untuk tidak memasukan data vandor yang sudah ada!</small>
							</div>
						</div>

						<div class="form-group">
							<label class="form-label" for="pemilik">Nama Pemilik *</label>
							<span class="help">e.g. "Jhone Doe"</span>
							<div class="controls">
								<input type="text" class="form-control" data-toggle="input" data-name="nama_pemilik" name="nama_pemilik" id="pemilik" value="{{ old('nama_pemilik') }}" required>
							</div>
						</div>

						<div class="form-group">
							<label class="form-label" for="alamat">Alamat *</label>
							<span class="help"></span>
							<div class="controls">
								<textarea type="text" name="alamat-supplier" data-toggle="input" data-name="alamat" id="alamat" required class="form-control" rows="6">{{ old('alamat') }}</textarea>
								<small class="text-muted">* Alamat harus yang lengkap, cantumkan Kode POS</small>
							</div>
						</div>

						<div class="form-group">
							<label class="form-label" for="telpon">Telpon *</label>
							<span class="help">e.g. "022 754321 / 022 1234567"</span>
							<div class="controls">
								<input type="text" class="form-control" data-toggle="input" data-name="telpon" id="telpon" name="telpon" value="{{ old('telpon') }}" required>
							</div>
						</div>

						<div class="form-group">
							<label class="form-label" for="fax">Fax</label>
							<span class="help">e.g. "022 754321 / 022 1234567"</span>
							<div class="controls">
								<input type="text" class="form-control" data-toggle="input" data-name="fax" name="fax" id="fax" value="{{ old('fax') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="form-label" for="email">Email</label>
							<span class="help"></span>
							<div class="controls">
								<input type="email" class="form-control" data-toggle="input" data-name="email" name="email" id="email" value="{{ old('email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="form-label" for="website">Website</label>
							<span class="help"></span>
							<div class="controls">
								<input type="text" class="form-control" data-toggle="input" data-name="website" name="website" id="website" value="{{ old('website') }}">
							</div>
						</div>

					</div>
				</div>
				<!-- end content -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
				@if(Auth::user()->permission > 1)
				<button type="button" data-loading-text="Menyimpan..." class="btn btn-primary simpan-supplier">Simpan Data Vendor</button>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection