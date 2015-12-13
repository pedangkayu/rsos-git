$(function(){

	$('.tgl').datepicker({
		format : 'yyyy-mm-dd'
	});
	$('.btn-tgl_terima_barang').click(function(){
		$('[name="tgl_terima_barang"]').val('');
	});
	
	$.getJSON(_base_url + '/sph/vendors', {}, function(json){
		$('[name="id_vendor"]').html(json.content);
		$('[name="id_vendor"]').select2();
	});

	allgr = function(page){

		var $no_spbm 			= $('[name="no_spbm"').val();
		var $no_po 				= $('[name="no_po"').val();
		var $no_surat_jalan 	= $('[name="no_surat_jalan"').val();
		var $id_vendor 			= $('[name="id_vendor"').val();
		var $tgl_terima_barang 	= $('[name="tgl_terima_barang"').val();
		var $id_kirim 			= $('[name="id_kirim"').val();
		var $limit 				= $('[name="limit"').val();
		var $titipan 			= $('[name="titipan"]').prop('checked');

		$('.content-gr').css('opacity', .3);

		$.getJSON(_base_url + '/gr/allgr', {

			page 				: page,
			no_spbm 			: $no_spbm,
			no_po 				: $no_po,
			no_surat_jalan 		: $no_surat_jalan,
			id_vendor 			: $id_vendor,
			tgl_terima_barang 	: $tgl_terima_barang,
			id_kirim 			: $id_kirim,
			titipan 			: $titipan,
			limit 				: $limit

		}, function(json){
			console.log(json);
			$('.content-gr').css('opacity', 1);

			$('.content-gr').html(json.content);
			$('.pagin').html(json.pagin);

			onDataCancel();

			$('div.pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allgr($page);
			});
		});

	}

	$('div.pagin > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link 	= $(this).attr('href');
		var $split 	= $link.split('?page=');
		var $page 	= $split[1];
		allgr($page);
	});

	$('.cari').click(function(){
		allgr(1);
	});

	$('select').change(function(){
		allgr(1);
	});

	$('input').keyup(function(e){
		if(e.keyCode == 13)
			allgr(1);
	});
	
});