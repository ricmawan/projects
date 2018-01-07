<?php
	SESSION_START();
	include __DIR__ . "/DBConfig.php";
	
	try {
		if(ISSET($_SESSION['UserLogin']) && ISSET($_SESSION['UserPassword'])) {
			$sql = "CALL spSelUserLogin('".mysqli_real_escape_string($dbh, $_SESSION['UserLogin'])."', '".mysqli_real_escape_string($dbh, $_SESSION['UserPassword'])."', 1, '".mysqli_real_escape_string($dbh, $_SESSION['UserLogin'])."')";
			if (!$result = mysqli_query($dbh, $sql)) {
				logEvent(mysqli_error($dbh), '/index.php', mysqli_real_escape_string($_SESSION['UserLogin']));
				return 0;
			}
			
			$cek = mysqli_num_rows($result);
			mysqli_free_result($result);
			mysqli_next_result($dbh);
			if($cek == 1) {
				header("Location: ".$APPLICATION_PATH."Home.php", true, 301);
				return;
			}
		}
	}
	catch (Exception $e)
	{
		logEvent($e->getMessage(), '/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
	}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>POS</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<link href="assets/css/custom.css" rel="stylesheet" />
		
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
		<link rel="shortcut icon" href="./assets/img/logo.ico">
		<meta name="msapplication-config" content="./assets/img/browserconfig.xml">
		<meta name="msapplication-TileImage" content="./assets/img/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">
	</head>
	<body>
		<div class="container">
			<div class="row text-center ">
				<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
					<br />
					<br />
					<img src="assets/img/logo.png" width=300>
					<br />
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
									<input id="txtUserLogin" name="txtUserLogin" type="text" tabindex=1 class="form-control" autocomplete=off onfocus="this.select();" placeholder="Username" required />
								</div>

								<div class="form-group input-group">
									<span class="input-group-addon"><i class="fa fa-lock"></i></span>
									<input id="txtPassword" name="txtPassword" type="password" tabindex=2 class="form-control" autocomplete=off onfocus="this.select();" placeholder="Password" required />
								</div>
								<input id="btnLogin" name="btnLogin" type="button" tabindex=3 value="Login" class="btn btn-primary" onclick="SubmitForm();" >
							</form>
						</div>
					</div>
				</div>					
			</div>
		</div>
		<script src="assets/js/jquery-1.12.4.js"></script>
		<script src="assets/js/notify.js"></script>
		<div id="loading"></div>
		<script>
			$.fn.hasAttr = function(name) {  
				return this.attr(name) !== undefined;
			};

			$(document).ready(function() {
				$("#txtUserLogin").focus();
				
				$("input").not($(":submit, :button")).keypress(function (evt) {
					if (evt.keyCode == 13) {
						evt.preventDefault();
						var next = $('[tabindex="'+(this.tabIndex+1)+'"]');
						if(next.length) next.focus();
						else $('[tabindex="1"]').focus();  
					}
				});
				
				$(document).keypress(function (evt) {
					//evt.preventDefault();
					if (evt.keyCode == 13) {
						if($(":focus").length == 0) $('[tabindex="1"]').focus();
					}
				});
			});

			function SubmitForm() {
				var cek = 1;
				var firstFocus = 0;
				$(".form-control").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "") {
							cek = 0;
							$(this).notify("Harus diisi!", { position:"right", className:"error", autoHideDelay: 2000 });
							if(firstFocus == 0) {
								$(this).focus();
								firstFocus = 1;
							}
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
