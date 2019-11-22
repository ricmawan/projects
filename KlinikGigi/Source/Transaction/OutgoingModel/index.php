<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			th[data-column-id="Opsi"] {
				width: 80px !important;
			}
			.ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
				float: left;
			}
		</style>
	</head>
	<body>
		<iframe id="OnlineFrame" style="border: 0;overflow: hidden;width: 100%;height: auto;" scrolling="none"></iframe>
		<script>
			$(document).ready(function() {
				$("#OnlineFrame").attr("src", "https://imdentalspecialist.com/old/Transaction/OutgoingModel/");
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