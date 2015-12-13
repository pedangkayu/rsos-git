<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<meta charset="utf-8" />
	<title>RUMAH SAKIT ONKOLOGI</title>
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="shortcut icon" href="{{ asset('/favicon.ico') }}"/>
	<meta content="" name="description" />
	<meta content="" name="author" />

	<!-- Penambahan -->
	@yield('csstop')

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
	<script src="{{ asset('/js/jam.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('/js/update.js') }}" type="text/javascript"></script> 
	<!-- END CORE TEMPLATE JS --> 
	
	<!-- Penambahan -->
	@yield('meta')

</head>
<!-- END HEAD -->

<!-- BEGIN BODY -->
<body class="">
	
	<!-- ALERT -->
	@if(Session::get('notif'))
	<div class="panel-alert">
		<div class="alert alert-{{ Session::get('notif')['label'] }}">
			<button type="button" class="close"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
			<p>{!! Session::get('notif')['err'] !!}</p>
		</div>
	</div>
	@endif

	@if (count($errors) > 0)
	<div class="panel-alert">
		<div class="alert alert-danger">
			<button type="button" class="close"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	</div>
	@endif
	<!-- END ALERT -->

	<!-- BEGIN HEADER -->
	<div class="header navbar navbar-inverse "> 
		<!-- BEGIN TOP NAVIGATION BAR -->
		<div class="navbar-inner">
			<div class="header-seperation"> 
				<ul class="nav pull-left notifcation-center" id="main-menu-toggle-wrapper" style="display:none">	
					<li class="dropdown"> <a id="main-menu-toggle" href="#main-menu"  class="" > <div class="iconset top-menu-toggle-white"></div> </a> </li>		 
				</ul>
				<!-- BEGIN LOGO -->	
				<a href="{{ url('/') }}"><img src="{{ asset('/img/logo.png') }}" class="logo" alt=""  data-src="{{ asset('/img/logo.png') }}" data-src-retina="{{ asset('/img/logo2x.png') }}" width="106" height="21"/></a>
				<!-- END LOGO --> 
				<ul class="nav pull-right notifcation-center">	
					<li class="dropdown" id="header_task_bar"> <a href="{{ url('/') }}" class="dropdown-toggle active" data-toggle=""> <div class="iconset top-home"></div> </a> </li>
					<!-- <li class="dropdown" id="header_inbox_bar" > <a href="email.html" class="dropdown-toggle" > <div class="iconset top-messages"></div>  <span class="badge" id="msgs-badge">2</span> </a></li> -->
					@if(env('CHAT'))
					<li class="dropdown" id="portrait-chat-toggler" style="display:none"> <a href="#sidr" class="chat-menu-toggle"> <div class="iconset top-chat-white "></div> </a> </li>        
					@endif
				</ul>
			</div>
			<!-- END RESPONSIVE MENU TOGGLER --> 
			<div class="header-quick-nav" > 
				<!-- BEGIN TOP NAVIGATION MENU -->
				<div class="pull-left"> 
				<ul class="nav quick-section">
					<li class="quicklinks"> <a href="javascript:;" class="" id="layout-condensed-toggle" >
						<div class="iconset top-menu-toggle-dark"></div>
					</a> </li>
				</ul>
				<ul class="nav quick-section">
			
			<!-- feedback -->
        	<li class="quicklinks"> 
          		<a href="#" data-toggle="modal" data-target="#modal-feedback" class="feedback" title="Feedback">
            		<i class="fa fa-bullhorn"></i>
            	</a>
           	</li>
			<li class="quicklinks"> <span class="h-seperate"></span></li>
          	<li class="quicklinks"> 
          		<a href="" class="tmp-reload" title="Reload">
            		<i class="iconset top-reload"></i>
            	</a>
           	</li>
          	<li class="quicklinks"> <span class="h-seperate"></span></li>
          	<li class="quicklinks">
	          	<a href="javascript:;" class="tmp-fullscreen">
	            	<div class="iconset glyphicon glyphicon-resize-full" title="Full Screen"></div>
	        	</a>
        	</li>
        	@if(env('CHAT'))
			<li class="m-r-10 input-prepend inside search-form no-boarder">
				<span class="add-on"> <span class="iconset fa fa-smile-o"></span></span>
					<input name="mystatus" type="text"  class="no-boarder " placeholder="Katakan sesuatu..." maxlength="50" value="{{ Auth::user()->status_user }}" style="width:300px;">
				</li>
			</ul>
			@endif
		</div>
		<!-- END TOP NAVIGATION MENU -->
		<!-- BEGIN CHAT TOGGLER -->
		<div class="pull-right"> 
			<div class="chat-toggler">	
				<!-- <a href="#" class="dropdown-toggle" id="my-task-list" data-placement="bottom"  data-content='' data-toggle="dropdown" data-original-title="Notifications"> -->
				<a href="javascript:;" class="dropdown-toggle" id="my-task-list">
					<div class="user-details"> 
						<div class="username">
							<!-- <span class="badge badge-important">3</span>  -->
							{{ Me::data()->nm_depan }} <span class="bold">{{ Me::data()->nm_belakang }}</span> &nbsp;&nbsp;
						</div>						
					</div> 
					<!-- <div class="iconset top-down-arrow"></div> -->
				</a>	
				<!-- <div id="notification-list" style="display:none">
					<div style="width:300px">
						  <div class="notification-messages info">
									<div class="user-profile">
										<img src="assets/img/profiles/d.jpg"  alt="" data-src="assets/img/profiles/d.jpg" data-src-retina="assets/img/profiles/d2x.jpg" width="35" height="35">
									</div>
									<div class="message-wrapper">
										<div class="heading">
											David Nester - Commented on your wall
										</div>
										<div class="description">
											Meeting postponed to tomorrow
										</div>
										<div class="date pull-left">
										A min ago
										</div>										
									</div>
									<div class="clearfix"></div>									
								</div>	
							<div class="notification-messages danger">
								<div class="iconholder">
									<i class="icon-warning-sign"></i>
								</div>
								<div class="message-wrapper">
									<div class="heading">
										Server load limited
									</div>
									<div class="description">
										Database server has reached its daily capicity
									</div>
									<div class="date pull-left">
									2 mins ago
									</div>
								</div>
								<div class="clearfix"></div>
							</div>	
							<div class="notification-messages success">
								<div class="user-profile">
									<img src="assets/img/profiles/h.jpg"  alt="" data-src="assets/img/profiles/h.jpg" data-src-retina="assets/img/profiles/h2x.jpg" width="35" height="35">
								</div>
								<div class="message-wrapper">
									<div class="heading">
										You haveve got 150 messages
									</div>
									<div class="description">
										150 newly unread messages in your inbox
									</div>
									<div class="date pull-left">
									An hour ago
									</div>									
								</div>
								<div class="clearfix"></div>
							</div>							
						</div>				
					</div> -->

					<div class="profile-pic"> 
						<a href="{{ url('/users/avatar') }}" title="Perbaharui Avatar">
							<img src="{{ asset('/img/avatars/md/' . Auth::user()->avatar) }}"  alt="" data-src="{{ asset('/img/avatars/md/' . Auth::user()->avatar) }}" data-src-retina="{{ asset('/img/avatars/sm/' . Auth::user()->avatar) }}" width="35" height="35" /> 
						</a>
					</div>       			
				</div>
				<ul class="nav quick-section ">
					<li class="quicklinks"> 
						<a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options">						
							<div class="glyphicon glyphicon-user"></div> 	
						</a>
						<ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">
							<li>
								<a href="{{ url('users/account') }}"> My Account</a>
							</li>
                  	<!-- <li>
                  		<a href="#"> My Inbox&nbsp;&nbsp;<span class="badge badge-important animated bounceIn">2</span></a>
                  	</li> -->
                  	<li class="divider"></li>                
                  	<li><a href="{{ url('/auth/logout') }}"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Log Out</a></li>
                  </ul>
              </li> 

              @if(env('CHAT'))
              <li class="quicklinks"> <span class="h-seperate"></span></li> 
              <li class="quicklinks"> 	
              	<a id="chat-menu-toggle" href="#sidr" class="chat-menu-toggle" ><div class="iconset top-chat-dark "><span class="badge badge-important hide" id="chat-message-count">1</span></div>
              	</a> 
              	<div class="simple-chat-popup chat-menu-toggle hide" >
              		<div class="simple-chat-popup-arrow"></div><div class="simple-chat-popup-inner">
              		<div style="width:100px">
              			<div class="semi-bold">David Nester</div>
              			<div class="message">Hey you there </div>
              		</div>
              	</div>
              </div>
          </li> 
          @endif

      </ul>
  </div>
  <!-- END CHAT TOGGLER -->
