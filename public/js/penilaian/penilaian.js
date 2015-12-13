$(function(){

	chains = function(){
		var id = $("select#id_karyawan").val();
		$.ajax({
			type: 'GET',
			url: _base_url + '/penilaian/chain',
			data: {id: id},
			cache: false,
			dataType: 'json',
			success: function(res){
				if(res.result == true){
					$("select#jabatan").val(res.jabatan);
					$("select#departemen").val(res.departemen);
				}
			}
		});
	}

	list = function(id){
		var data = $('.detail');
		var load = '<i class="fa fa-circle-o-notch fa-spin"></i> Memuat...';
		data.html(load);
		var link = $('.link');
		$.ajax({
			type: 'GET',
			url: _base_url + '/penilaian/list',
			data: {id: id},
			cache: false,
			dataType: 'json',
			success: function(res){
				if(res.result == true){
					data.html(res.table);
				}
			}
		});
	}

});