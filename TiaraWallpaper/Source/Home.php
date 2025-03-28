<?php
	include "DBConfig.php";
	include "GetSession.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Mutiara Indah Mandiri</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
		<link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
		<link href="assets/css/custom.css" rel="stylesheet" />
		<link href="assets/css/jquery.bootgrid.css" rel="stylesheet" />
		<!--<link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />-->
		<link rel="stylesheet" href="assets/css/bootstrap-multiselect.css" type="text/css"/>
		<!--<link rel="shortcut icon" type="image/png" href="./assets/img/favicon.png"/>-->
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
					 Selamat Datang, <a href="#" style="color: white;font-size: 16px;" onclick="UpdatePassword();"><?php echo $_SESSION['Nama']; ?>!</a> &nbsp;&nbsp;&nbsp;<a href="#" class="menu" link="./Logout.php"><img src="./assets/img/logout.png" width="20px" border="0" acronym title="Logout" /></a>
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
							<a href="#" onclick="Reload();"><i class="fa fa-refresh fa-3x"></i> Reload</a>
						</li>
						<?php
							$sql = "SELECT 
									GroupMenuID,
									GroupMenuName,
									Icon,
									Url
								FROM
									master_groupmenu
								ORDER BY 
									OrderNo ASC";
							if (! $result=mysql_query($sql, $dbh)) {
								echo mysql_error();
								return 0;
							}
							while($row = mysql_fetch_array($result)) {

								echo "<li>";
								$sql2 = "SELECT
										MM.MenuID,
										MM.GroupMenuID,
										MM.MenuName,
										MM.Url,
										MM.Icon
									 FROM
										master_menu MM
										JOIN master_role MR
											ON MR.MenuID = MM.MenuID
									WHERE 
										GroupMenuID = ".$row['GroupMenuID']."
										AND MR.UserID = ".$_SESSION['UserID']."
									GROUP BY
										MM.MenuID
									ORDER BY
										MM.OrderNo ASC";
								if (! $result2=mysql_query($sql2, $dbh)) {
									echo mysql_error();
									return 0;
								}
								$rowcount = mysql_num_rows($result2);
								if($rowcount == 0 && $row['GroupMenuID'] == 1) echo "<a class='menu active-menu' href='#' id='Menu".$row['GroupMenuID']."' link='".$row['Url']."'><i class='".$row['Icon']."'></i> ".$row['GroupMenuName']."</a>";
								else if($rowcount == 0) { }
								else {
									echo "<a href='#'><i class='".$row['Icon']."'></i>&nbsp; ".$row['GroupMenuName']."<span class='fa arrow'></span></a>";
									echo "<ul class='nav nav-second-level'>";
									while($row2 = mysql_fetch_array($result2)) {
										echo "<li>";
										echo "<a href='#' link='".$row2['Url']."' class='menu' ><i class='".$row2['Icon']."'></i> ".$row2['MenuName']."</a>";
										echo "</li>";
									}
									echo "</ul>";
								}
								echo "</li>";
							}
						?>
					</ul>
				</div>
			</nav>  
			<!-- /. NAV SIDE  -->
			
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
		<div id="update-password" title="Ganti Password" style="display: none;">
			<form class="col-md-12" id="UpdatePasswordForm" method="POST" action="" style="font-size:14px !important;" >
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
				var windowHeight = $( window ).height() - 55;
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
				$("#wrapper").css ({
					
					"width" : "calc(100% - 5px)"
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
