$(function(){
	

	$('.hapus').click(function(){
		var _id 	= $(this).data('id');
		var _name 	= $(this).data('nama');
		swal({   
			title: "Anda yakin ?",   
			text: _name + " akan dihapus secara permanen, dan tidak dapat dikembalikan!",   
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "Yes, delete it!",   
			closeOnConfirm: false 
		}, function(){
			/* Delete with Ajax */
			//$('.item_' + _id).css('opacity', '.3');
			$('.sweet-alert h2').html('Menghapus...');
			$.ajax({
				type : 'POST',
				url : _base_url + '/employment/destroy',
				data : {id : _id},
				cache : false,
				dataType : 'json',
				success : function(ses){
					if(ses.result == true){
						$('.item_' + _id).addClass('animated hinge', function(){
							setTimeout(function(){
								$('.item_' + _id).remove();
							}, 2000);
							swal("Deleted!", _name + " Berhasil dihapus dari Database.", "success");
						});
					}
						
				}
			});

		});
	});


	/**
	* Menambahkan kolom Portfolio
	*/
	$('.add-detail').click(function(){
		var tmp = '\
			<div class="row">\
				<button type="button" class="close" style="position:absolute; right:50px; z-index:1;" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\
            	<div class="col-sm-8">\
	            		<div class="form-group">\
							<div class="form-label">Company Name</div>\
							<div class="control">\
								<input type="text" class="form-control" name="company_name[]">\
							</div>\
						</div>\
						<div class="form-group">\
							<div class="form-label">Title</div>\
							<div class="control">\
								<input type="text" class="form-control" name="title[]">\
							</div>\
						</div>\
						<div class="form-group">\
							<div class="form-label">Location</div>\
							<div class="control">\
								<input type="text" class="form-control" name="location[]">\
							</div>\
						</div>\
						<div class="form-group">\
							<div class="form-label">Date Start</div>\
							<div class="control">\
							<div class="input-append success date col-md-10 col-lg-6 no-padding">\
								<input type="text" name="date_start[]" id="date_start" class="form-control" data-provide="datepicker">\
								<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> \
							</div>\
						</div>\
						</div>\
						<br>\
						<br>\
						<div class="form-group">\
							<div class="form-label">Date End</div>\
							<div class="control">\
							<div class="input-append success date col-md-10 col-lg-6 no-padding">\
								<input type="text" name="date_end[]" id="date_end" class="form-control" data-provide="datepicker">\
								<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> \
							</div>\
						</div>\
						</div>\
						<br>\
						<br>\
						<div class="form-group">\
							<div class="form-label">Description</div>\
							<div class="control">\
								<input type="text" class="form-control" name="description[]">\
							</div>\
						</div>\
	            	</div>\
	            	<br>\
            </div>\
		';

		$('.detail-items').append(tmp);
	});

	detail = function(id){
		
		var data = $('.detail');
		var posisi = $('.posisi');
		var load = '<i class="fa fa-circle-o-notch fa-spin"></i> Memuat...';
		data.html(load);
		var link = $('.link');
		$.ajax({
			type: 'GET',
			url: _base_url + '/employment/lowongan',
			data: {id: id},
			cache: false,
			dataType: 'json',
			success: function(res){
				if(res.result == true){
					data.html(res.content);
					posisi.html(res.posisi);
					$('.link').html(res.link);
				}
			}
		});
	}

	update = function(id){
		var data = $('.update');
		var load = '<i class="fa fa-circle-o-notch fa-spin"></i> Memuat...';
		data.html(load);
		var link = $('.link');
		$.ajax({
			type: 'GET',
			url: _base_url + '/employment/update',
			data: {id:id},
			cache: false,
			dataType: 'json',
			success: function(res){
				if(res.result == true){
					data.html(res.content);
					$('.link').html(res.link);
				}
			}
		});
	}

});