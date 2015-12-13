$(function(){

	allvendor = function(page){

		var $nm_vendor = $('[name="nm_vendor"]').val();
		var $kode = $('[name="kode"]').val();
		var $tanggal = $('[name="tanggal"]').val();
		var $limit = $('[name="limit"]').val();
		var $disabled = $('[name="disabled"]').prop('checked');


		$('.content-vendor').css('opacity', .3);
		$.getJSON(_base_url + '/vendor/allvendor', {

			page		: page,
			nm_vendor 	: $nm_vendor,
			kode 		: $kode,
			tanggal 	: $tanggal,
			limit 		: $limit,
			disabled	: $disabled

		}, function(json){
			$('.pagin').html(json.pagin);
			$('.content-vendor').html(json.content);
			$('.content-vendor').css('opacity', 1);

			onDataCancel();

			$('div.pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allvendor($page);
			});
		});
	}

	$('div.pagin > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link 	= $(this).attr('href');
		var $split 	= $link.split('?page=');
		var $page 	= $split[1];
		allvendor($page);
	});
	
	$('.btn-cari-vendor').click(function(){
		allvendor(1);
	});

	detail = function(id){
		$('.modal-btn').html('');
		$('.modal-code').html('');
		$('.modal-content-vendor').html('Memuat...');
		$.getJSON(_base_url + '/vendor/detail', {id : id}, function(json){
			$('.modal-code').html(json.kode);
			$('.modal-content-vendor').html(json.content);
			$('.modal-btn').html(json.btn);
		});
	}

	disabled = function(id){
		
		swal({   
			title: "Anda yakin ?",   
			text: "Anda yakin ingin me nonaktifkan penyedia ini?",   
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "Yes, disable it!",   
			closeOnConfirm: true
		}, function(){
			$('.vendor-' + id).css('opacity', .3);
			$.post(_base_url + '/vendor/disable', {id : id}, function(json){
				$('.vendor-' + json.id).fadeIn('slow', function(){
					$(this).remove();
				});
			}, 'json');

		});

	}

	restore = function(id){
		swal({   
			title: "Anda yakin ?",   
			text: "Anda yakin ingin mengaktifkan penyedia ini?",   
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "Yes, aktifkan!",   
			closeOnConfirm: true
		}, function(){
			$('.vendor-' + id).css('opacity', .3);
			$.post(_base_url + '/vendor/activated', {id : id}, function(json){
				$('.vendor-' + json.id).fadeIn('slow', function(){
					$(this).remove();
				});

				// $('[name="disabled"]').prop('checked', false);
				// allvendor(1);

			}, 'json');

		});
	}

});