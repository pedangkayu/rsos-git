<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<meta charset="utf-8" />
	<title>RS Onkologi</title>
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta content="" name="description" />
	<meta content="" name="author" />

	<link href="{{ asset('/plugins/pace/pace-theme-flash.css') }}" rel="stylesheet" type="text/css" media="screen"/>
	<link href="{{ asset('/plugins/jquery-slider/css/jquery.sidr.light.css') }}" rel="stylesheet" type="text/css" media="screen"/>
	<!-- BEGIN CORE CSS FRAMEWORK -->
	<link href="{{ asset('/plugins/boostrapv3/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('/plugins/boostrapv3/css/bootstrap-theme.min.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('/plugins/font-awesome/css/font-awesome.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('/css/animate.min.css') }}" rel="stylesheet" type="text/css"/>
	<!-- END CORE CSS FRAMEWORK -->

	<!-- BEGIN CSS TEMPLATE -->
	<link href="{{ asset('/css/style.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('/css/responsive.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('/css/custom-icon-set.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('/css/update.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('/plugins/bootstrap-datepicker/css/datepicker.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/plugins/bootstrap-timepicker/css/bootstrap-timepicker.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css')}}" rel="stylesheet" type="text/css" />
	<!-- END CSS TEMPLATE -->


	<!-- BEGIN CORE JS FRAMEWORK--> 
	<script src="{{ asset('/plugins/jquery-1.8.3.min.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('/plugins/breakpoints.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('/plugins/jquery-unveil/jquery.unveil.min.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('/plugins/jquery-block-ui/jqueryblockui.js') }}" type="text/javascript"></script> 
	<!-- END CORE JS FRAMEWORK --> 
	<!-- BEGIN PAGE LEVEL JS --> 	
	<script src="{{ asset('/plugins/jquery-slider/jquery.sidr.min.js') }}" type="text/javascript"></script> 	
	<script src="{{ asset('/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('/plugins/pace/pace.min.js') }}" type="text/javascript"></script>  
	<script src="{{ asset('/plugins/jquery-numberAnimate/jquery.animateNumbers.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset ('/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
	<script src="{{ asset ('/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
	<!-- END PAGE LEVEL PLUGINS --> 	

	<!-- BEGIN CORE TEMPLATE JS --> 
	<script src="{{ asset('/js/core.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('/js/chat.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('/js/demo.js') }}" type="text/javascript"></script>

	<!-- END CORE TEMPLATE JS --> 


	<!-- Penambahan -->
	@yield('meta')

</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="vacancy-list">
	<div class="vacancy-list">
		<div>
			<a href="{{ url('/') }}"><img src="{{ asset('/img/login2.png') }}" class="img-responsive"></a>
		</div>
		<div class="container">
			<div class="row column-seperation">  
				@yield('content')
			</div>
		</div>
	</div>

	<!-- END CONTAINER -->
	<!-- END CORE TEMPLATE JS -->
	<script type="text/javascript">
		var _base_url = '{{ url() }}';
	</script>
</body>
</html>