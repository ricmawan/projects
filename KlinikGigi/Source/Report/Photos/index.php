<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			.multi-item-carousel .carousel-inner > .item {
				-webkit-transition: 500ms ease-in-out left;
				transition: 500ms ease-in-out left;
			}
			.multi-item-carousel .carousel-inner .active.left {
				left: -33%;
			}
			.multi-item-carousel .carousel-inner .active.right {
				left: 33%;
			}
			.multi-item-carousel .carousel-inner .next {
				left: 33%;
			}
			.multi-item-carousel .carousel-inner .prev {
				left: -33%;
			}
			@media all and (transform-3d), (-webkit-transform-3d) {
				.multi-item-carousel .carousel-inner > .item {
					-webkit-transition: 500ms ease-in-out left;
					transition: 500ms ease-in-out left;
					-webkit-transition: 500ms ease-in-out all;
					transition: 500ms ease-in-out all;
					-webkit-backface-visibility: visible;
					backface-visibility: visible;
					-webkit-transform: none!important;
					transform: none!important;
				}
			}
			.multi-item-carousel .carouse-control.left,
			.multi-item-carousel .carouse-control.right {
				background-image: none;
			}
			
			h1 {
				color: white;
				font-size: 2.25em;
				text-align: center;
				margin-top: 1em;
				margin-bottom: 2em;
				text-shadow: 0px 2px 0px #000000;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Foto Pemeriksaan</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-2 labelColumn">
								Pasien :
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<select name="ddlPatient" id="ddlPatient" class="form-control-custom" placeholder="Pilih Pasien">
										<option value="" ></option>
										<?php
											$sql = "SELECT PatientID, PatientName, PatientNumber FROM master_patient";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												echo "<option value='".$row['PatientID']."' >".$row['PatientNumber']." - ".$row['PatientName']."</option>";
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn">
								Kategori Foto :
							</div>
							<div class="col-md-3">
								<select name="ddlCategory" id="ddlCategory" class="form-control-custom" placeholder="Pilih Kategori" >
									<option value=0>-Pilih Kategori-</option>
									<option value="Sebelum">Sebelum Perawatan</option>
									<option value="Proses">Selama Perawatan</option>
									<option value="Setelah">Setelah Perawatan</option>
								</select>
							</div>
						</div>
						<button class="btn btn-info" id="btnView" onclick="LoadPhotos();" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
						<br />
						<br />
						<div class="carousel slide multi-item-carousel" id="theCarousel">
							<div class="carousel-inner" id="carouselContainer">
								<!--<div class="item active">
									<div class="col-xs-4">
										<div ><a href="#1">1<img src="http://placehold.it/300/f44336/000000" class="img-responsive"></a></div>
										<div ><a href="#2">11<img src="http://placehold.it/300/f44336/000000" class="img-responsive"></a></div>
									</div>
								</div>
								<div class="item">
									<div class="col-xs-4">
										<div ><a href="#2">2<img src="http://placehold.it/300/e91e63/000000" class="img-responsive"></a></div>
									</div>
								</div>
								<div class="item">
									<div class="col-xs-4">
										<div ><a href="#3">3<img src="http://placehold.it/300/9c27b0/000000" class="img-responsive"></a></div>
									</div>
								</div>
								<div class="item">
									<div class="col-xs-4">
										<div ><a href="#4">4<img src="http://placehold.it/300/673ab7/000000" class="img-responsive"></a></div>
									</div>
								</div>
								<div class="item">
									<div class="col-xs-4">
										<div ><a href="#5">5<img src="http://placehold.it/300/4caf50/000000" class="img-responsive"></a></div>
									</div>
								</div>
								<div class="item">
									<div class="col-xs-4">
										<div ><a href="#6">6<img src="http://placehold.it/300/8bc34a/000000" class="img-responsive"></a></div>
									</div>
								</div>
								<!-- add  more items here -->
								<!-- Example item start:  

								<div class="item">
									<div class="col-xs-4">	
										<div ><a href="#7">7<img src="http://placehold.it/300/8bc34a/000000" class="img-responsive"></a></div>
									</div>
								</div>

							<!--  Example item end -->
							</div>
							<a class="left carousel-control" href="#theCarousel" style="display:none;" data-slide="prev"><i class="glyphicon glyphicon-chevron-left"></i></a>
							<a class="right carousel-control" href="#theCarousel" style="display:none;" data-slide="next"><i class="glyphicon glyphicon-chevron-right"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			// Instantiate the Bootstrap carousel
			$('.multi-item-carousel').carousel({
				interval: false
			});

			// for every slide in carousel, copy the next slide's item in the slide.
			// Do the same for the next, next item.
			$('.multi-item-carousel .item').each(function(){
				var next = $(this).next();
				if (!next.length) {
					next = $(this).siblings(':first');
				}
				next.children(':first-child').clone().appendTo($(this));

				if (next.next().length>0) {
					next.next().children(':first-child').clone().appendTo($(this));
				} else {
					$(this).siblings(':first').children(':first-child').clone().appendTo($(this));
				}
			});
			
			$(document).ready(function () {
				$("#ddlPatient").combobox();
				$("#ddlPatient").next().find("input").click(function() {
					$(this).val("");
				});
				
				$("a.imageGallery").fancybox();
			});
			
			function LoadPhotos() {
				var ddlPatient = $("#ddlPatient").val();
				var ddlCategory = $("#ddlCategory").val();
				if(ddlPatient == 0) {
					$("#ddlPatient").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlPatient").next().find("input").focus();
					FirstFocus = 1;
				}
				else if (ddlCategory == 0) {
					$("#ddlCategory").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlCategory").next().find("input").focus();
					FirstFocus = 1;
				}
				else  {
					$.ajax({
						url: "./Report/Photos/ReadPhotos.php",
						type: "POST",
						data: { ddlPatient : ddlPatient, ddlCategory : ddlCategory },
						dataType: "html",
						success: function(data) {
							$("#loading").hide();
							$("#carouselContainer").html("");
							$("#carouselContainer").html(data);
							$("a.imageGallery").fancybox();
							if(data == "") $(".carousel-control").hide();
							else $(".carousel-control").show();
							$('.multi-item-carousel').carousel({
								interval: false
							});

							// for every slide in carousel, copy the next slide's item in the slide.
							// Do the same for the next, next item.
							$('.multi-item-carousel .item').each(function(){
								var next = $(this).next();
								if (!next.length) {
									next = $(this).siblings(':first');
								}
								next.children(':first-child').clone().appendTo($(this));

								if (next.next().length>0) {
									next.next().children(':first-child').clone().appendTo($(this));
								} else {
									$(this).siblings(':first').children(':first-child').clone().appendTo($(this));
								}
							});
						},
						error: function(data) {
							$("#loading").hide();
							$.notify("Terjadi kesalahan sistem!", "error");
						}
					});
				}
			}
		</script>
	</body>
</html>