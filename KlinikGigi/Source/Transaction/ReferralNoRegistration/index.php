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
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Penjadwalan Rujukan</h5>
					</div>
					<div class="panel-body">
						<div id="dialog-referral" title="Rujukan" >
							<div id="left-side" >
								<form class="col-md-8" id="PostForm" method="POST" action="" >
									<input type="hidden" id="hdnMedicationID" name="hdnMedicationID" value=0 />
									<div class="row" >
										<div class="col-md-2 labelColumn" >
											Pasien:
										</div>
										<div class="col-md-4">
											<input id="hdnPatientID" name="hdnPatientID" type="hidden" value="0" />
											<div class="ui-widget" style="width: 100%;" id="dvPatient">
												<select name="ddlPatient" id="ddlPatient" class="form-control-custom" placeholder="Pilih Pasien" >
													<option value="" ></option>
													<?php
														$sql = "SELECT PatientID, PatientName, PatientNumber, Address, Telephone, Email FROM master_patient";
														if(!$result = mysql_query($sql, $dbh)) {
															echo mysql_error();
															return 0;
														}
														while($row = mysql_fetch_array($result)) {
															echo "<option value='".$row['PatientID']."' patientname='".$row['PatientName']."' email='".$row['Email']."' phone='".$row['Telephone']."' >".$row['PatientName']." - ".$row['Address']."</option>";
														}
													?>
												</select>
											</div>
											<input id="hdnPatientName" name="hdnPatientName" type="hidden" value="" />
											<input id="hdnScheduledDate" name="hdnScheduledDate" type="hidden" value="" />
											<input id="hdnEmail" name="hdnEmail" type="hidden" value="" />
											<input id="hdnPhone" name="hdnPhone" type="hidden" value="" />
										</div>
									</div>
									<br />
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
											Tanggal:
										</div>
										<div class="col-md-4">
											<div class="ui-widget" style="width: 100%;">
												<input id="txtScheduledDate" name="txtScheduledDate" type="text" class="form-control-custom" placeholder="Tanggal Rujukan" />
											</div>
										</div>
									</div>
									<br />
									<div class="row">
										<iframe class="col-md-12" id="scheduleFrame" style="border: 0;overflow: hidden;width:1090px;height:310px;" scrolling="none">
											
										</iframe>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
		</div>
		<script>
			function reloadSchedule() {
				var PassValidate = 1;
				var FirstFocus = 0;
				if($("#ddlPatient").val() == "") {
					PassValidate = 0;
					$("#ddlPatient").next().find("input").notify("Harus dipilih!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#ddlPatient").next().find("input").focus();
					FirstFocus = 1;
				}

				if($("#txtScheduledDate").val() == "" ) {
					PassValidate = 0;
					if(FirstFocus == 0) $("#txtScheduledDate").focus();
				}

				if(PassValidate == 0) return false;
				else {
					var BranchID = $("#ddlBranch").val();
					var ScheduledDate = $("#hdnScheduledDate").val();
					var PatientName = $("#hdnPatientName").val();
					var Phone = $("#hdnPhone").val();
					var Email = $("#hdnEmail").val();
					$("#loading").show();
					$("#scheduleFrame").attr("src", "https://imdentalspecialist.com/old/Transaction/Referral/Schedule.php?BranchID=" + BranchID + "&ScheduledDate=" + ScheduledDate + "&PatientName=" + PatientName + "&Phone=" + Phone + "&Email=" + Email + "$rbFilter=1" );
				}
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
						reloadSchedule();
					}
				});

				$("#ddlPatient").combobox({
					select: function( event, ui ) {
						var PatientName = $("#ddlPatient option:selected").attr("patientname");
						var Email = $("#ddlPatient option:selected").attr("email");
						var Phone = $("#ddlPatient option:selected").attr("phone");
						$("#hdnPatientName").val(PatientName);
						$("#hdnEmail").val(Email);
						$("#hdnPhone").val(Phone);
						reloadSchedule();
					}
				});
				$("#ddlPatient").next().find("input").click(function() {
					$(this).val("");
				});
			});
		</script>
	</body>
</html>
