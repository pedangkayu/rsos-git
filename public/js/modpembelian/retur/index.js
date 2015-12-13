$(function(){
	$('.tgl').datepicker({
		format : 'yyyy-mm-dd'
	});
	$('.btn-tanggal').click(function(){
		$('[name="tanggal"]').val('');
	});
	
	$.getJSON(_base_url + '/sph/vendors', {}, function(json){
		$('[name="id_vendor"]').html(json.content);
		$('[name="id_vendor"]').select2();
	});


	allretur = function(page){

		var $no_retur 	= $('[name="no_retur"').val();
		var $no_po	 	= $('[name="no_po"').val();
		var $tgl 		= $('[name="tanggal"').val();
		var $id_vendor 	= $('[name="id_vendor"').val();
		var $limit 		= $('[name="limit"').val();

		$('.content-retur').css('opacity', .3);

		$.getJSON(_base_url + '/returvendor/allretur', {

			page 		: page,
			no_retur 	: $no_retur,
			no_po 		: $no_po,
			tanggal 	: $tgl,
			id_vendor 	: $id_vendor,
			limit 		: $limit

		}, function(json){
			
			$('.content-retur').css('opacity', 1);

			$('.content-retur').html(json.content);
			$('.retur-pagin').html(json.pagin);

			onDataCancel();

			$('div.retur-pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allretur($page);
			});
		});

	}

	$('div.retur-pagin > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link 	= $(this).attr('href');
		var $split 	= $link.split('?page=');
		var $page 	= $split[1];
		allretur($page);
	});

	$('.cari').click(function(){
		allretur(1);
	});

});