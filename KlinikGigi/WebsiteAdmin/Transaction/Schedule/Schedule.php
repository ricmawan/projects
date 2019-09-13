<html>
	<head>
		<style>
			table {
				font-size: 1em !important;
			}
			.ui-widget {
				font-size: 0.9em !important;
			}
		</style>
		<link href="../../assets/css/bootstrap.css" rel="stylesheet" />
		<link href="../../assets/css/font-awesome.css" rel="stylesheet" />
		<link href="../../assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
		<link href="../../assets/css/custom.css" rel="stylesheet" />
		<link href="../../assets/css/jquery.bootgrid.css" rel="stylesheet" />
	</head>
	<body>
		<?php
			if(isset($_GET["BranchID"])) {
				include "../../DBConfig.php";
				$Content = "";
				$BranchID = mysql_real_escape_string($_GET["BranchID"]);
				$ScheduledDate = mysql_real_escape_string($_GET["ScheduledDate"]);
				$PatientName = mysql_real_escape_string($_GET["PatientName"]);
				$rbFilter = mysql_real_escape_string($_GET["rbFilter"]);
				$Where = "";
				if($rbFilter == 1) {
					$Where = " AND DATE_FORMAT(TOS.ScheduledDate, '%Y-%m-%d') = '". $ScheduledDate ."'";
				}

				else {
					$Where = " AND TOS.PatientName LIKE '%".$PatientName."%' AND DATE_FORMAT(TOS.ScheduledDate, '%Y-%m-%d') >= DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 HOUR), '%Y-%m-%d')";
				}

				$dayNames = [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu" ];
				$monthNames = [ "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember" ];
				$sql = "SELECT
							TOS.OnlineScheduleID,
							MU.UserID,
							MU.UserName,
							DATE_FORMAT(TOS.ScheduledDate, '%e-%c-%Y %H:%i') BusinessHour,
							TOS.PatientName,
							TOS.Medication,
							TOS.ScheduledDate,
							DATE_FORMAT(TOS.ScheduledDate, '%H:%i') HourMinute
						FROM
							master_user MU
							LEFT JOIN transaction_onlineschedule TOS
								ON MU.UserID = TOS.DoctorID
						WHERE
							TOS.BranchID = ". $BranchID ."
							$Where
						ORDER BY
							MU.UserName,
							DATE_FORMAT(TOS.ScheduledDate, '%H:%i')";
							
				if (! $result = mysql_query($sql, $dbh)) {
					$MessageDetail = mysql_error();
					echo $MessageDetail;
					return 0;
				}
				$DayOfWeek = date("w", strtotime($row['ScheduledDate']));

				$RowNumber = 1;

				$Content = "<table class='table table-striped table-bordered table-hover' style='width:auto;padding-right:17px;' >";
				$Content .= "<thead style='background-color: black;color:white;height:25px;display:block;width:1030px;'>";
				$Content .= "<td align='center' style='width: 40px;' >No</td>";
				$Content .= "<td align='center' style='width: 270px;' >Dokter</td>";
				$Content .= "<td align='center' style='width: 130px;' >Jadwal</td>";
				$Content .= "<td align='center' style='width: 200px;' >Pasien</td>";
				$Content .= "<td align='center' style='width: 300px;' >Tindakan</td>";
				$Content .= "<td align='center' style='vertical-align:middle;width: 90px;'>Opsi</td>";
				$Content .= "</thead>";
				$Content .= "<tbody style='display:block;max-height:284px;height:100%;overflow-y:auto;'>";

				$Date = $dayNames[$DayOfWeek] ."," . date("d", strtotime($row['ScheduledDate'])) . " " . $monthNames[date("n", strtotime($row['ScheduledDate'])) - 1] . " " . date("Y", strtotime($row['ScheduledDate']));

				$RawDate = date("Y-m-d", strtotime($row['ScheduledDate']));

				if(mysql_num_rows($result) == 0) $Content .= "<tr><td align='center' style='width: 1030px;' colspan='5'>Data tidak ditemukan!</td></tr>";

				while ($row = mysql_fetch_array($result)) {
					$Content .= "<tr>";
					$Content .= "<td align='center' style='width: 40px;' >$RowNumber</td>";
					$Content .= "<td align='left' style='width: 270px;' >".$row['UserName']."</td>";
					$Content .= "<td align='center' style='width: 130px;' >".$row['BusinessHour']."</td>";
					$Content .= "<td align='left' style='width: 200px;' >".$row['PatientName']."</td>";
					$Content .= "<td align='left' style='width: 300px;' >".$row['Medication']."</td>";
					$Content .= '<td align="center" style="vertical-align:middle;width: 90px;">
												<i style="cursor:pointer;" class="fa fa-edit" onclick="EditMedication(\''.$row['UserName'].'\', \''.$Date.'\', \''.$row['HourMinute'].'\', \''.$row['PatientName'].'\', '.$row['OnlineScheduleID'].', \''.$row['Medication'].'\');" acronym title="Ubah Tindakan"></i>
											</td>';
					$Content .= "</tr>";
					$RowNumber++;
				}

				$Content .= "</tbody>";
				$Content .= "</table>";

				echo $Content;
			}

		?>
		<!--<div id="dialog-submit-referral" title="Jadwalkan Rujukan" style="display: none;">
			<form class="col-md-12" id="ReferralForm" method="POST" action=""  >
				<div class="row" >
					<div class="col-md-4 labelColumn" >
						Dokter:
					</div>
					<div class="col-md-8">
						<span id="doctorName" style="font-weight: bold; font-size: 15px; color: red;"></span>
					</div>
				</div>
				<br />
				<div class="row" >
					<div class="col-md-4 labelColumn" >
						Waktu:
					</div>
					<div class="col-md-8">
						<span id="ScheduledDate" style="font-weight: bold; font-size: 15px; color: red;"></span>
					</div>
				</div>
				<br />
				<div class="row" >
					<div class="col-md-4 labelColumn" >
						Name:
					</div>
					<div class="col-md-8">
						<input type="text" placeholder="Name" required id="txtPatientName" name="txtPatientName" class="form-control-custom" />
					</div>
				</div>
				<br />
				<div class="row" >
					<div class="col-md-4 labelColumn" >
						Phone:
					</div>
					<div class="col-md-8">
						<input type="text" placeholder="Phone" required id="txtPhone" name="txtPhone" class="form-control-custom" />
					</div>
				</div>
				<br />
				<div class="row" >
					<div class="col-md-4 labelColumn" >
						Email:
					</div>
					<div class="col-md-8">
						<input type="text" placeholder="Email" id="txtEmail" name="txtEmail" class="form-control-custom" />
					</div>
				</div>
				<br />
				<div class="row" >
					<div class="col-md-4 labelColumn" >
						Tindakan:
					</div>
					<div class="col-md-8">
						<input type="text" placeholder="Tindakan" required id="txtMedication" name="txtMedication" class="form-control-custom" />
						<?php
							//echo '<input id="hdnPatientName2" name="hdnPatientName2" type="hidden" value="'.$PatientName.'" />';
							//echo '<input id="hdnPhone2" name="hdnPhone2" type="hidden" value="'.$Phone.'" />';
							//echo '<input id="hdnEmail2" name="hdnEmail2" type="hidden" value="'.$Email.'" />';
							echo '<input id="hdnRawDate" name="hdnRawDate" type="hidden" value="'.$RawDate.'" />';
							echo '<input id="hdnBranchID" name="hdnBranchID" type="hidden" value="'.$BranchID.'" />';
						?>
						<input id="hdnBusinessHour" name="hdnBusinessHour" type="hidden" />
						<input id="hdnUserID" name="hdnUserID" type="hidden" />
					</div>
				</div>
			</div>
		</div>-->

		<div id="dialog-edit-medication" title="Ubah Tindakan" style="display: none;">
			<form class="col-md-12" id="EditForm" method="POST" action=""  >
				<div class="row" >
					<div class="col-md-4 labelColumn" >
						Dokter:
					</div>
					<div class="col-md-8">
						<span id="doctorName" style="font-weight: bold; font-size: 15px; color: red;"></span>
					</div>
				</div>
				<br />
				<div class="row" >
					<div class="col-md-4 labelColumn" >
						Waktu:
					</div>
					<div class="col-md-8">
						<span id="ScheduledDate" style="font-weight: bold; font-size: 15px; color: red;"></span>
					</div>
				</div>
				<br />
				<div class="row" >
					<div class="col-md-4 labelColumn" >
						Dokter:
					</div>
					<div class="col-md-8">
						<span id="PatientName" style="font-weight: bold; font-size: 15px; color: red;"></span>
					</div>
				</div>
				<br />
				<div class="row" >
					<div class="col-md-4 labelColumn" >
						Tindakan:
					</div>
					<div class="col-md-8">
						<input type="text" placeholder="Tindakan" required id="txtMedication" name="txtMedication" class="form-control-custom" />
						<input id="hdnOnlineScheduleID" name="hdnOnlineScheduleID" type="hidden" />
					</div>
				</div>
			</div>
		</div>

		<div id="loading"></div>
		<script src="../../assets/js/jquery-1.10.2.js"></script>
		<script src="../../assets/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="../../assets/js/bootstrap.min.js"></script>
		<script src="../../assets/js/notify.js"></script>
		<script src="../../assets/js/global.js"></script>
		<script src="../../assets/js/jquery.bootgrid.js"></script>
		<script>
			$(document).ready(function() {
				parent.postMessage("loaded", "*");
			});

			function EditMedication(UserName, ScheduledDate, HourMinute, PatientName, OnlineScheduleID, Medication) {
				$("#doctorName").html(UserName);
				$("#PatientName").html(PatientName);
				$("#ScheduledDate").html(ScheduledDate + " " + HourMinute);
				$("#hdnOnlineScheduleID").val(OnlineScheduleID);
				$("#txtMedication").val(Medication);
				$("#dialog-edit-medication").dialog({
					autoOpen: false,
					show: {
						effect: "fade",
						duration: 500
					},
					hide: {
						effect: "fade",
						duration: 500
					},
					resizable: false,
					height: 300,
					width: 450,
					modal: true,
					close: function() {
						$(this).dialog("destroy");
					},
					buttons: {
						"Save": function() {
							if($("#txtMedication").val() == "") {
								$("#txtMedication").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
								$("#txtMedication").focus();
							}

							else {
								$("#loading").show();
								$.ajax({
									url: "./UpdateMedication.php",
									type: "POST",
									data: $("#EditForm").serialize(),
									dataType: "json",
									success: function(data) {
										$("#loading").hide();
										if(data.FailedFlag == '0') {
											//$.notify(data.Message, "success");
											parent.postMessage(data.Message, "*");
											$("#txtMedication").val("");
											$("#doctorName").html("");
											$("#PatientName").html("");
											$("#ScheduledDate").html("");
											$("#hdnOnlineScheduleID").val(0);
											$("#dialog-edit-medication").dialog("destroy");
											//reloadSchedule();
										}
										else {
											//$.notify(data.Message, "error");
											parent.postMessage(data.Message, "*");
										}
									},
									error: function(data) {
										$("#loading").hide();
										$.notify("Terjadi kesalahan sistem!", "error");
									}
								
								});
							}
						},
						"Close": function() {
							$(this).dialog("destroy");
						}
					}
				}).dialog("open");
			}

			function SubmitReferral(BusinessHour, ScheduledDate, UserName, UserID) {
				$("#doctorName").html(UserName);
				$("#ScheduledDate").html(ScheduledDate + " " + BusinessHour);
				$("#hdnBusinessHour").val(BusinessHour);
				$("#hdnUserID").val(UserID);
				$("#dialog-submit-referral").dialog({
					autoOpen: false,
					show: {
						effect: "fade",
						duration: 500
					},
					hide: {
						effect: "fade",
						duration: 500
					},
					resizable: false,
					height: 340,
					width: 450,
					modal: true,
					close: function() {
						$(this).dialog("destroy");
					},
					buttons: {
						"Save": function() {
							if($("#txtMedication").val() == "") {
								$("#txtMedication").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
								$("#txtMedication").focus();
							}

							else {
								$("#loading").show();
								$.ajax({
									url: "./Insert.php",
									type: "POST",
									data: $("#ReferralForm").serialize(),
									dataType: "json",
									success: function(data) {
										$("#loading").hide();
										if(data.FailedFlag == '0') {
											//$.notify(data.Message, "success");
											parent.postMessage(data.Message, "*");
											$("#txtMedication").val("");
											$("#doctorName").html("");
											$("#ScheduledDate").html("");
											//$("#hdnPatientName2").val("");
											//$("#hdnEmail2").val("");
											//$("#hdnPhone2").val("");
											$("#hdnBusinessHour").val();
											$("#hdnUserID").val("");
											$("#dialog-submit-referral").dialog("destroy");
										}
										else {
											//$.notify(data.Message, "error");
											parent.postMessage(data.Message, "*");
										}
									},
									error: function(data) {
										$("#loading").hide();
										$.notify("Terjadi kesalahan sistem!", "error");
									}
								
								});
							}
						},
						"Close": function() {
							$(this).dialog("destroy");
						}
					}
				}).dialog("open");
			}
		</script>
	</body>
</html>