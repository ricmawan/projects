<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			.actionBar {
				display: none;
			}
			th[data-column-id="ScheduledDate"] {
			    width: 85px !important;
			}
		</style>
	</head>
	<body>
		<iframe id="OnlineFrame" style="border: 0;overflow: hidden;width: 100%;height: auto;" scrolling="none"></iframe>
		<script>
			$(document).ready(function() {
				$("#OnlineFrame").attr("src", "https://imdentalspecialist.com/old/Report/CustomerConfirmation/");
				var windowHeight = $( window ).height() - 59;
				$("#OnlineFrame").css ({
					"min-height" : windowHeight,
					"max-height" : windowHeight,
					"overflow-x" : "hidden",
					"overflow-y" : "auto"
				});
			});
		</script>
	</body>
</html>