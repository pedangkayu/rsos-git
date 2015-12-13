<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta charset="utf-8" />
<title>RS.ONKOLOGI SURABAYA</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta content="" name="description" />
<meta content="" name="author" />
<!-- BEGIN CORE CSS FRAMEWORK -->
<link href="{{ asset('/plugins/pace/pace-theme-flash.css') }}" rel="stylesheet" type="text/css" media="screen"/>
<link href="{{ asset('/plugins/boostrapv3/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/plugins/boostrapv3/css/bootstrap-theme.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/plugins/font-awesome/css/font-awesome.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/css/animate.min.css') }}" rel="stylesheet" type="text/css"/>
<!-- END CORE CSS FRAMEWORK -->
<!-- BEGIN CSS TEMPLATE -->
<link href="{{ asset('/css/style.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/css/responsive.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/css/custom-icon-set.css') }}" rel="stylesheet" type="text/css"/>
<!-- END CSS TEMPLATE -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="error-body no-top">
<div class="container">
  <div class="row login-container">  
        <div class="col-md-5 col-md-offset-1">
          	<img src="{{ asset('/img/login1.png') }}" class="img-responsive">
        </div>

        <div class="col-md-5 "> <br>
		 <form id="login-form" class="login-form" action="{{ url('/auth/login') }}" method="post">
     			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			@if (count($errors) > 0)
				<div class="alert alert-danger col-md-10">
				  <strong>Ups!</strong>  Username / Password anda tidak sesuai.<br><br>
				  <ul>
					@foreach ($errors->all() as $error)
					  <li>{{ $error }}</li>
					@endforeach
				  </ul>
				</div>
			@endif

			 <div class="row">
				 <div class="form-group col-md-11">
						<div class="controls">
							<div class="input-with-icon  right">                                       
								<i class=""></i>
								<input name="username" value="{{ old('username') }}" id="login_username" type="text"  class="form-control" placeholder="Username">
							</div>
						</div>
				  </div>
			  </div>

			  <div class="row">
				  <div class="form-group col-md-11">
						<span class="help"></span>
						<div class="controls">
							<div class="input-with-icon  right">                                       
								<i class=""></i>
								<input name="password" id="login_pass" type="password"  class="form-control" placeholder="Password">
							</div>
						</div>
				  </div>
			  </div>

			  <div class="row">
				  <div class="control-group col-md-11">
						<div class="checkbox checkbox check-success"> <!-- <a href="#">Trouble login in?</a>&nbsp;&nbsp; -->
						  <input type="checkbox" id="checkbox1" name="remember" value="1">
						  <label for="checkbox1">Keep me reminded </label>
						</div>
				  </div>
			  </div>

			  <div class="row">
					<div class="col-md-11">
					  <button class="btn btn-primary btn-cons pull-right" type="submit">Login</button>
					</div>
			  </div>
		  </form>
        </div>

	    <div class="row">
			<div class="col-md-11">
               <center>
			   <p style="margin-top: 80px;">&copy; 2015 &middot; RS ONKOLOGI SURABAYA &middot; ICT Division &middot; All right reserved &middot;  <a href="{{ url('/vacancy/list/') }}">Lowongan Kerja</a></p>
			   <small>only for internal purposes RS. ONKOLOGI SURABAYA. all activities and transaction data will be stored in our database</small>
			   </center>
			</div>
	    </div>

  </div>
</div>
<!-- END CONTAINER -->
<!-- BEGIN CORE JS FRAMEWORK-->
<script src="{{ asset('/plugins/jquery-1.8.3.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/plugins/pace/pace.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/login.js') }}" type="text/javascript"></script>
<!-- BEGIN CORE TEMPLATE JS -->
<!-- END CORE TEMPLATE JS -->
</body>
</html>