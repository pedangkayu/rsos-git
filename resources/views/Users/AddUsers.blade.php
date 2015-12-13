@extends('Master.Template')

@section('meta')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>

	<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>

	<script type="text/javascript">
		$(function(){
			$('#source').select2({
				placeholder: "Pilih nama karyawan..."
			});
			$('#multis').select2({
				placeholder: "Pilih Levels..."
			});
		});
	</script>
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<form action="" method="post">
				<input type="hidden" value="{{ csrf_token() }}" name="_token">
				<div class="grid simple">
					<div class="grid-title no-border">
						<h3>Tambah <span class="semi-bold">Pengguna</span></h3>
						<hr />
					</div>
					<div class="grid-body no-border">
						<p>Pastikan nama yang akan di daftarkan sebagai pengguna sudah masuk kedalam data Personalia.</p>
		             	<div class="form-group">
	                    	<label class="form-label">Nama Karyawan *</label>
	                    	<span class="help"></span>
	                    	<div class="controls">
	                      		<select id="source" name="karyawan" style="width:100%;" required>
				             		<option value="">Pilih</option>
				             		@foreach($stafs as $staf)
				             			<option value="{{ $staf->id_karyawan }}" {{ old('karyawan') == $staf->id_karyawan ? 'selected="selected"' : '' }}>{{ $staf->nm_depan }} {{ $staf->nm_belakang }}</option>
				             		@endforeach
				             	</select>
	                    	</div>
	                  	</div>

	                  	<div class="form-group">
	                    	<label class="form-label">Username *</label>
	                    	<span class="help"></span>
	                    	<div class="controls">
	                      		<input type="text" name="username" value="{{ old('username') }}" class="form-control" required>
	                    	</div>
	                  	</div>

	                  	<div class="form-group">
	                    	<label class="form-label">Password *</label>
	                    	<span class="help"></span>
	                    	<div class="controls">
	                      		<input type="password" name="password" class="form-control" required>
	                    	</div>
	                  	</div>

	                  	<div class="form-group">
	                    	<label class="form-label">Permission *</label>
	                    	<span class="help"></span>
	                    	<div class="controls">
	                      		<div class="radio radio-success">
			                        <input id="read" type="radio" name="permission" value="1" checked="checked">
			                        <label for="read">Read</label>
			                        <input id="write" type="radio" name="permission" value="2">
			                        <label for="write">Write</label>
			                        <input id="execute" type="radio" name="permission" value="3">
			                        <label for="execute">Execute</label>
			                     </div>
	                    	</div>
	                  	</div>

	                  	<div class="form-group">
	                    	<label class="form-label">Levels *</label>
	                    	<span class="help"></span>
	                    	<div class="controls">
	                      		<select id="multis" style="width:100%" multiple required name="levels[]">
				                    @foreach($levels as $level)
				                    	<option value="{{ $level->id_level_user }}">{{ $level->nm_level }}</option>
				                    @endforeach
				                </select>
	                    	</div>
	                  	</div>

	                  	<hr />
	                  	<div class="text-right">
		                  	@if(Auth::user()->permission == 3)
		                  	<button class="btn btn-primary pull-left" type="submit">Simpan</button>
		                  	@endif
		                  	<a href="{{ url('/users') }}" class="btn btn-link">Batal</a>
	                  	</div>
		            </div>

				</div>
			</form>
		</div>
	</div>

@endsection