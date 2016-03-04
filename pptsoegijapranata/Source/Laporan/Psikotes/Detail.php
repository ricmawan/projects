<?php
	if(isset($_GET['id'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
		
		}
	}
?>
<html>
	<head>
		<script src="assets/js/jquery.form.js"></script>
		<style>
			.custom-combobox {
				position: relative;
				display: inline-block;
				width: 100%;
			}
			.custom-combobox-input {
				margin: 0;
				padding: 5px 10px;
				display: block;
				width: 100%;
				height: 34px;
				padding: 6px 12px;
				font-size: 14px;
				line-height: 1.42857143;
				color: #555;
				background-color: #fff;
				background-image: none;
				border: 1px solid #ccc;
				border-radius: 4px;
				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
				-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
				transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			}
			.btn-group {
				width: 100%;
				display: block;
			}
			.col-md-2 {
				display: -webkit-box;
				-webkit-box-align: center;
				height: 34px;
			}
			.col-md-1 {
				display: -webkit-box;
				-webkit-box-align: center;
				height: 34px;
				width: 1%;
			}
			.col-md-6 {
				display: -webkit-box;
				-webkit-box-align: center;
			}
			.ui-autocomplete {
				font-family: Open Sans, sans-serif; 
				font-size: 14px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Laporan Psikotes</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong>Tambah Data Psikotes</strong>  
					</div>
					<div class="panel-body">
						<form id="PostForm" method="POST" enctype="multipart/form-data" action="./Laporan/Psikotes/Insert.php" >
							<div class="row">
								<div class="col-md-2">Jenis Klien :</div>
								<div class="col-md-6">
									<input id="hdnId" name="hdnId" type="hidden" value="0" />
									<select class="form-control" name="ddlJenis" id="ddlJenis" required >
										<option value="" selected>-- Pilih Jenis Klien --</option>
										<option value="1">Perusahaan/Industri</option>
										<option value="2">Anak & Remaja</option>
										<option value="4">Dewasa</option>
										<option value="3">Pendidikan</option>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">Nama Klien :</div>
								<div class="col-md-6">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlKlien" id="ddlKlien" class="form-control" >
											<option value="" selected> </option>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">Nama Konsultan :</div>
								<div class="col-md-6">
									<select class="form-control" name="ddlKonsultan" id="ddlKonsultan" >
										<option value=0>-Pilih Konsultan-</option>
										<?php
											$sql = "SELECT * FROM master_konsultan";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_row($result)) {
												echo "<option value='".$row[0]."'>".$row[1]."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">Tanggal :</div>
								<div class="col-md-6">
									<input id="txtTanggal" name="txtTanggal" type="text" class="form-control DatePickerGlobal" placeholder="Tanggal" required />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">File :</div>
								<div class="col-md-6">
									<input type="file" class="form-control" name="uploadfile" id="uploadfile" />
								</div>
							</div>
							<br />
							<input type="button" class="btn btn-default" value="Simpan" onclick="savedata()" />
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="output" style="display:none;"></div>
		<script>			
			$(document).ready(function () {
				$("#ddlKlien").combobox();
				$("#ddlJenis").on("change", function() {
					$.ajax({
						url: "./CustomerService/CustomerService/GetKlien.php",
						type: "POST",
						data: { ddlJenis : $("#ddlJenis").val() },
						dataType: "html",
						success: function(data) {
							$("#ddlKlien").html(data);
						},
						error: function(data) {
							$.notify("Koneksi gagal", "error");
						}
					});
				});
				var options = { 
					target: '#output',   // target element(s) to be updated with server response 
					success: afterSuccess,  // post-submit callback 
					resetForm: false// reset the form after successful submit 
				};
				$('#PostForm').submit(function() { 
					$(this).ajaxSubmit(options);  			
					// always return false to prevent standard browser submit and page navigation 
					return false; 
				}); 
				function afterSuccess()
				{
					$("#loading").hide();
					var response = $("#output").html();
					var detail = response.split("|");
					if(detail[3] == "0") {
						$.notify(detail[1], "success");
						//window.location='/perumnas/physicprogress';
						if(PrevMenu != "./Home.php") {
							CurrentMenu = PrevMenu;
							$.ajax({
								url: PrevMenu,
								type: "POST",
								data: { },
								dataType: "html",
								success: function(data) {
									$("#page-inner").html(data);
									$("html, body").animate({
										scrollTop: 0
									}, "slow");
									$("#loading").hide();
								},
								error: function(data) {
									$("#loading").hide();
									$.notify("Koneksi gagal", "error");
								}
							});
						}
					}
					else $.notify(detail[1], "warn");
					/*$('#submit-btn').show(); //hide submit button
					$('#loading-img').hide(); //hide submit button
					$('#progressbox').delay( 1000 ).fadeOut(); //hide progress bar*/
				}
			});
			function savedata()
			{
				/*var ask=confirm("Simpan Data?");
				if (ask==true){*/
					var PassValidate = 1;
					var FirstFocus = 0;
					$(".form-control").each(function() {
						if($(this).hasAttr('required')) {
							if($(this).val() == "") {
								PassValidate = 0;
								$(this).notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $(this).focus();
								FirstFocus = 1;
							}
						}
					});
					if($("#ddlKlien").val() == "") {
						PassValidate = 0;
						$(".custom-combobox-input").notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $(".custom-combobox-input").focus();
						FirstFocus = 1;
					}
					if(PassValidate == 0) {
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						return false;
					}
					else {
						$("#loading").show();
						form = $("#PostForm");
						form.submit();
					}
				/*}
				else{
					ask="Cancel";
				}*/
			}
		</script>
	</body>
</html>