<?php
	include "DBConfig.php";
	include "GetSession.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>POS</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
		<link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
		<link href="assets/css/custom.css" rel="stylesheet" />
		<link href="assets/css/jquery.bootgrid.css" rel="stylesheet" />
		<!--<link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />-->
		<link rel="stylesheet" href="assets/css/bootstrap-multiselect.css" type="text/css"/>
		
		<link rel="apple-touch-icon" sizes="180x180" href="./assets/img/apple-touch-icon.png">
		<link rel="icon" type="image/png" href="./assets/img/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="./assets/img/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="./assets/img/manifest.json">
		<link rel="mask-icon" href="./assets/img/safari-pinned-tab.svg" color="#5bbad5">
		<link rel="shortcut icon" href="./assets/img/favicon.ico">
		<meta name="msapplication-config" content="./assets/img/browserconfig.xml">
		<meta name="theme-color" content="#ffffff">
		<link rel="icon" type="image/png" sizes="32x32" href="./assets/img/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="./assets/img/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="./assets/img/favicon-16x16.png">
		<style>
			.navbar {
				min-height : 0px;
			}
			@media (min-width: 992px) {
				.col-md-12 {
				   float: none;
				}
			}
			.panel-heading {
				padding: 1px 15px;
			}
			.panel {
				margin-bottom : 0px;
			}
			.panel-body {
				margin-bottom : 0px;
				padding : 5px;
			}
			h1, .h1, h2, .h2, h3, .h3 {
				margin-top: 5px;
				margin-bottom: 5px;
			}
			.panel-default {
				height : device-height;
			}
			.bootgrid-footer {
				margin: 10px 0;
			}
		</style>
		
	</head>
	<body>
		<div id="wrapper">
			<nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0; min-height : 35px;">
				<!--<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<!--<a class="navbar-brand" href="#"></a>
				</div>-->
				<!--<span id="Clock" style="color: white; padding: 5px 20px 0px 20px; float: left; font-size: 16px;"></span>-->
				<div style="color: white; padding: 5px 20px 5px 50px; float: right; font-size: 16px;"> 
					 Selamat Datang, <a href="#" style="color: white;font-size: 16px;" onclick="UpdatePassword();"><?php echo $_SESSION['Nama']; ?>!</a> &nbsp;&nbsp;&nbsp;<a href="#" class="menu" link="./Logout.php"><img src="./assets/img/logout.png" width="20px" border="0" acronym title="Logout" /></a>
				</div>
			</nav>   
			<!-- /. NAV TOP  -->
			
			<div id="page-wrapper">
				<div id="page-inner">
					<div class="panel panel-default">
					<div class="panel-heading">
						<h5>Transaksi Penjualan</h5>  
					</div>
					<div class="panel-body">
						<div class="col-md-12" >
							<div class="row">
								<table style="width: 98%;border-spacing: 10px;border-collapse:separate;">
									<thead>
										<td align="center" style="width: 20%;">Kode Barang</td>
										<td align="center" style="width: 30%;">Nama Barang</td>
										<td align="center" style="width: 10%;">Qty</td>
										<td align="center" style="width: 25%;">Harga</td>
										<td align="center" style="width: 15%;">Diskon</td>
									</thead>
									<tbody>
										<tr>
											<td style="width: 20%;"><input id="txtItemCode" name="txtItemCode" type="text" class="form-control-custom" placeholder="Kode Barang" /></td>
											<td style="width: 30%;"><input id="txtItemName" name="txtItemName" type="text" class="form-control-custom" placeholder="Nama Barang" readonly /></td>
											<td style="width: 10%;"><input id="txtQuantity" name="txtQuantity" type="text" class="form-control-custom" placeholder="Qty" style="text-align: right;" value="1" /></td>
											<td style="width: 25%;"><input id="txtPrice" name="txtPrice" type="text" class="form-control-custom" placeholder="Harga" style="text-align: right;" readonly value="0.00" /></td>
											<td style="width: 15%;"><input id="txtDiscount" name="txtDiscount" type="text" class="form-control-custom" placeholder="Diskon"  style="text-align: right;" value="0" /></td>
										</tr>
									</tbody>
								</table>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<div style="width:99%;display:block;" id="dvTableHeader">
										<table class="table" style="width:100%;margin-bottom:0;" id="datainput">
											<thead style="color:white;display:block;width:100%;">
												<tr style="display:block;width:100%;height:25px;background-color: black;" >
													<td align="center" style="width: 15%;display:inline-block;">Kode Barang</td>
													<td align="center" style="width: 25%;display:inline-block;">Nama Barang</td>
													<td align="center" style="width: 10%;display:inline-block;">Qty</td>
													<td align="center" style="width: 15%;display:inline-block;">Harga</td>
													<td align="center" style="width: 10%;display:inline-block;">Diskon</td>
													<td align="center" style="width: 20%;display:inline-block;">Total</td>
												</tr>
											</thead>
										</table>
									</div>
									<div style="width:99%;display:block;max-height:220px;height:220px;overflow-y:auto;overflow-x:hidden;" id="dvTableContent">
										<table style="display:block;" id="tableContent">
											<tbody style="width:100%;display:block;">
												<tr style="display:block;width:100%;border-bottom:solid 1px black;" >
													<td align="center" style="width: 15%;display:inline-block;text-align:left;border-right:solid 1px black;">Kode Barang</td>
													<td align="center" style="width: 25%;display:inline-block;text-align:left;border-right:solid 1px black;">Nama Barang</td>
													<td align="center" style="width: 10%;display:inline-block;text-align:right;border-right:solid 1px black;">Qty</td>
													<td align="center" style="width: 15%;display:inline-block;text-align:right;border-right:solid 1px black;">Harga</td>
													<td align="center" style="width: 10%;display:inline-block;text-align:right;border-right:solid 1px black;">Diskon</td>
													<td align="center" style="width: 20%;display:inline-block;text-align:right;">Total</td>
												</tr>
												
											</tbody>
										</table>
									</div>
									<br />
									<div style="width: 20%;min-width:240px;height:5%;min-height:45px;margin:10px 20px 0 auto;text-align:right;background-color:black;color:white;font-size:32px;font-weight:bold;">100,000.00</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="delete-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin ingin menghapusnya?</p>
		</div>
		<div id="save-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
		</div>
		<div id="update-password" title="Ganti Password" style="display: none;">
			<form class="col-md-12" id="UpdatePasswordForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-5 labelColumn">
						Password Lama :
					</div>
					<div class="col-md-6">
						<input id="txtCurrentPassword" name="txtCurrentPassword" type="password" class="form-control-custom" placeholder="Password Lama" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Password Baru :
					</div>
					<div class="col-md-6">
						<input id="txtNewPassword" name="txtNewPassword" type="password" class="form-control-custom" placeholder="Password Baru" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Konfirmasi Password :
					</div>
					<div class="col-md-6">
						<input id="txtConfirmNewPassword" name="txtConfirmNewPassword" type="password" class="form-control-custom" placeholder="Konfirmasi Password" />
					</div>
				</div>
			</form>
		</div>
		<script src="assets/js/jquery-1.10.2.js"></script>
		<script src="assets/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/jquery.metisMenu.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="assets/js/notify.js"></script>
		<script src="assets/js/global.js"></script>
		<script src="assets/js/jquery.bootgrid.js"></script>
		<!--<script src="assets/js/dataTables/jquery.dataTables.js"></script>
		<script src="assets/js/dataTables/dataTables.bootstrap.js"></script>-->
		<script src="assets/js/bootstrap-multiselect.js"></script>
		<a href="#Top" class="scrollup" onclick="return false;">Scroll</a>
		<!--<a href="#Bottom" class="scrolldown" onclick="return false;">Scroll</a>-->
		<div id="loading"></div>
		<iframe id='excelDownload' src='' style='display:none'></iframe>
		<script type="text/javascript">
			$(document).ready(function() {
				var windowHeight = $( window ).height() - 45;
				$("#page-inner").css ({
					"min-height" : windowHeight,
					"max-height" : windowHeight,
					"overflow-x" : "hidden",
					"overflow-y" : "auto"
				});
				
				$("#page-wrapper").css ({
					"min-height" : windowHeight
				});
				
				$("head").append("<style> .panel-default { min-height : " + windowHeight + "px } </style>");
				$(".panel-default").css ({
					"min-height" : windowHeight
				});
				
				$("#tableContent").width($("#dvTableContent").width());
				
				$(window).resize(function() {
					$("#tableContent").width($("#dvTableContent").width());
				});
				/*$.ajax({
					url: "./Master/Notification/",
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
				});*/
			});
			//alert($( window ).height());
		</script>
	</body>
</html>
