$(function(){

	getlogs = function(page){

		var $karyawan 	= $('[name="karyawan"]').val();
		var $dept 		= $('[name="dept"]').val();
		var $tanggal 	= $('[name="tanggal"]').val();
		var $limit 		= $('[name="limit"]').val();


		$('.content-logs').css('opacity', .3);
		$.getJSON(_base_url + '/loguser/getlogs', {

			karyawan	: $karyawan,
			page 		: page,
			dept		: $dept,
			tanggal 	: $tanggal,
			limit 		: $limit

		}, function(json){

			$('.content-logs').html(json.content);
			$('.pagin').html(json.pagin);

			$('.content-logs').css('opacity', 1);

			onDataCancel();

			$('div.pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				getlogs($page);
			});
		});

	}


	$('div.pagin > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link 	= $(this).attr('href');
		var $split 	= $link.split('?page=');
		var $page 	= $split[1];
		getlogs($page);
	});

	$('.cari').click(function(){
		getlogs(1);
	});

});