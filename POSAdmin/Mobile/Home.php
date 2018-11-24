<?php
	include __DIR__ . "/DBConfig.php";
	include __DIR__ . "/GetSession.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<meta name="mobile-web-app-capable" content="yes">
		<title>POS</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui.structure.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui.theme.css" rel="stylesheet" />
		<link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
		<link href="assets/css/custom.css" rel="stylesheet" />
		<link href="assets/css/bootstrap-float-label.min.css" rel="stylesheet" />
		<link href="assets/css/dataTables.bootstrap.css" rel="stylesheet" />
		<link href="assets/css/jquery.dataTables.css" rel="stylesheet" />
		<link href="assets/css/keyTable.bootstrap.css" rel="stylesheet" />
		<link href="assets/css/lobibox.css" rel="stylesheet" />
		<link href="assets/css/toggles.css" rel="stylesheet" />
		<link href="assets/css/toggles-modern.css" rel="stylesheet" />
		<link href="assets/css/menu.css" rel="stylesheet">
		
		<!--<link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />-->
		<!--<link rel="stylesheet" href="assets/css/bootstrap-multiselect.css" type="text/css"/>-->
		
		<link rel="shortcut icon" href="./assets/img/logo.ico">
		<link rel="apple-touch-icon" sizes="57x57" href="./assets/img/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="./assets/img/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="./assets/img/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="./assets/img/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="./assets/img/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="./assets/img/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="./assets/img/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="./assets/img/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="./assets/img/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="./assets/img/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="./assets/img/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="./assets/img/favicon-16x16.png">
		<link rel="manifest" href="./assets/img/manifest.json">
		<meta name="msapplication-config" content="./assets/img/browserconfig.xml">
		<meta name="msapplication-TileImage" content="./assets/img/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">
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
			.actionBar, #grid-data-footer, #grid-data-header {
				display: none;
			}
			.fa-2x {
				font-size: 1.2em;
			}
		</style>
	</head>
	<body> 
		<div id="wrapper">
			<div style="height: 25px;"></div>
			<header style="height:30px;">
				<div class="container" style="display:inline-block; width: 70%;padding-right: 0;float:left;" >
					<div class="navbar navbar-static-top">
						<div class="navigation">
							<nav>
								<ul class="nav topnav bold">
									<!--<li class='dropdown'>
										<a href='#' class='menu active-menu' link='./Home.php'><i class='fa fa-home fa-2'></i> Home</a>
									</li>-->
									<li class='dropdown'>
										<a href='#' id="menuSale" class='menu' link='Transaction/Sale/'><i class='fa fa-cart-plus fa-2'></i> Penjualan</a>
									</li>
									<li class='dropdown'>
										<a href='#' class='menu' link='Transaction/SaleReturn/'><i class='fa fa-undo fa-2'></i> Retur</a>
									</li>
									<li class='dropdown'>
										<a href='#' class='menu' link='Transaction/Booking/'><i class='fa fa-hourglass-start fa-2'></i> D.O</a>
									</li>
									<li class='dropdown'>
										<a href='#' class='menu' link='Transaction/Payment/'><i class='fa fa-dollar fa-2'></i> Pembayaran</a>
									</li>
									<li class='dropdown'>
										<a href='#' class='menu' link='Transaction/PickUp/'><i class='fa fa-download fa-2'></i> Pengambilan</a>
									</li>
									<li class='dropdown'>
										<a href='#' class='menu' link='Transaction/StockAdjust'><i class='fa fa-check fa-2'></i>Stock Opname</a>
									</li>
									
								</ul>
							</nav>
						</div>
					</div>
				</div>
				<div class="container" style="display:inline-block;width: 30% !important;color:white;padding-right: 0;margin-top: 5px;float:left;" >
					<div class="navbar navbar-static-top" style="float:right;">
						<div class="navigation" style="margin-bottom: 7px;">
							<nav>
									Selamat Datang, <a href="#" style="color: white;font-size: 13px;" onclick="UpdatePassword();"><?php echo $_SESSION['Nama']; ?>!</a>&nbsp;&nbsp;&nbsp;<a href="#" onclick="printDailyReport();"><img src="./assets/img/logout.png" width="15px" border="0" acronym title="Logout" /></a>
									<input type="hidden" id="hdnLogout" class="menu" link="./Logout.php" />
							</nav>
						</div>
					</div>
				</div>
			</header>
			<!-- /. NAV TOP  -->
			
			<div id="page-wrapper">
				<div id="page-inner">
					
				</div>
			</div>
		</div>
		<div id="delete-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin ingin menghapusnya?</p>
		</div>
		<div id="save-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
		</div>
		<div id="print-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda ingin mencetak laporan harian?</p>
		</div>
		<div id="update-password" title="Ganti Password" style="display: none;">
			<form class="col-md-12 col-sm-12" id="UpdatePasswordForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-5 col-sm-5 labelColumn">
						Password Lama :
					</div>
					<div class="col-md-6 col-sm-6">
						<input id="txtCurrentPassword" name="txtCurrentPassword" type="password" class="form-control-custom" placeholder="Password Lama" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 col-sm-5 labelColumn">
						Password Baru :
					</div>
					<div class="col-md-6 col-sm-6">
						<input id="txtNewPassword" name="txtNewPassword" type="password" class="form-control-custom" placeholder="Password Baru" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 col-sm-5 labelColumn">
						Konfirmasi Password :
					</div>
					<div class="col-md-6 col-sm-6">
						<input id="txtConfirmNewPassword" name="txtConfirmNewPassword" type="password" class="form-control-custom" placeholder="Konfirmasi Password" />
					</div>
				</div>
			</form>
		</div>
		<div id="divModal"></div>
		<div id="first-balance" title="Saldo Awal" style="display: none;">
			<form class="col-md-12 col-sm-12" id="FirstBalanceForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-5 col-sm-5 labelColumn">
						Saldo Awal :
					</div>
					<div class="col-md-6 col-sm-6">
						<input id="txtFirstBalance" tabindex=50; name="txtFirstBalance" type="tel" class="form-control-custom text-right" placeholder="Saldo Awal" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" value="0.00" />
					</div>
				</div>
				<br />
			</form>
		</div>
		<script src="assets/js/jquery-1.12.4.js"></script>
		<script src="assets/js/bootstrap.js"></script>
		<script src="assets/js/jquery-ui.js"></script>
		<script src="assets/js/jquery.metisMenu.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="assets/js/notify.js"></script>
		<script src="assets/js/lobibox.js"></script>
		<script src="assets/js/global.js"></script>
		<script src="assets/js/jquery.dataTables.js"></script>
		<script src="assets/js/dataTables.bootstrap.js"></script>
		<script src="assets/js/dataTables.keyTable.js"></script>
		<script src="assets/js/toggles.js"></script>
		<!--<a href="#Top" class="scrollup" onclick="return false;">Scroll</a>-->
		<!--<a href="#Bottom" class="scrolldown" onclick="return false;">Scroll</a>-->
		<div id="loading"></div>
		<iframe id='excelDownload' src='' style='display:none'></iframe>
		<script type="text/javascript" >
			var counter = 0;
			$(document).on("click", function() {
				if(counter == 0)	{
					//document.documentElement.webkitRequestFullscreen();
					counter = 1;
				}
			});

			$(document).ready(function() {
				var windowHeight = $( window ).height() - 58;				
				if(windowHeight < 540) {
					//windowHeight = 540;
				}
				$("#page-inner").css ({
					"min-height" : windowHeight,
					"max-height" : windowHeight,
					"overflow-x" : "hidden",
					"overflow-y" : "auto"
				});
				
				$("#page-wrapper").css ({
					"min-height" : windowHeight
				});
				
				$("#leftSide").css ({
					"min-height" : windowHeight - 75
				});
				
				$("head").append("<style> .panel-default { min-height : " + windowHeight + "px } </style>");
				$(".panel-default").css ({
					"min-height" : windowHeight
				});
				
				$("#tableContent").width($("#dvTableContent").width());
				
				$(window).resize(function() {
					$("#tableContent").width($("#dvTableContent").width());
				});

				$("body").on("click", "span.dropdown", function() {
					if($(this).children("div").css("display") == "block") { 
						$(this).children("div").toggle();
					}
					else if($(this).children("div").css("display") == "none") {
						$(".dropdown .dropdown-content").hide();
						$(this).children("div").toggle();
					}
				});

				$("#menuSale").click();

				$("#txtFirstBalance").on("input change paste",
				    function filterNumericAndDecimal(event) {
						var formControl;
						formControl = $(event.target);
						formControl.val(formControl.val().replace(/[^0-9]+/g, ""));
					}
				);
			});
		</script>
	</body>
</html>