</div> 
<!-- END TOP NAVIGATION MENU --> 

</div>
<!-- END TOP NAVIGATION BAR --> 
</div>
<!-- END HEADER -->
<!-- BEGIN CONTAINER -->
<div class="page-container row-fluid">
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar" id="main-menu"> 
		<!-- BEGIN MINI-PROFILE -->
		<div class="user-info-wrapper">	
			<div class="profile-wrapper">
				<a href="{{ url('/users/avatar') }}" title="Perbaharui Avatar">
					<img src="{{ asset('/img/avatars/md/' . Auth::user()->avatar) }}"  alt="" data-src="{{ asset('/img/avatars/md/' . Auth::user()->avatar) }}" data-src-retina="{{ asset('/img/avatars/sm/' . Auth::user()->avatar) }}" width="69" height="69" />
				</a>
			</div>
			<div class="user-info">
				<div class="greeting"><small>Welcome</small></div>
				<div class="username"><small>{{ Me::data()->nm_depan }}</small></div>
				<div class="status">{{ Format::hari(date('Y-m-d')) }}, <span id="jam">Memuat...</span></div>
			</div>
		</div>
		<!-- END MINI-PROFILE -->

		<!-- BEGIN SIDEBAR MENU -->	
		<p class="menu-title">MAIN MENU <span class="pull-right"><a href="{{ url('/lockscreen') }}"><i class="fa fa-power-off"></i></a></span></p>

		{!! Menu::mainMenu() !!}

		<a href="#" class="scrollup">Scroll</a>
		<div class="clearfix"></div>
		<!-- END SIDEBAR MENU --> 
	</div>


	<!-- END SIDEBAR --> 
	<!-- BEGIN PAGE CONTAINER-->
	<div class="page-content"> 
		<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
		<div id="portlet-config" class="modal hide">
			<div class="modal-header">
				<button data-dismiss="modal" class="close" type="button"></button>
				<h3>Widget Settings</h3>
			</div>
			<div class="modal-body"> Widget settings form goes here </div>
		</div>
		<div class="clearfix"></div>
		<div class="content">  
			<div class="page-title">	
				<h3>@yield('title')</h3>		
			</div>

			@yield('content')

		</div>
	</div>

	<footer>
		@yield('footer')
	</footer>

	<!-- FEEDBACK MODAL -->

	<div class="modal fade" id="modal-feedback" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
	    		<form method="post" action="{{ url('/feedback') }}" enctype="multipart/form-data">
	    			<input type="hidden" name="_token" value="{{ csrf_token() }}">
	      			<div class="modal-header">
	        			<h4 class="modal-title" id="myModalLabel">Feedback</h4>
	      			</div>
			    	<div class="modal-body">
			        	
			    		<div class="grid simple">
							<div class="grid-title no-border"></div>
							<div class="grid-body no-border">
								<div class="form-group">
			    					<label for="feed_title">Title *</label>
			    					<input type="text" name="feed_title" id="feed_title" class="form-control" required>
			    				</div>

			    				<div class="form-group">
			    					<label for="feed_ask">Deskripsi *</label>
			    					<textarea name="feed_ask" id="feed_ask" rows="7" class="form-control" required></textarea>
			    				</div>

			    				<div class="form-group">
			    					<label for="feed_link">Link Module</label>
			    					<input type="text" name="feed_link" id="feed_link" class="form-control">
			    				</div>

			    				<div class="form-group">
			    					<label for="feed_file">
			    						<span class="btn btn-white"><i class="fa fa-paperclip"></i> Lampiran</span> <span class="feed_file"></span>
			    					</label>
			    					<input type="file" accept="image/*" name="feed_file" class="sr-only" id="feed_file">
			    				</div>
							</div>
						</div>

			    	</div>
		      		<div class="modal-footer">
		        		<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
		        		<button type="submit" class="btn btn-primary">Kirim</button>
		        		<a class="btn btn-primary" href="{{ url('/feedback') }}">List Feedback &nbsp;<span class="feedback"></span></a>
		      		</div>
	     		</form>
	    	</div>
	  	</div>
	</div>

	<!-- END FEEDBACK MODAL -->

</div>
<!-- END CONTAINER --> 
<!-- BEGIN CHAT --> 
@if(env('CHAT'))
@include('Chat.Master')
@endif
<!-- END CHAT --> 
<!-- END CONTAINER -->
<script type="text/javascript">
	var _base_url = '{{ url() }}';
</script>

@if(env('CHAT'))
<script type="text/javascript" src="{{ asset('/js/socket.io.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/io.js') }}"></script>
<script type="text/javascript">var __SOCKET_HOST = "{{ env('SOCKET_HOST') }}";</script>
@endif
</body>
</html>