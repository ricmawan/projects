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
			.ui-widget {
				font-size: 0.9em !important;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Jadwal</h5>
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<input type="hidden" id="hdnMedicationID" name="hdnMedicationID" value=0 />
							<input id="hdnScheduledDate" name="hdnScheduledDate" type="hidden" />
							<div class="row">
								<div class="col-md-2 labelColumn" >
									Pilih Cabang:
								</div>
								<div class="col-md-4" >
									<select id="ddlBranch" name="ddlBranch" class="form-control-custom" onchange="reloadSchedule();">
										<?php
											$sql = "SELECT BranchID, BranchName, StartHour, EndHour FROM master_branch";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												echo "<option value='".$row['BranchID']."' startHour=".$row['StartHour']." endHour=".$row['EndHour']." >".$row['BranchName']."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<br />
							<div class="row" >
								<div class="col-md-2 labelColumn" >
									<input type="radio" style="vertical-align: top;" id="rbTransactionDate" name="rbFilter" value=1 onclick="reloadSchedule();" checked /> Tanggal:
								</div>
								<div class="col-md-4">
									<div class="ui-widget" style="width: 100%;">
										<input id="txtScheduledDate" name="txtScheduledDate" type="text" class="form-control-custom" placeholder="Tanggal Rujukan" />
									</div>
								</div>
							</div>
							<br />
							<div class="row" >
								<div class="col-md-2 labelColumn" >
									<input type="radio" style="vertical-align: top;" id="rbPatientName" name="rbFilter" value=2 onclick="reloadSchedule();" /> Pasien:
								</div>
								<div class="col-md-4">
									<div class="ui-widget" style="width: 100%;">
										<input id="txtPatientName" name="txtPatientName" type="text" class="form-control-custom" placeholder="Nama Pasien" onfocus="$('#rbPatientName').prop('checked', true);" onchange="reloadSchedule();" />
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<iframe class="col-md-12" id="scheduleFrame" style="border: 0;overflow: hidden;height:340px;" scrolling="none">
									
								</iframe>
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
		</div>
		<script>
			function reloadSchedule() {
				var BranchID = $("#ddlBranch").val();
				var ScheduledDate = $("#hdnScheduledDate").val();
				var PatientName = $("#txtPatientName").val();
				var rbFilter = $("input[name=rbFilter]:checked").val();
				$("#loading").show();
				$("#scheduleFrame").attr("src", "./Transaction/Schedule/Schedule.php?BranchID=" + BranchID + "&ScheduledDate=" + ScheduledDate + "&PatientName=" + PatientName + "&rbFilter=" + rbFilter );
			}

			var counterLoaded = 0;
			var counterSuccess = 0;
			var counterFailed = 0;
			window.addEventListener("message", function(event) {
				if(event.data == "loaded") {
					if(counterLoaded == 0) {
						counterLoaded = 1;
						$("#loading").hide();
						$("#scheduleFrame").css({ "width": (parseFloat($("#scheduleFrame").css("width")) + 1) });
						$("#scheduleFrame").css({ "width": (parseFloat($("#scheduleFrame").css("width")) - 1) });
					}
					setTimeout(function() { counterLoaded = 0; }, 1000);
				}
				else if(event.data == "Data has been saved!") {
					if(counterSuccess == 0) {
						counterSuccess = 1;
						$.notify(event.data, "success");
						reloadSchedule();
					}
					setTimeout(function() { counterSuccess = 0; }, 1000);
				}
				else {
					if(counterFailed == 0) {
						counterFailed = 1;
						$.notify(event.data, "error");
					}
					setTimeout(function() { counterFailed = 0; }, 1000);
				}
			});
			
			$(document).ready(function() {
				$("#txtScheduledDate").datepicker({
					dateFormat: 'DD, dd M yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu" ],
					monthNames: [ "Jan", "Feb", "Mar", "Apr", "Mey", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des" ],
					minDate : "+0D",
					onSelect: function(dateText, obj) {
						var ScheduledDate = obj.selectedYear + "-" + ("0" + (obj.selectedMonth + 1)).slice(-2) + "-" + ("0" + obj.selectedDay).slice(-2);
						$("#hdnScheduledDate").val(ScheduledDate);
						$("#rbTransactionDate").prop("checked", true);
						$("#rbTransactionDate").attr("checked", "checked");
						reloadSchedule();
					}
				});
				//setInterval(function(){ $("#grid-data").bootgrid("reload") }, 900000);
			});
		</script>
	</body>
</html>
