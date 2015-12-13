$(function(){

	getlaporan = function(page){

		var $waktu = $('[name="waktu"]:checked').val();
		var $bulan = $('[name="bulan"]').val();
		var $tahun = $('[name="tahun"]').val();

		var $dari 	= $('[name="dari"]').val();
		var $sampai = $('[name="sampai"]').val();

		var $id_gudang = $('[name="id_gudang"]').val();
		var $limit = $('[name="limit"]').val();

		try{

			if($id_gudang.length == 0)
				throw "Anda belum memilih gudang tujuan!";

			$('.btn-proses').button('loading');

			var param = {
				page 	: page,
				waktu	: $waktu,
				bulan	: $bulan,
				tahun	: $tahun,
				dari	: $dari,
				sampai	: $sampai,
				id_gudang: $id_gudang,
				limit	: $limit
			};

			kembali();

			$('.content-laporan').css('opacity', .3);
			$.getJSON(_base_url + '/reportlogistik/lpbajax', param, function(json){
				console.log(json);
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

		}catch(e){
			swal('PERINGATAN!', e);
		}

	}

	$('.btn-proses').click(function(){
		getlaporan(1);
	});

	detail = function(id, page){
		var $waktu = $('[name="waktu"]:checked').val();
		var $bulan = $('[name="bulan"]').val();
		var $tahun = $('[name="tahun"]').val();

		var $dari 	= $('[name="dari"]').val();
		var $sampai = $('[name="sampai"]').val();

		var $tipe = $('[name="tipe"]').val();
		var $limit = $('[name="limit"]').val();

		var param = {
			page 	: page,
			id_kategori		: id,
			waktu	: $waktu,
			bulan	: $bulan,
			tahun	: $tahun,
			dari	: $dari,
			sampai	: $sampai,
			tipe	: $tipe,
			limit	: $limit
		};

		$('.content-laporan-detail').css('opacity', .3);
		$('.detail-0').css('opacity', .3);
		$.getJSON(_base_url + '/reportlogistik/rekapbelanjadetailajax', param, function(json){
			
			// SETUP
			$('.content-laporan-detail').css('opacity', 1);
			onDataCancel();
			$('[name="id_kategori"]').val(json.id_kategori);

			// CONTENT
			$('.content-laporan-detail').html(json.content);
			$('.pagin').html(json.pagin);


			$('.detail-0').css('opacity', 1).addClass('hide');
			$('.detail-1').removeClass('hide');

			$('div.pagin-detail > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				getlaporan($page);
			});

		}, 'json');
	}

	kembali = function(){
		$('.detail-0').removeClass('hide');
		$('.detail-1').addClass('hide');
		$('[name="id_kategori"]').val(0);
	}

});