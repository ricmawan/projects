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
		<link rel="stylesheet" type="text/css" href="assets/css/sooperfish.css" media="screen">
		<link rel="stylesheet" type="text/css" href="assets/css/sooperfish-theme-silver.css" media="screen">
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
			<div id="menu-container"> 
				<ul class="sf-menu" id="nav" >
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
							if($rowcount == 0 && $row['GroupMenuID'] == 1) echo "<a href='#' id='Menu".$row['GroupMenuID']."' link='".$row['Url']."'><i class='".$row['Icon']."'></i> ".$row['GroupMenuName']."</a>";
							else if($rowcount == 0) { }
							else {
								echo "<a href='#'><i class='".$row['Icon']."'></i> ".$row['GroupMenuName']."</a>";
								echo "<ul>";
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
				</ul>
			</div>			
			<div id="page-inner">
				
			</div>
		</div>
		<script src="assets/js/jquery-1.10.2.js"></script>
		<script src="assets/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/jquery.metisMenu.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="assets/js/notify.js"></script>
		<script src="assets/js/global.js"></script>
		<script src="assets/js/jquery.bootgrid.js"></script>
		<script type="text/javascript" src="assets/js/jquery.easing-sooper.js"></script>
		<script type="text/javascript" src="assets/js/jquery.sooperfish.js"></script>
		<!--<script src="assets/js/dataTables/jquery.dataTables.js"></script>
		<script src="assets/js/dataTables/dataTables.bootstrap.js"></script>-->
		<script src="assets/js/bootstrap-multiselect.js"></script>
		<!--<a href="#Top" class="scrollup" onclick="return false;">Scroll</a>-->
		<!--<a href="#Bottom" class="scrolldown" onclick="return false;">Scroll</a>-->
		<div id="loading"></div>
		<iframe id='excelDownload' src='' style='display:none'></iframe>
		<script type="text/javascript">
			$(document).ready(function() {
				$('ul.sf-menu').sooperfish();
				var windowHeight = $( window ).height() - 55;
				$("#page-inner").css ({
					"min-height" : windowHeight
				});
				$("head").append("<style> .panel-default { min-height : " + windowHeight + "px } </style>");
				$(".panel-default").css ({
					"min-height" : windowHeight
				});
				/*$("#wrapper").css ({
					
					"width" : "calc(100% - 5px)"
				});*/
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
