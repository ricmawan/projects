<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			th[data-column-id="TransactionDate"] {
			    width: 85px !important;
			}

			th[data-column-id="ReceivedDate"] {
			    width: 140px !important;
			}

			th[data-column-id="IncomingReceiptNumber"] {
			    width: 140px !important;
			}

			.ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
				float: left;
			}

			#ui-datepicker-div {
				z-index: 10 !important;
			}

			.form-control-custom {
				height: 17px !important;
			}
		</style>
	</head>
	<body>
		<iframe id="OnlineFrame" style="border: 0;overflow: hidden;width: 100%;height: auto;" scrolling="none"></iframe>
		<script>
			$(document).ready(function() {
				$("#OnlineFrame").attr("src", "https://imdentalspecialist.com/old/Transaction/IncomingModel/");
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