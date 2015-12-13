@extends('Master.Template')

@section('meta')
<link rel="stylesheet" href="{{ asset('/plugins/crop/cropper.min.css') }}" type="text/css" />
<script src="{{ asset('/plugins/crop/cropper.min.js') }}"></script>

<script type="text/javascript">

	$(function(){
		var $image = $('.img-container > img');
			// Import image
			var $inputImage = $('#inputImage'),
			URL = window.URL || window.webkitURL,
			blobURL;

			if (URL) {

				$inputImage.change(function () {
					var files = this.files,
					file;

					if (files && files.length) {
						file = files[0];

						if (/^image\/\w+$/.test(file.type)) {
							blobURL = URL.createObjectURL(file);
							$image.one('built.cropper', function () {
		              URL.revokeObjectURL(blobURL); // Revoke when load complete
		            }).cropper('reset').cropper('replace', blobURL);
		            //$inputImage.val('');
		          } else {
		          	showMessage('Please choose an image file.');
		          }
		        }

		      });

			} else {
				$inputImage.parent().remove();
			}

			$image.cropper({
				aspectRatio: 1,
				preview: '.img-preview',
				rotatable : true,
				minCropBoxWidth : 20,
				minCropBoxHeight : 20,
				crop: function (data) {
					$('#x').val(Math.round(data.x));
					$('#y').val(Math.round(data.y));
					$('#height').val(Math.round(data.height));
					$('#width').val(Math.round(data.width));
					$("#rotate").val(Math.round(data.rotate));
				}
			});

			$('.left').click(function(){
				$image.cropper('rotate', -45);
			});
			$('.right').click(function(){
				$image.cropper('rotate', 45);
			});

		});

</script>

<style type="text/css">

	.img-container,
	.img-preview {
		background-color: #f7f7f7;
		overflow: hidden;
		width: 100%;
		text-align: center;
	}

	.img-container {
		min-height: 200px;
		max-height: 466px;
		margin-bottom: 20px;
	}

	@media (min-width: 768px) {
		.img-container {
			min-height: 466px;
		}
	}

	.img-container > img {
		max-width: 100%;
	}


	.img-preview {
		float: left;
		margin-right: 10px;
		margin-bottom: 10px;
	}

	.img-preview > img {
		max-width: 100%;
	}

	.preview-lg {
		width: 220px;
		height: 220px;
	}

	.preview-md {
		width: 130px;
		height: 130px;
	}

	.preview-sm {
		width: 69px;
		height: 69px;
	}

	.preview-xs {
		width: 35px;
		height: 35px;
		margin-right: 0;
	}
</style>

@endsection

@section('title')
Upload Photo
@endsection

@section('content')
<div class="col-md-12">
	<ul class="nav nav-tabs">
		<li>

			<a href="{{ url('/karyawan/update/'.$id_karyawan) }}">Data Karyawan</a>

		</li>
		<li><a href="{{ url('/karyawan/keluarga/'.$id_karyawan) }}">Data Keluarga</a></li>
		<li  class="active"><a href="{{ url('/karyawan/photo/'.$id_karyawan) }}">Photo</a></li>
	</ul>
	<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
	<div class="tab-content">
		<div class="tab-pane active">
		<i>Upload Foto ini untuk dokumentasi Personalia</i>
			<form method="post" action="{{ url('/karyawan/photo') }}" enctype="multipart/form-data">
				<input type="hidden" value="{{ csrf_token() }}" name="_token">
				<div class="row">
					<div class="col-sm-9">
						<div class="img-container">
							<img>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="docs-preview clearfix">
							<div class="img-preview preview-lg"></div>
							<div class="img-preview preview-md"></div>
							<div class="img-preview preview-sm"></div>
							<div class="img-preview preview-xs"></div>
						</div>

						<div class="form-group text-center">
							<label class="btn btn-primary btn-upload" for="inputImage" title="Upload image file" style="width:100%;">
								<input class="sr-only" id="inputImage" name="image" type="file" accept="image/*">
								<span class="docs-tooltip" data-toggle="tooltip" title="Import image with Blob URLs">
									<span class="fa fa-camera"></span> Import Image
								</span>
							</label>
						</div>
						<input type="hidden" value="{{ $id_karyawan }}" name="id">
						<div class="form-group text-center">
							<button type="submit" class="btn btn-primary" style="width:100%;"><i class="fa fa-cloud-upload"></i> Upload</button>
						</div>

						<input type="hidden" value="0" name="x" id="x">
						<input type="hidden" value="0" name="y" id="y">
						<input type="hidden" value="0" name="w" id="width">
						<input type="hidden" value="0" name="h" id="height">
						<input type="hidden" value="0" name="r" id="rotate">

					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection