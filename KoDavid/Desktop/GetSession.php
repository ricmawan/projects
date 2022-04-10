<?php
	$requestFile = basename("$_SERVER[SCRIPT_NAME]");
	SESSION_START();
	try {
		if(ISSET($_SESSION['UserLoginKasir']) && ISSET($_SESSION['UserPasswordKasir'])) {
			$sql = "CALL spSelUserLogin('".mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir'])."', '".mysqli_real_escape_string($dbh, $_SESSION['UserPasswordKasir'])."', 1, '".mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir'])."')";
			if (! $result = mysqli_query($dbh, $sql)) {
				logEvent(mysqli_error($dbh), '/Login.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
				return 0;
			}
			$cek = mysqli_num_rows($result);
			mysqli_free_result($result);
			mysqli_next_result($dbh);
			if($cek != 1) {
				if($requestFile == "Home.php") {
					echo '<script>
							alert("User tidak memiliki akses.");
							window.location= "./";
						  </script>';
				}
				else {
					echo '<script>
							var counterError = 0;
							Lobibox.alert("error",
							{
								msg: "User tidak memiliki akses.",
								width: 480,
								delay: false,
								beforeClose: function() {
									if(counterError == 0) {
										window.location= "./";
										counterError = 1;
									}
								}
							});
						</script>';
				}
				exit;
			}
		}
		else {
			if($requestFile == "Home.php") {
				echo '<script>
						alert("User tidak memiliki akses.");
						window.location = "./";
					  </script>';
			}
			else {
				echo '<script>
						var counterError = 0;
						Lobibox.alert("error",
						{
							msg: "User tidak memiliki akses.",
							width: 480,
							delay: false,
							beforeClose: function() {
								if(counterError == 0) {
									window.location = "./";
									counterError = 1;
								}
							}
						});
					</script>';
			}
			exit;
		}
	}
	catch (Exception $e)
	{
		logEvent($e->getMessage(), '/GetSession.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
	}
?>