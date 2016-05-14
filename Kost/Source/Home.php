<?php
	include "DBConfig.php";
	include "GetSession.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Kost</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
		<link href="assets/css/custom.css" rel="stylesheet" />
		<link href="assets/css/jquery.bootgrid.css" rel="stylesheet" />
		<link href="assets/css/menu.css" rel="stylesheet">
		<!--<link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />-->
		<!--<link rel="stylesheet" href="assets/css/bootstrap-multiselect.css" type="text/css"/>-->
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
			<header>
				<div class="container">
					<div class="navbar navbar-static-top">
						<div class="navigation">
							<nav>
								<ul class="nav topnav bold">
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
											if($row['GroupMenuID'] == 1) echo "<li class='dropdown active'>";
											else echo "<li class='dropdown'>";
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
											if($rowcount == 0 && $row['GroupMenuID'] == 1) echo "<a href='#' class='menu' id='Menu".$row['GroupMenuID']."' link='".$row['Url']."'><i class='".$row['Icon']."'></i> ".$row['GroupMenuName']."</a>";
											else if($rowcount == 0) { }
											else {
												echo "<a href='#'><i class='".$row['Icon']."'></i> ".$row['GroupMenuName']." <i class='fa fa-angle-down'></i></a>";
												echo "<ul class='dropdown-nav-menu bold'>";
												while($row2 = mysql_fetch_array($result2)) {
													echo "<li>";
													echo "<a href='#' link='".$row2['Url']."' class='menu' >".$row2['MenuName']."</a>";
													echo "</li>";
												}
												echo "</ul>";
											}
											echo "</li>";
										}
									?>
									<li class='dropdown'>
										<a href='#' class='menu' link='./Logout.php'><i class='fa fa-sign-out fa-2'></i> Logout</a>
									<li>
								</ul>
							</nav>
						</div>
					</div>
				</div>			
			</header>
			<div id="page-inner">
				<span id="page-inner-left">
					<span class="room">
						101
					</span>
					<span class="room booked">
						102
					</span>
					<span class="room">
						101
					</span>
					<span class="room occupied">
						102
					</span>
					<span class="room">
						101
					</span>
					<span class="room booked">
						102
					</span>
					<span class="room">
						101
					</span>
					<span class="room">
						102
					</span>
					<span class="room booked">
						101
					</span>
					<span class="room">
						102
					</span>
					<span class="room">
						101
					</span>
					<span class="room occupied">
						102
					</span>
					<span class="room occupied">
						101
					</span>
					<span class="room">
						102
					</span>
					<span class="room occupied">
						101
					</span>
					<span class="room">
						102
					</span>
					<span class="room booked">
						101
					</span>
					<span class="room">
						102
					</span>
					<span class="room occupied">
						101
					</span>
					<span class="room occupied">
						102
					</span>
					<span class="room">
						101
					</span>
					<span class="room">
						102
					</span>
					<span class="room">
						101
					</span>
					<span class="room booked">
						102
					</span>
					<span class="room booked">
						101
					</span>
					<span class="room booked">
						102
					</span>
					<span class="room">
						101
					</span>
					<span class="room">
						102
					</span>
					<span class="room">
						101
					</span>
					<span class="room">
						102
					</span>
					<span class="room booked">
						101
					</span>
					<span class="room booked">
						102
					</span>
					<span class="room booked">
						101
					</span>
					<span class="room">
						102
					</span>
					<span class="room occupied">
						101
					</span>	
					<span class="room booked">
						101
					</span>
					<span class="room booked">
						102
					</span>
					<span class="room booked">
						101
					</span>
					<span class="room">
						102
					</span>
					<span class="room occupied">
						101
					</span>					
				</span>
				<span id="page-inner-right">
					&nbsp;
				</span>
			</div>
		</div>
		<script src="assets/js/jquery-1.10.2.js"></script>
		<script src="assets/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="assets/js/notify.js"></script>
		<script src="assets/js/global.js"></script>
		<script src="assets/js/jquery.bootgrid.js"></script>
		<!--<script src="assets/js/dataTables/dataTables.bootstrap.js"></script>-->
		<!--<a href="#Top" class="scrollup" onclick="return false;">Scroll</a>-->
		<!--<a href="#Bottom" class="scrolldown" onclick="return false;">Scroll</a>-->
		<div id="loading"></div>
		<iframe id='excelDownload' src='' style='display:none'></iframe>
		<script type="text/javascript">
			$(document).ready(function() {
				var windowHeight = $( window ).height() - 65;
				$("#page-inner").css ({
					"min-height" : windowHeight
				});
				$("head").append("<style> #page-inner-left { min-height : " + windowHeight + "px; overflow-y: auto; } .panel-default { min-height : " + windowHeight + "px; max-height : " + windowHeight + "px; } .panel-body { max-height: " + (windowHeight - 40) + "px; overflow-y: auto; } </style>");
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
