@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){
			$('[name="nm_vendor"]').focus();
		});
	</script>
@endsection

@section('title')
	Tambah Penyedia
@endsection

@section('content')
	<div class="row">
		<div class="col-sm-8">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
				<form method="post" action="{{ url('/vendor/add') }}">
					<input type="hidden" value="{{ csrf_token() }}" name="_token">
					
					<div class="form-group">
	                    <label class="form-label" for="nm_vendor">Nama Penyedia *</label>
	                    <span class="help">e.g. "PT. Maju Mundur"</span>
	                    <div class="controls">
	                     	<input type="text" class="form-control" name="nm_vendor" id="nm_vendor" value="{{ old('nm_vendor') }}" required>
	                     	<small class="text-muted">Mohon untuk tidak memasukan data vandor yang sudah ada!</small>
	                    </div>
	                </div>

	                <div class="form-group">
	                    <label class="form-label" for="pemilik">Nama Pemilik *</label>
	                    <span class="help">e.g. "Jhone Doe"</span>
	                    <div class="controls">
	                     	<input type="text" class="form-control" name="nama_pemilik" id="pemilik" value="{{ old('nama_pemilik') }}" required>
	                    </div>
	                </div>

	                <div class="form-group">
	                    <label class="form-label" for="alamat">Alamat *</label>
	                    <span class="help"></span>
	                    <div class="controls">
	                     	<textarea type="text" name="alamat" id="alamat" required class="form-control" rows="6">{{ old('alamat') }}</textarea>
	                     	<small class="text-muted">* Alamat harus yang lengkap, cantumkan Kode POS</small>
	                    </div>
	                </div>

	                <div class="form-group">
	                    <label class="form-label" for="telpon">Telpon *</label>
	                    <span class="help">e.g. "022 754321 / 022 1234567"</span>
	                    <div class="controls">
	                     	<input type="text" class="form-control" id="telpon" name="telpon" value="{{ old('telpon') }}" required>
	                    </div>
	                </div>

	                <div class="form-group">
	                    <label class="form-label" for="fax">Fax</label>
	                    <span class="help">e.g. "022 754321 / 022 1234567"</span>
	                    <div class="controls">
	                     	<input type="text" class="form-control" name="fax" id="fax" value="{{ old('fax') }}">
	                    </div>
	                </div>

	                <div class="form-group">
	                    <label class="form-label" for="email">Email</label>
	                    <span class="help"></span>
	                    <div class="controls">
	                     	<input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}">
	                    </div>
	                </div>

	                <div class="form-group">
	                    <label class="form-label" for="website">Website</label>
	                    <span class="help"></span>
	                    <div class="controls">
	                     	<input type="text" class="form-control" name="website" id="website" value="{{ old('website') }}">
	                    </div>
	                </div>

	                <div class="text-right">
		                <div class="form-group">
		                	<a href="{{ url('/vendor') }}" class="btn btn-default pull-left">Batal</a>
		                	@if(Auth::user()->permission > 1)
		                	<button type="submit" class="btn btn-primary">Simpan Data Vendor</button>
		                	@endif
		                </div>
	                </div>

				</form>

				</div>
			</div>	

		</div>
		<div class="col-sm-4">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<address>
						<strong>Oleh</strong>
						<p>{{ Me::fullname() }}</p>
						<strong>Tanggal</strong>
						<p>{{ Format::indoDate(date('Y-m-d')) }}</p>
					</address>

					<p class="text-danger">(*) wajib diisi!</p>
				</div>
			</div>	

		</div>
	</div>
@endsection