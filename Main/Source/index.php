<?php
	SESSION_START();
	include "DBConfig.php";
	if(ISSET($_SESSION['UserLogin']) && ISSET($_SESSION['UserPassword'])) {
		$sql = "SELECT 
					1
				FROM
					master_user 
				WHERE 
					UserLogin = '".mysql_real_escape_string($_SESSION['UserLogin'])."' 
					AND IsActive = 1
					AND UserPassword = '".mysql_real_escape_string($_SESSION['UserPassword'])."'";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$cek = mysql_num_rows($result);
		if($cek == 1) {
			echo "<script>window.location='./Home.php'; </script>";
			die();
		}
	}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<!--<link rel="shortcut icon" type="image/png" href="./assets/img/favicon.png"/>-->
		<title>Klinik Gigi</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<link href="assets/css/custom.css" rel="stylesheet" />
	</head>
	<body>
		<div class="container">
			<div class="row text-center ">
				<div class="col-md-12">
					<br /><br />
					<!--<img src="assets/img/pptunika.png">-->
					<br />
				</div>
			</div>
			 <div class="row ">
				<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
					<div class="panel panel-default">
						<div class="panel-heading">
							<strong> Login Page </strong>  
						</div>
						<div class="panel-body">
							<form class="LoginForm" id="LoginForm" method="POST" action="" >
								<br />
								<div class="form-group input-group">
									<span class="input-group-addon"><i class="fa fa-user"></i></span>
									<input id="txtUserLogin" onkeypress="isEnterKey(event, 'SubmitForm');" name="txtUserLogin" type="text" class="form-control" placeholder="Username" required />
								</div>

								<div class="form-group input-group">
									<span class="input-group-addon"><i class="fa fa-lock"></i></span>
									<input id="txtPassword" onkeypress="isEnterKey(event, 'SubmitForm');" name="txtPassword" type="password" class="form-control" placeholder="Password" required />
								</div>
								<input type="button" class="btn btn-primary" onclick="SubmitForm();" value="Login">
							</form>
						</div>
					</div>
				</div>					
			</div>
		</div>
		<script src="assets/js/jquery-1.10.2.js"></script>
		<script src="assets/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/notify.js"></script>
		<script src="assets/js/global.js"></script>
		<div id="loading"></div>
		<script>
			$("#txtUserLogin").focus();

			function SubmitForm() {
				var cek = 1;
				$(".form-control").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "") {
							cek = 0;
							$(this).notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
						}
					}
				});
				if(cek == 1) {
					$("#loading").show();
					$.ajax({
						url: "Login.php",
						type: "POST",
						data: $("#LoginForm").serialize(),
						dataType: "html",
						success: function(data) {
							$("#loading").hide();
							if(data == "Success") window.location = "./Home.php";
							else {
								$.notify(data, "error");
							}
						},
						error: function(data) {
							$("#loading").show();
							$.notify("Koneksi gagal, Cek koneksi internet!", "error");
						}
					});
				}
			}
		</script>
	</body>
</html>
