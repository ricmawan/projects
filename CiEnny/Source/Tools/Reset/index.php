<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Reset</h5>
					</div>
					<div class="panel-body">
						<button class="btn btn-danger" onclick="Reset();" ><i class="fa fa-warning"></i> RESET</button>
					</div>
				</div>
			</div>
		</div>
		<script>
			function Reset() {
				var ask=confirm("Apakah anda yakin ingin melakukan reset program?");
				if(ask==true) {
					$("#loading").show();
					/*form = $("#PostForm");
					form.submit();*/
					$.ajax({
						url: "./Tools/Reset/Reset.php",
						type: "POST",
						data: "",
						dataType: "json",
						success: function(data) {
							if(data.FailedFlag == '0') {
								$("#loading").hide();
								$.notify(data.Message, "success");
							}
							else {
								$("#loading").hide();
								$.notify(data.Message, "error");					
							}
						},
						error: function(data) {
							$("#loading").hide();
							$.notify("Terjadi kesalahan sistem!", "error");
						}
					});
				}
			}
		</script>
	</body>
</html>
