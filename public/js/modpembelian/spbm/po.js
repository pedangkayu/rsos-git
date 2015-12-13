$(function(){

	$('.tgl').datepicker({
		format : 'yyyy-mm-dd'
	});
	$('.btn-tanggal').click(function(){
		$('[name="tanggal"]').val('');
	});
	$('.btn-deadline').click(function(){
		$('[name="deadline"]').val('');
	});

	$.getJSON(_base_url + '/sph/vendors', {}, function(json){
		$('[name="id_vendor"]').html(json.content);
		$('[name="id_vendor"]').select2();
	});

	allpo = function(page){

		var $no_po = $('[name="no_po"').val();
		var $tgl 	= $('[name="tanggal"').val();
		var $deadline 	= $('[name="deadline"').val();
		var $id_vendor 	= $('[name="id_vendor"').val();
		var $limit 	= $('[name="limit"').val();
		var $status = $('[name="status"').val();
		var $titipan 	= $('[name="titipan"]').prop('checked');

		$('.content-po').css('opacity', .3);

		$.getJSON(_base_url + '/gr/allpo', {

			page 		: page,
			no_po 		: $no_po,
			tanggal 	: $tgl,
			deadline 	: $deadline,
			id_vendor 	: $id_vendor,
			titipan 	: $titipan,
			limit 		: $limit,
			status 		: $status

		}, function(json){
			console.log(json);
			$('.content-po').css('opacity', 1);

			$('.content-po').html(json.content);
			$('.po-pagin').html(json.pagin);

			onDataCancel();

			$('div.po-pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allpo($page);
			});
		});

	}

	$('div.po-pagin > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link 	= $(this).attr('href');
		var $split 	= $link.split('?page=');
		var $page 	= $split[1];
		allpo($page);
	});

	$('.cari').click(function(){
		allpo(1);
	});

	$('select').change(function(){
		allpo(1);
	});

	$('input').keyup(function(e){
		if(e.keyCode == 13)
			allpo(1);
	});

});