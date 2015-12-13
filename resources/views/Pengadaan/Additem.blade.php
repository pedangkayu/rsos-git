@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/logistik.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('[name="tipe"]').click(function(){
				var prop = $(this).prop('value');
				if(prop == 1){
					$('.opsi-obat').show();
					$('[name="id_klasifikasi"]').attr('required', 'required');
				}else{
					$('.opsi-obat').hide();
					$('[name="id_klasifikasi"]').removeAttr('required');
				}
			});


			/////////////////////////////////////////
			@if(count($akses) == 1)
				if('{{ $akses[0] }}' == '1'){
					$('.opsi-obat').show();
					$('[name="id_klasifikasi"]').attr('required', 'required');
				}else{
					$('.opsi-obat').hide();
					$('[name="id_klasifikasi"]').removeAttr('required');
				}
			@endif
		});
	</script>
@endsection

@section('title')
	Tambah Barang
@endsection

@section('content')
	<form method="post" action="">
		<div class="row">
			<div class="col-sm-8">

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<form method="post" action="">
							<input type="hidden" value="{{ csrf_token() }}" name="_token">
							
							<div class="form-group">
		                        <label for="nm_barang" class="form-label">Nama Barang *</label>
		                        <span class="help"></span>
		                        <div class="controls">
		                          <input type="text" class="form-control" name="nm_barang" id="nm_barang" required>
		                        </div>
		                    </div>

		                    <div class="form-group">
		                        <label for="id_kategori" class="form-label">Kategori *</label>
		                        <span class="help"></span>
		                        <div class="controls">
		                         	<select style="width:100%;" name="id_kategori" id="id_kategori" required>
		                          		<option value="">Pilih Kategori</option>
		                          		@foreach($kategoris as $kategori)
		                          			<option value="{{ $kategori->id_kategori }}">{{ $kategori->nm_kategori }}</option>
										@endforeach	                          		
		                          	</select>
		                        </div>
		                    </div>

		                    <div class="form-group">
		                        <label for="id_satuan" class="form-label">Satuan Terkecil *</label>
		                        <span class="help"></span>
		                        <div class="controls">
		                         	<select style="width:100%;" id="id_satuan" name="id_satuan" required>
		                          		<option value="">Pilih Satuan</option>
		                          		@foreach($satuan as $sat)
		                          			<option value="{{ $sat->id_satuan }}">{{ $sat->nm_satuan }}</option>
										@endforeach	                          		
		                          	</select>
		                          	<small>Ambilah satuan paling kecil Conto BOX dan Strip maka yang dipilih adalah Strip</small>
		                        </div>
		                    </div>

		                    @if(count($akses) > 1)
		                    <div class="form-group">
		                        <label for="stok_awal" class="form-label">Tipe Barang *</label>
		                        <span class="help"></span>
		                        <div class="controls">
		                          <div class="radio">
			                        <input id="obat" type="radio" name="tipe" value="1" checked="checked">
			                        <label for="obat">Obat-obatan</label>
			                        <input id="barang" type="radio" name="tipe" value="2">
			                        <label for="barang">Barang</label>
			                      </div>
		                        </div>
		                    </div>
		                    @else
		                    <input type="hidden" name="tipe" value="{{ count($akses) > 0 ? $akses[0] : 1 }}">
		                    @endif

		                    <div class="form-group opsi-obat">
		                        <label for="id_klasifikasi" class="form-label">Klasifikasi *</label>
		                        <span class="help"></span>
		                        <div class="controls">
		                         	<select style="width:100%;" id="id_klasifikasi" name="id_klasifikasi" required>
		                          		<option value="">Pilih Klasifikasi</option>
		                          		@foreach($klasifikasi as $kals)
		                          			<option value="{{ $kals->id_klasifikasi }}">{{ $kals->nm_klasifikasi }}</option>
										@endforeach	                          		
		                          	</select>
		                        </div>
		                    </div>

		                    <div class="row">
		                    	<div class="col-sm-6">
		                    		<div class="form-group">
				                        <label for="stok_awal" class="form-label">Stok Awal *</label>
				                        <span class="help"></span>
				                        <div class="controls">
				                          <input type="number" class="form-control text-right" name="stok_awal" id="stok_awal" value="0" required>
				                        </div>
				                    </div>
		                    	</div>

		                    	<div class="col-sm-6">
		                    		<div class="form-group">
				                        <label for="stok_minimal" class="form-label">Stok Minimal *</label>
				                        <span class="help"></span>
				                        <div class="controls">
				                          <input type="number" class="form-control text-right" name="stok_minimal" value="0" id="stok_minimal" required>
				                        </div>
				                    </div>
		                    	</div>
		                    </div>

		                    <div class="row opsi-obat">
		                    	<div class="col-sm-6">
		                    		<div class="form-group">
				                        <label for="harga_beli" class="form-label">Harga Beli / Item *</label>
				                        <span class="help"></span>
				                        <div class="controls">
				                          <input type="number" class="form-control text-right" name="harga_beli" id="harga_beli" value="0" required>
				                        </div>
				                    </div>
		                    	</div>

		                    	<div class="col-sm-6">
		                    		<div class="form-group">
				                        <label for="harga_jual" class="form-label">Harga Jual / Item *</label>
				                        <span class="help"></span>
				                        <div class="controls">
				                          <input type="number" class="form-control text-right" name="harga_jual" id="harga_jual" value="0" required>
				                        </div>
				                    </div>
		                    	</div>
		                    </div>

		                    <div class="row">
		                    	<div class="col-sm-6">
		                    		<div class="form-group">
		                    			<label for="ppn" class="form-label">PPn *</label>
		                    			<span class="help"></span>
		                    		 	<div class="input-group transparent" style="margin-top:8px;">
		                    		 		<span class="input-group-addon">
												&nbsp;
										  	</span>
										  	<input class="form-control input-sm text-right" data-number="true" type="number" id="ppn" name="ppn" pattern="^\d*\.?\d*$" step="0.01" value="0">
										  	<span class="input-group-addon">
												% &nbsp;&nbsp;
										  	</span>
										</div>
									</div>
		                    	</div>
		                    </div>

		                    <div class="form-group">
		                        <label for="stok_awal" class="form-label">Keterangan</label>
		                        <span class="help"></span>
		                        <div class="controls">
		                          <textarea name="keterangan" style="resize:none;" class="form-control" rows="5">{{ old('keterangan') }}</textarea>
		                        </div>
		                    </div>

						</form>
					</div>
				</div>


				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">

						<h3>Detail <span class="semi-bold">Barang</span></h3>
						<p>
							Tambahkan beberapa detail yang mungkin bisa berguna sebagai informasi untuk barang tersebut<br />
							<span class="semi-bold">CONTOH PENGISIAN</span>
							<ul>
								<li><strong>Label</strong> diisi dengan judul detail seperti contoh Merek/Satuan/Kategori</li>
								<li><strong>Value</strong> diisi dengan nama dari judul tersebut jika Label berisi Merek maka nama Mereknya apa? contoh Betadine</li>
							</ul>
							Klik tombol <strong>+ Tambah Detail</strong> di bawah ini.
						</p>
						<br />
						<div class="detail-items"></div>
						<div class="text-right">
							<button type="button" class="btn btn-default add-detail"><i class="fa fa-plus"></i> Tambah Detail</button>
						</div>
					</div>
				</div>


			</div>

			<div class="col-sm-4">
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>Ket <span class="semi-bold">Pembuatan</span></h4>
						<div class="tools">
			          		<a href="javascript:;" class="collapse"></a>
			          	</div>
					</div>
					<div class="grid-body no-border">
						<address>
							<strong>Dibuat Oleh</strong>
							<p>{{ Me::fullName() }}</p>
							<strong>Tanggal</strong>
							<p>{{ Format::indoDate(date('Y-m-d')) }}</p>
						</address>
						<br />
						<p class="text-danger">
							Tanda (*) wajib diisi!
						</p>
					</div>
				</div>

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						@if(Auth::user()->permission > 1)
						<div class="form-group">
							<button class="btn btn-block btn-primary" type="submit">Simpan</button>
						</div>
						@endif
						<div class="row">
							<div class="col-xs-6">
								<button class="btn btn-block btn-info reset" type="button">Reset Form</button>
							</div>
							<div class="col-xs-6">
								<a class="btn btn-block btn-default" href="{{ url('/logistik') }}">Kembali</a>
							</div>
						</div>
					</div>
				</div>

				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>Konversi <strong>Satuan</strong></h4>
					</div>
					<div class="grid-body no-border">
						<p>Pilih satuan paling besar untuk di konversi dengan satuan terkecil yang telah anda tentukan sebelumnya!</p>
						<p><strong>Catatan : </strong> Jangan dipilih jika tidak digunakan</p>
						<div class="satuan_besar">
							<div class="form-group master-konversi">
								<div class="row">
									<div class="col-sm-6">
										<select style="width:100%;" id="koversi_satuan" name="koversi_satuan[]">
			                          		<option value="0">Pilih Satuan</option>
			                          		@foreach($satuan as $sat)
			                          			<option value="{{ $sat->id_satuan }}">1 {{ $sat->nm_satuan }}</option>
											@endforeach	                          		
			                          	</select>
									</div>
									<div class="col-sm-6">
										<input type="number" value="" class="form-control text-right" placeholder="Qty" name="koversi_qty[]">
									</div>
								</div>
							</div>

							<div class="add-konversi"></div>

							<div class="form-group">
								<button class="btn btn-primary btn-block tabah-konversi" type="button"><i class="fa fa-plus"></i> Tambah</button>
							</div>

						</div>
					</div>
				</div>

			</div>
		</div>
	</form>
@endsection