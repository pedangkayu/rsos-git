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
	Avatars
@endsection

@section('content')
	
	<div class="row">
		<div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab1-1"><i class="fa fa-camera"></i> Avatars</a></li>
                <li><a data-toggle="tab" href="#tab1-2"><i class="fa fa-cloud-upload"></i> Upload</a></li>
            </ul>
            <form method="post" action="{{ url('/users/avatar') }}" enctype="multipart/form-data">
            	<input type="hidden" value="{{ csrf_token() }}" name="_token">
	            <div class="tab-content">
	                <div id="tab1-1" class="tab-pane active">
	                	<button class="btn btn-primary btn-flat" type="submit">Save Avatar</button>
	                	<hr />

	                    <?php
	                    	$avatar = [
	                    		'avatar1.png',
	                    		'avatar2.png',
	                    		'avatar3.png',
	                    		'avatar4.png',
	                    		'avatar5.png',
	                    		'avatar6.png',
	                    		'avatar7.png',
	                    		'avatar8.png',
	                    		'avatar9.png',
	                    		'avatar10.png',
	                    		'avatar11.png',
	                    		'avatar12.png',
	                    	];
	                    ?>
	                    <div class="row">
	                    	@foreach($avatar as $img)
	                    		<div class="col-sm-6 col-md-4 col-lg-3">
						            <div class="panel panel-default panel-member">
						                <div class="panel-body">
						                    <label>
						                        <div class="text-center">
						                            <img src="{{ asset('/img/avatars/lg/' . $img) }}" class="img-responsive img-thumbnail img-circle">

						                            <h4 class="thin">
						                            	<div class="radio radio-success">
						                                <input type="radio" name="avatar" class="icheck square-blue" id="avatar_{{ $img }}" value="{{ $img }}" {{ Auth::user()->avatar == $img ? 'checked' : '' }}/> <label for="avatar_{{ $img }}">{{ rtrim($img, '.png') }}</label>
						                                </div>
						                            </h4>
						                        </div>
						                    </label>
						                </div>
						            </div>
						        </div>
	                    	@endforeach
	                    </div>
	                </div>
	                <div id="tab1-2" class="tab-pane">
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
	                	
	                </div>
	            </div>
            </form>

        </div>
	</div>

@endsection