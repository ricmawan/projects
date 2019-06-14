<?php
	include "DBConfig.php";
	include "GetSession.php";
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
		<link href="assets/css/custom.css" rel="stylesheet" />
		<link href="assets/css/AdminLTE.css" rel="stylesheet" />
		<link href="assets/css/skin-purple-light.css" rel="stylesheet" />
		<!--<link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />-->
		
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
	<body class="skin-purple-light sidebar-mini">
		<!--<div id="wrapper">-->
			<header class="main-header">
				<!-- Logo -->
				<a href="../../index2.html" class="logo">
					<!-- mini logo for sidebar mini 50x50 pixels -->
					<span class="logo-mini"><img src="./assets/img/android-chrome-192x192.png" style="width:50%;" /></span>
					<!-- logo for regular state and mobile devices -->
					<span class="logo-lg">
						<img src="./assets/img/logo_2.png" />
					</span>
				</a>
				<!-- Header Navbar: style can be found in header.less -->
				<nav class="navbar navbar-static-top">
					<!-- Sidebar toggle button-->
					<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					
					<div class="navbar-custom-menu">
						<ul class="nav navbar-nav">
							<li class="dropdown user user-menu">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<img src="./assets/img/find_user.png" class="user-image" alt="User Image">
									<span><?php echo $_SESSION['Nama']; ?></span>
								</a>
							</li>
							<li>
								<a href="#" class="menu" link="./Logout.php"><img src="./assets/img/logout.png" width="18px" border="0" acronym title="Logout" /></a>
							</li>
						</ul>
					</div>
				</nav>
			</header>
			
			<!-- Left side column. contains the logo and sidebar -->
			<aside class="main-sidebar">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar">
					<!-- sidebar menu: : style can be found in sidebar.less -->
					<ul class="sidebar-menu" data-widget="tree">
						<li class="header">MAIN NAVIGATION</li>
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

								echo "<li class='treeview'>";
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
								if($rowcount == 0 && $row['GroupMenuID'] == 1) echo "<a class='menu active-menu' href='#' id='Menu".$row['GroupMenuID']."' link='".$row['Url']."'><i class='".$row['Icon']."'></i> <span>".$row['GroupMenuName']."</span></a>";
								else if($rowcount == 0) { echo "<a class='menu' href='#' id='Menu".$row['GroupMenuID']."' link='".$row['Url']."'><i class='".$row['Icon']."'></i> <span>".$row['GroupMenuName']."</span></a>"; }
								else {
									echo "<a href='#'><i class='".$row['Icon']."'></i> <span> ".$row['GroupMenuName']."</span><span class='pull-right-container'><i class='fa fa-angle-left pull-right'></i></span></a>";
									echo "<ul class='treeview-menu'>";
									while($row2 = mysql_fetch_array($result2)) {
										echo "<li>";
										echo "<a href='#' link='".$row2['Url']."' class='menu' ><i class='fa fa-circle-o'></i> ".$row2['MenuName']."</a>";
										echo "</li>";
									}
									echo "</ul>";
								}
								echo "</li>";
							}
						?>
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<div class="content-wrapper" style="min-height: 1126px;">
				<div id="page-inner">
					<div class="row">
						<img src="./assets/img/logo.png" style="width:100%;" />
					</div>
				</div>
			</div>
		<!--</div>-->
		<div id="delete-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin ingin menghapusnya?</p>
		</div>
		<script src="assets/js/jquery-1.10.2.js"></script>
		<script src="assets/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/notify.js"></script>
		<script src="assets/js/global.js"></script>
		<script src="assets/js/adminlte.js"></script>
		<script src="assets/js/fastclick.js"></script>
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
				$("#content-wrapper").css ({
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
		</script>
	</body>
</html>
