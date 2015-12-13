$(function(){

	$('[data-toggle="tooltip"]').tooltip();

	$('.tgl,[name="deadline"],[name="tanggal"],[data-exp="exp"]').on('changeDate', function(ev){
	    $(this).datepicker('hide');
	});

	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
	
	$('.panel-alert').fadeIn('slow');
	$('.panel-alert').find('.close').click(function(){
		$('.panel-alert').remove();
	});

	$('.tmp-reload').click(function(){
		$(this).html('<i class="iconset top-reload fa-spin"></i>');
		$('body').css('opacity', .8);
		onDataCancel();
	});

	/* Confirmasi pada saat akan meningalkan halaman */
	var changesMade = false;
	$(window).bind('beforeunload', function() {
	    if (changesMade) {
	        return 'Perubahan / Penambahan data sudah dilakuan!';
	    } else {
	        return null;
	    }
	});
	onDataChanged = function() {
	    changesMade = true;
	}
	onDataCancel = function() {
	    changesMade = false;
	}
	$('[type="number"]').change(onDataChanged);
	$('input:text, textarea, select').change(onDataChanged);
	$('input:checkbox, input:radio').click(onDataChanged);
	$('form').submit(function(){
		onDataCancel();
	});
	/* End Confirmasi pada saat akan meningalkan halaman */


	/*Full Screen Mode*/
	launchIntoFullscreen = function(element) {
	  if(element.requestFullscreen) {
	    element.requestFullscreen();
	  } else if(element.mozRequestFullScreen) {
	    element.mozRequestFullScreen();
	  } else if(element.webkitRequestFullscreen) {
	    element.webkitRequestFullscreen();
	  } else if(element.msRequestFullscreen) {
	    element.msRequestFullscreen();
	  }
	}

	exitFullscreen = function() {
	  if(document.exitFullscreen) {
	    document.exitFullscreen();
	  } else if(document.mozCancelFullScreen) {
	    document.mozCancelFullScreen();
	  } else if(document.webkitExitFullscreen) {
	    document.webkitExitFullscreen();
	  }
	}

	$('.tmp-fullscreen').toggle(function(){
		launchIntoFullscreen(document.documentElement);
		$(this).html('<div class="iconset glyphicon glyphicon-resize-small" title="Close Full Screen"></div>');
	}, function(){
		exitFullscreen();
		$(this).html('<div class="iconset glyphicon glyphicon-resize-full" title="Full Screen"></div>');
	});
	/*End Full Screen Mode*/

	$('[name="mystatus"]').change(function(){
		var $mystatus = $(this).val();
		$.post(_base_url + '/ajax/status', {mystatus : $mystatus}, function(){
			onDataCancel();
		});
	});

	// Menu open navigasi
	$('li.start').closest('ul.sub-menu').css('display', 'block');
	$('li.start').closest('ul.sub-menu').closest('li').closest('ul.sub-menu').css('display', 'block');

	$('[type="submit"]').attr('data-loading-text', 'Process...');

	$('form').submit(function(){
		$('[type="submit"]').button('loading');
		$('body').css('cursor', 'wait');
	});

	close_sidebar = function(){
		$.sidr('close', 'sidr');
		 if($('#main-menu').attr('data-inner-menu') =='1'){
			//Do nothing
			console.log("Menu is already condensed");
		 }
		 else{
		  if($('#main-menu').hasClass('mini')){
			$('#main-menu').removeClass('mini');
			$('.page-content').removeClass('condensed');		
			$('.scrollup').removeClass('to-edge');	
			$('.header-seperation').show();
			//Bug fix - In high resolution screen it leaves a white margin
			$('.header-seperation').css('height','61px');
			$('.footer-widget').show();
		  }	
		  else{
			$('#main-menu').addClass('mini');
			$('.page-content').addClass('condensed');		
			$('.scrollup').addClass('to-edge');	
			$('.header-seperation').hide();
			$('.footer-widget').hide();	  
		  }
		 }
	}

	$('[name="feed_file"]').change(function(){
		var val = $(this).val();
		$('.feed_file').html(val);
	});

	/* GET FEEDBACK NOTIFICATION */
	$.getJSON(_base_url + '/ajax/feedback', {}, function(json){
		if(json.total != 0)
			$('.feedback').append('<span class="badge badge-notif">' + json.total + '</span>');
	});


	number_format= function(number, decimals, dec_point, thousands_sep) {

	  	number = (number + '')
	    	.replace(/[^0-9+\-Ee.]/g, '');
	  	var n = !isFinite(+number) ? 0 : +number,
	    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	    s = '',
	    toFixedFix = function(n, prec) {
	      var k = Math.pow(10, prec);
	      return '' + (Math.round(n * k) / k)
	        .toFixed(prec);
	    };
		  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
		  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
		    .split('.');
		  if (s[0].length > 3) {
		    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		  }
		  if ((s[1] || '')
		    .length < prec) {
		    s[1] = s[1] || '';
		    s[1] += new Array(prec - s[1].length + 1)
		      .join('0');
		  }
		  return s.join(dec);
	}


});