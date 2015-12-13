$(function(){

	getlaporan = function(page){

		var $waktu = $('[name="waktu"]:checked').val();
		var $bulan = $('[name="bulan"]').val();
		var $tahun = $('[name="tahun"]').val();

		var $dari 	= $('[name="dari"]').val();
		var $sampai = $('[name="sampai"]').val();

		var $tipe = $('[name="tipe"]').val();
		var $limit = $('[name="limit"]').val();

		$('.btn-proses').button('loading');

		var param = {
			page 	: page,
			waktu	: $waktu,
			bulan	: $bulan,
			tahun	: $tahun,
			dari	: $dari,
			sampai	: $sampai,
			tipe	: $tipe,
			limit	: $limit
		};

		$('.content-laporan').css('opacity', .3);
		$.getJSON(_base_url + '/rekap/skbajax', param, function(json){

			// SETUP
			$('.btn-proses').button('reset');
			$('.content-laporan').css('opacity', 1);
			onDataCancel();

			// CONTENT
			$('.content-laporan').html(json.content);
			$('.pagin').html(json.pagin);


			$('div.pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				getlaporan($page);
			});

		}, 'json');

	}

	$('.btn-proses').click(function(){
		getlaporan(1);
	});

});