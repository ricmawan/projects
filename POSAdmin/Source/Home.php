<?php
	include __DIR__ . "/DBConfig.php";
	include __DIR__ . "/GetSession.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>POS Admin</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		
		
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<!--<link href="assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />-->
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
	</head>
	<body>
		<div id="wrapper">
			<nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0; height : 35px;">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#"></a>
				</div>
				<span id="Clock" style="color: white; padding: 5px 20px 0px 20px; float: left; font-size: 16px;"></span>
				<div style="color: white; padding: 5px 20px 0px 50px; float: right; font-size: 16px;"> 
					 Selamat Datang, <a href="#" style="color: white;font-size: 16px;" onclick="UpdatePassword();"><?php echo $_SESSION['Nama']; ?>!</a> &nbsp;&nbsp;&nbsp;<a href="#" class="menu" link="./Logout.php" ><img src="./assets/img/logout.png" width="20px" border="0" acronym title="Logout" /></a>
				</div>
			</nav>   
			<!-- /. NAV TOP  -->
			<nav class="navbar-default navbar-side" role="navigation">
				<div class="sidebar-collapse">
					<ul class="nav" id="main-menu">
						<li class="text-center" style="visibility:hidden;">
							<!--<img src="assets/img/find_user.png" class="user-image img-responsive"/>-->&nbsp;
						</li>
						<li>
							<a href="#" onclick="Reload();"><i class="fa fa-refresh fa-2x"></i> Reload</a>
						</li>
						<li>
							<a class='menu active-menu' href='#' id='Menu1' link='./Home.php'><i class='fa fa-home fa-2x'></i> Home</a>
						</li>
						<?php
							$sql = "CALL spSelUserMenuNavigation(".$_SESSION['UserID'].", '".$_SESSION['UserLogin']."')";
										
							if (!$result = mysqli_query($dbh, $sql)) {
								logEvent(mysqli_error($dbh), '/Home.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
								return 0;
							}
							
							$PrevGroupMenuID = 0;
							while($row = mysqli_fetch_array($result)) {
								if($PrevGroupMenuID == $row['GroupMenuID']) {
									//menu dengan group menu yg sama
									echo "<li>";
									echo "<a href='#' link='".$row['Url']."' class='menu' > ".$row['MenuName']."</a>";
									echo "</li>";
								}
								else if($PrevGroupMenuID <> $row['GroupMenuID'] && $PrevGroupMenuID == 0) {
									//Group menu pertama
									echo "<li>";
									echo "<a href='#'><i class='".$row['Icon']."'></i>&nbsp; ".$row['GroupMenuName']."<span class='fa arrow'></span></a>";
									echo "<ul class='nav nav-second-level'>";
									echo "<li>";
									echo "<a href='#' link='".$row['Url']."' class='menu' > ".$row['MenuName']."</a>";
									echo "</li>";
								}
								else {
									//Mulai Groupmenu berikutnya, tutup grup menu sebelumnya..
									echo "</ul>";
									echo "</li>";
									echo "<li>";
									echo "<a href='#'><i class='".$row['Icon']."'></i>&nbsp; ".$row['GroupMenuName']."<span class='fa arrow'></span></a>";
									echo "<ul class='nav nav-second-level'>";
									echo "<li>";
									echo "<a href='#' link='".$row['Url']."' class='menu' > ".$row['MenuName']."</a>";
									echo "</li>";
								}
								$PrevGroupMenuID = $row['GroupMenuID'];
							}
							mysqli_free_result($result);
							mysqli_next_result($dbh);
						?>
					</ul>
				</div>
			</nav>  
			<!-- /. NAV SIDE  -->
			
			<div id="page-wrapper">
				<div id="page-inner" style="overflow-x:hidden;overflow-y:hidden;">
					<!--<img src="./assets/img/logo.png" style="width:40%;margin: 40px auto;display:block;"/>-->
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
			<form class="col-md-12" id="UpdatePasswordForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-5 labelColumn">
						Password Lama :
					</div>
					<div class="col-md-6">
						<input tabindex=1 id="txtCurrentPassword" name="txtCurrentPassword" type="password" class="form-control-custom" placeholder="Password Lama" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Password Baru :
					</div>
					<div class="col-md-6">
						<input tabindex=2 id="txtNewPassword" name="txtNewPassword" type="password" class="form-control-custom" placeholder="Password Baru" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Konfirmasi Password :
					</div>
					<div class="col-md-6">
						<input tabindex=3 id="txtConfirmNewPassword" name="txtConfirmNewPassword" type="password" class="form-control-custom" placeholder="Konfirmasi Password" />
					</div>
				</div>
			</form>
		</div>
		<div id="divModal"></div>
		<!-- Sliding div starts here -->
		<!--<div id="slider" style="right:-300px;">
			<div id="sidebar" onclick="open_panel()"><img src="assets/img/btnShortcutInfo.png"></button></div>
			<div id="header">
				<ul>
					<li>Double Klik/Enter: Edit.</li>
					<li>INSERT: Tambah Data.</li>
					<li>DELETE: Delete.</li>
				</ul>
			</div>
		</div>-->
		<!-- Sliding div ends here -->
		<!--<script src="assets/js/jquery-1.10.2.js"></script>
		<script src="assets/js/jquery-ui-1.10.3.custom.js"></script>-->
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
		<script src="assets/js/mousetrap.min.js"></script>
		<script src="assets/js/jquery.simulate.js"></script>
		<div id="loading"></div>
		<iframe id='excelDownload' src='' style='display:none'></iframe>
		<script type="text/javascript">
			$(document).ready(function() {
				var windowHeight = $( window ).height() - 55;
				$("#page-inner").css ({
					"min-height" : windowHeight,
					"max-height" : windowHeight
				});
				$(".panel-default").css ({
					"min-height" : windowHeight
				});
				$("head").append("<style> .panel-default { min-height : " + windowHeight + "px; } .panel-body { overflow-y:auto;min-height : " + (windowHeight - 60) + "px; max-height : " + (windowHeight - 60) + "px } </style>");
				$("#wrapper").css ({
					"width" : "calc(100% - 5px)"
				});
				$( window ).resize(function() {
					windowHeight = $( window ).height() - 55;
					$("#page-inner").css ({
						"min-height" : windowHeight,
						"max-height" : windowHeight
					});

					$(".panel-default").css ({
						"min-height" : windowHeight
					});
					$("#wrapper").css ({
						"width" : "calc(100% - 5px)"
					});
				});

				$("#loading").show();
				$.ajax({
					url: "./Notification/",
					type: "POST",
					data: { },
					dataType: "html",
					success: function(data) {
						$("#loading").hide();
						$("#page-inner").html(data);
					},
					error: function(data) {
						$("#loading").hide();
						var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
						LogEvent(errorMessage, "/Home.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
					}
				});
			});
		</script>
	</body>
</html>
