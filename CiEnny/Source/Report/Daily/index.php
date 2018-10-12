<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			td.details-control {
			    background: url('./assets/img/details_open.png') no-repeat center center;
			    cursor: pointer;
			}
			tr.shown td.details-control {
			    background: url('./assets/img/details_close.png') no-repeat center center;
			}
			.Cashier {
				color: red;
				font-size: 14px;
				font-weight: bold;
			}
			.TransactionName {
				font-size: 14px;
				font-weight: bold;
				text-decoration: underline;
			}
			.GrandTotal {
				color: #00ff50;
				font-size: 24px;
				font-weight: bold;
				text-decoration: underline;
				background-color: black;
			}
			.UnionTotal {
				background-color: yellow;
			}
			.TotalKasir {
				background-color: red;
				color: black;
			}
			.table > thead > tr > th,
			.table > tbody > tr > th,
			.table > tfoot > tr > th,
			.table > thead > tr > td,
			.table > tbody > tr > td,
			.table > tfoot > tr > td {
				border-top: none;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading" style="padding: 1px 15px;">
						 <h5>Harian</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								User:
							</div>
							<div class="col-md-2">
								<select id="ddlUser" name="ddlUser" tabindex=8 class="form-control-custom" placeholder="Pilih Cabang" >
									<option value=0 selected >-- Semua Kasir --</option>
									<?php
										$sql = "CALL spSelDDLCashier('".$_SESSION['UserLogin']."')";
										if (! $result = mysqli_query($dbh, $sql)) {
											logEvent(mysqli_error($dbh), '/Report/Daily/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
											return 0;
										}
										while($row = mysqli_fetch_array($result)) {
											echo "<option value='".$row['UserID']."' >".$row['UserName']."</option>";
										}
										mysqli_free_result($result);
										mysqli_next_result($dbh);
									?>
								</select>
							</div>
							<div class="col-md-1 labelColumn">
								Tanggal :
							</div>
							<div class="col-md-2">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom" style="background-color: #FFF;cursor: text;" placeholder="Tanggal" readonly />
								</div>
							</div>
							<div class="col-md-3">
								<button class="btn btn-info" id="btnView" onclick="Preview();" style="padding-top: 1px;padding-bottom: 1px;" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-success" id="btnExcel" onclick="ExportExcel();" style="padding-top: 1px;padding-bottom: 1px;"" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
						<hr style="margin: 10px 0;" />
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-hover" style="border: none !important;" >
								
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>		
			var table;
			var FirstPass = 1;
			var setCookie = function(name, value, expiracy) {
				var exdate = new Date();
				exdate.setTime(exdate.getTime() + expiracy * 1000);
				var c_value = escape(value) + ((expiracy == null) ? "" : "; expires=" + exdate.toUTCString());
				document.cookie = name + "=" + c_value + '; path=/';
			};

			var getCookie = function(name) {
				var i, x, y, ARRcookies = document.cookie.split(";");
				for (i = 0; i < ARRcookies.length; i++) {
					x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
					y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
					x = x.replace(/^\s+|\s+$/g, "");
					if (x == name) {
						return y ? decodeURI(unescape(y.replace(/\+/g, ' '))) : y; //;//unescape(decodeURI(y));
					}
				}
			};
			function Preview() {
				var UserID = $("#ddlUser").val();
				var TransactionDate = $("#txtTransactionDate").val();
			
				$("#loading").show();
				$("#dvTable").show();
				$.ajax({
					url: "./Report/Daily/DataSource.php",
					type: "POST",
					data: { UserID : UserID, TransactionDate : TransactionDate },
					dataType: "html",
					success: function(data) {
						if(data != "") {
							$("#loading").hide();
							$("#grid-data").html(data);
						}
						else {
							$("#loading").hide();
							var counter = 0;
							Lobibox.alert("warning",
							{
								msg: "Gagal memuat data!",
								width: 480,
								delay: false
							});
							return 0;
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$("#loading").hide();
						var counter = 0;
						var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
						LogEvent(errorMessage, "/Report/Daily/index.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
				$("#loading").hide();
			}
			function ExportExcel() {
				var UserID = $("#ddlUser").val();
				var TransactionDate = $("#txtTransactionDate").val();
				
				$("#loading").show();
				setCookie('downloadStarted', 0, 100); //Expiration could be anything... As long as we reset the value
				setTimeout(checkDownloadCookie, 1000); //Initiate the loop to check the cookie.
				$("#excelDownload").attr("src", "Report/Daily/ExportExcel.php?UserID=" + UserID + "&TransactionDate=" + TransactionDate);
			}

			var downloadTimeout;
			var checkDownloadCookie = function() {
				if (getCookie("downloadStarted") == 1) {
					setCookie("downloadStarted", "false", 100); //Expiration could be anything... As long as we reset the value
					$("#loading").hide();
				}
				else {
					downloadTimeout = setTimeout(checkDownloadCookie, 1000); //Re-run this function in 1 second.
				}
			};

			$(document).ready(function () {
				$( window ).resize(function() {
					table.columns.adjust().draw();
				});
				
				$("#txtTransactionDate").datepicker({
					dateFormat: 'dd-mm-yy',
					maxDate : "+0D"
				});

				var panelHeight = $(".panel-body").height();
				//$("head").append("<style> .panel-body { overflow-y:auto;min-height : " + (panelHeight + 20) + "px; max-height : " + (panelHeight + 20) + "px } </style>");
				$(".panel-body").css({
					"min-height" : (panelHeight + 50),
					"max-height" : (panelHeight + 50)
				});
			});
		</script>
	</body>
</html>
