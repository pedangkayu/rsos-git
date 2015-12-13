$(function(){
	$.getJSON(_base_url + '/skb/notifspb', {}, function(json){
		if(json.total > 0){
			var $total = json.total > 9 ? '9+' : json.total;
			$('.spb-notif').html('<span title="' + json.total + '" class="badge">' + $total + '</span>');
		}
	});

	/*Pagination*/
	allspb = function(page){

		var $no 	= $('[name="kode"]').val();
		var $status = $('[name="status"]').val();
		var $limit 	= $('[name="limit"]').val();
		var $dep 	= $('[name="departemen"]').val();
		var $deadline = $('[name="deadline"]').val();
		var $surat = $('[name="surat"]').val();

		$('.allspb').css('opacity', .3);
		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/skb/allspb',
			data 	: {
				page 	: page, 
				kode 	: $no, 
				status 	: $status, 
				limit 	: $limit,
				dep 	: $dep,
				deadline: $deadline,
				surat	: $surat
			},
			cache 	: false,
			dataType : 'json',
			success : function(json){
				$('.allspb').html(json.data);
				$('.paginspb').html(json.pagin);

				$('div.paginspb > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link = $(this).attr('href');
					var $split = $link.split('?page=');
					var $page = $split[1];
					allspb($page);
				});

				$('.allspb').css('opacity', 1);
				onDataCancel();
			}
		});
	}

	$('div.paginspb > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link = $(this).attr('href');
		var $split = $link.split('?page=');
		var $page = $split[1];
		allspb($page);
	});

	$('.carispb').click(function(){
		allspb(1);
	});


	detailspb = function(id){
		$('.viewkode').html('');
		$('.btn-acc').html('');
		$('.detail-pmb').html('Memuat...');
		$.post(_base_url + '/skb/detailspb', {id : id}, function(json){
			$('.viewkode').html(json.kode);
			$('.detail-pmb').html(json.content);
			$('.btn-acc').html(json.button);
		}, 'json');
	}

	$('.btn-prosesSPB').click(function(){
		swal({
			title: "Anda yakin ?",   
			text: "Pastikan kembali permintaan ini, jika sudah yakin silahkan dilanjutkan!",
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#0b9c8f",   
			confirmButtonText: "Yes, Process!",   
			closeOnConfirm: true 
		}, function(){
			onDataCancel();
			$('#prosesSPB').submit();
		});
	});


	/*Pagination*/
	allskb = function(page){

		var $no_spb = $('[name="no_spb"]').val();
		var $no_skb	= $('[name="no_skb"]').val();
		var $limit 	= $('[name="limit"]').val();
		var $dep 	= $('[name="departemen"]').val();
		var $tanggal = $('[name="tanggal"]').val();

		$('.content-skb').css('opacity', .3);
		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/skb/allskb',
			data 	: {
				page 	: page,
				no_skb 	: $no_skb,
				no_spb 	: $no_spb,
				limit 	: $limit,
				dep 	: $dep,
				tanggal	: $tanggal
			},
			cache 	: false,
			dataType : 'json',
			success : function(json){
				$('.content-skb').html(json.content);
				$('.pagin-skb').html(json.pagin);

				$('div.pagin-skb > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link = $(this).attr('href');
					var $split = $link.split('?page=');
					var $page = $split[1];
					allskb($page);
				});

				$('.content-skb').css('opacity', 1);
				onDataCancel();
			}
		});
	}

	$('div.pagin-skb > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link = $(this).attr('href');
		var $split = $link.split('?page=');
		var $page = $split[1];
		allskb($page);
	});

	$('.cariskb').click(function(){
		allskb(1);
	});

});