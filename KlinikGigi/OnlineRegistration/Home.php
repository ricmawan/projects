<?php
	include "DBConfig.php";
	//include "GetSession.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>IM Dental Specialist</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
		<link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
		<link href="assets/css/custom.css" rel="stylesheet" />
		<link href="assets/css/jquery.bootgrid.css" rel="stylesheet" />
		<!--<link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />-->
		<link rel="stylesheet" href="assets/css/bootstrap-multiselect.css" type="text/css"/>
		<link rel="stylesheet" href="assets/css/jquery.fancybox.css" type="text/css" media="screen" />
		
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
			#page-inner {
				text-align: center;
				overflow: auto;
			}
			
			#page-inner img {
				flex-shrink: 0;
			}
		</style>
		
	</head>
	<body>
		<div id="page-inner">
			<img src="./assets/img/logo.png" style="width:50%;" />
			<br />
			<h1>Selamat datang, klik "Daftar" untuk menjadwalkan pemeriksaan gigi.
			<br />
			<button class="btn btn-primary" onclick="Schedule();" link="./Transaction/Calendar/"><i class="fa fa-list-alt "></i> Daftar</button>&nbsp;
		</div>
		<script src="assets/js/jquery-1.10.2.js"></script>
		<script src="assets/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/jquery.metisMenu.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="assets/js/notify.js"></script>
		<script src="assets/js/global.js"></script>
		<script src="assets/js/jquery.bootgrid.js"></script>
		<script type="text/javascript" src="assets/js/jquery.fancybox.js"></script>
		<!--<script src="assets/js/dataTables/jquery.dataTables.js"></script>
		<script src="assets/js/dataTables/dataTables.bootstrap.js"></script>-->
		<script src="assets/js/bootstrap-multiselect.js"></script>
		<a href="#Top" class="scrollup" onclick="return false;">Scroll</a>
		<!--<a href="#Bottom" class="scrolldown" onclick="return false;">Scroll</a>-->
		<div id="loading"></div>
		<iframe id='excelDownload' src='' style='display:none'></iframe>
		<script type="text/javascript">
			function Schedule() {
				$.ajax({
					url: "./Transaction/Calendar/",
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
			$(document).ready(function() {
				var windowHeight = $( window ).height();
				$("#page-inner").css ({
					"min-height" : windowHeight,
					"max-height" : windowHeight,
					"overflow-x" : "hidden",
					"overflow-y" : "auto"
				});
				$("head").append("<style> .panel-default { min-height : " + windowHeight + "px } </style>");
				$(".panel-default").css ({
					"min-height" : windowHeight
				});
			});
		</script>
	</body>
</html>
