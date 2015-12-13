@extends('Master.Template')


@section('content')
	
	<div class="row">
		<div class="col-sm-12">

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<div class="row">
						<div class="col-sm-8">
							<h3>Hi, {{ Me::data()->nm_depan }} <span class="semi-bold">{{ Me::data()->nm_belakang }}</span></h3>
							<form method="post" action="{{ url('/users/account') }}">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="last_username" value="{{ Auth::user()->username }}">
								<div class="row">
									<div class="col-sm-11">

										<div class="form-group">
				                        	<label class="form-label">Nama Alias</label>
				                        	<span class="help">(Nama samaran)</span>
				                        	<div class="controls">
				                          	<input type="text" name="name" value="{{ Auth::user()->name }}" class="form-control" required>
				                        	</div>
				                      	</div>

				                      	<div class="form-group">
				                        	<label class="form-label">Status</label>
				                        	<span class="help">(Tentang perasaan anda)</span>
				                        	<div class="controls">
				                          	<input type="text" name="status_user" value="{{ Auth::user()->status_user }}" class="form-control" placeholder="Katakan sesuatu..." length="100">
				                        	</div>
				                      	</div>

				                      	<p>
				                      		<h3>Akses <span class="semi-bold">Login</span></h3>
				                      		Kosongkan Password jika Username atau Password tidak ada perubahan 
				                      	</p>
				                      	<div class="form-group">
				                        	<label class="form-label">Username</label>
				                        	<span class="help"></span>
				                        	<div class="controls">
				                          	<input type="text" name="username" value="{{ empty(old('username')) ? Auth::user()->username : old('username') }}" class="form-control" required>
				                        	</div>
				                      	</div>

				                      	<div class="form-group">
				                        	<label class="form-label">Password</label>
				                        	<span class="help"></span>
				                        	<div class="controls">
				                          	<input type="password" name="password" class="form-control">
				                        	</div>
				                      	</div>

				                      	<div class="form-group">
				                        	<label class="form-label">Konfirmasi</label>
				                        	<span class="help"></span>
				                        	<div class="controls">
				                          	<input type="password" name="password_confirmation" class="form-control">
				                        	</div>
				                      	</div>

				                      	<div class="form-group">
				                        	<label class="form-label"></label>
				                        	<span class="help"></span>
				                        	<div class="controls">
				                          		<button type="submit" class="btn btn-primary">Simpan Perubahan</button>
				                        	</div>
				                      	</div>

									</div>
								</div>
							</form>

						</div>
						<div class="col-sm-4">
							<a href="{{ url('/users/avatar') }}">
								<img src="{{ asset('/img/avatars/xl/' . Auth::user()->avatar) }}" class="img-thumbnail">
							</a>

							<div class="user-description-box" style="margin:30px 0 0 0;">
								<h4>Akses User <span class="semi-bold">{{ $permission[Auth::user()->permission] }}</span></h4>
								
								<h4>Levels User</h4>
								<ul style="padding-left:20px;">
								@foreach($levels as $level)
									<li>{{ $level->nm_level }}</li>
								@endforeach
								</ul>
							</div>

						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

@endsection