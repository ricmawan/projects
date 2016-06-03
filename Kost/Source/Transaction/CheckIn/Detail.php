<?php
	if(isset($_POST['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		//include "../../GetPermission.php";
		include "../../DBConfig.php";
		//echo $_SERVER['REQUEST_URI'];
		$RoomID = mysql_real_escape_string($_POST['ID']);
		$RoomNumber = "";
		$DailyRate = 0;
		$HourlyRate = 0;
		$RoomInfo = "";
		if($RoomID != 0) {
			//$Content = "Place the content here";
			$sql = "SELECT
						RoomNumber,
						DailyRate,
						HourlyRate,
						RoomInfo
					FROM
						master_room
					WHERE
						RoomID = $RoomID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$RoomNumber = $row['RoomNumber'];
			$DailyRate = $row['DailyRate'];
			$HourlyRate = $row['HourlyRate'];
			$RoomInfo = $row['RoomInfo'];
		}
	}
?>
<html>
	<head>
		<style>
			.
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h5>Cek In Kamar <?php echo $RoomNumber; ?></h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									<input type="radio" name="rdRateType" id="rdRateType" value="Daily" checked>
									Harian :
									<input id="hdnRoomID" name="hdnRoomID" type="hidden" <?php echo 'value="'.$RoomID.'"'; ?> />
									<input id="hdnDailyRate" name="hdnDailyRate" type="hidden" <?php echo 'value="'.$DailyRate.'"'; ?> />
									<input id="hdnHourlyRate" name="hdnHourlyRate" type="hidden" <?php echo 'value="'.$HourlyRate.'"'; ?> />
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtStartDate" name="txtStartDate" type="text" class="form-control-custom DatePickerFromNow" placeholder="Dari Tanggal" />
									</div>
								</div>
								<div style="float: left;">
								-
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtEndDate" name="txtEndDate" type="text" class="form-control-custom DatePickerFromNow" placeholder="Sampai Tanggal" />
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									<input type="radio" name="rdRateType" id="rdRateType" value="Hourly" >
									Per Jam :
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtStartDateHourly" name="txtStartDateHourly" type="text" class="form-control-custom DatePickerFromNow" placeholder="Tanggal" />
									</div>
								</div>
								<div style="float: left;">
									<select id="ddlStartHour" name="ddlStartHour" class="form-control-custom">
										<option value="00:00:00">00:00</option>
										<option value="01:00:00">01:00</option>
										<option value="02:00:00">02:00</option>
										<option value="03:00:00">03:00</option>
										<option value="04:00:00">04:00</option>
										<option value="05:00:00">05:00</option>
										<option value="06:00:00">06:00</option>
										<option value="07:00:00">07:00</option>
										<option value="08:00:00">08:00</option>
										<option value="09:00:00">09:00</option>
										<option value="10:00:00">10:00</option>
										<option value="11:00:00">11:00</option>
										<option value="12:00:00">12:00</option>
										<option value="13:00:00">13:00</option>
										<option value="14:00:00">14:00</option>
										<option value="15:00:00">15:00</option>
										<option value="16:00:00">16:00</option>
										<option value="17:00:00">17:00</option>
										<option value="18:00:00">18:00</option>
										<option value="19:00:00">19:00</option>
										<option value="20:00:00">20:00</option>
										<option value="21:00:00">21:00</option>
										<option value="22:00:00">22:00</option>
										<option value="23:00:00">23:00</option>
									</select>
								</div>
								<div style="float: left;margin: 0 10px 0 10px;">
								-
								</div>
								<div style="float: left;">
									<select id="ddlEndHour" name="ddlEndHour" class="form-control-custom">
										<option value="00:00:00">00:00</option>
										<option value="01:00:00">01:00</option>
										<option value="02:00:00">02:00</option>
										<option value="03:00:00">03:00</option>
										<option value="04:00:00">04:00</option>
										<option value="05:00:00">05:00</option>
										<option value="06:00:00">06:00</option>
										<option value="07:00:00">07:00</option>
										<option value="08:00:00">08:00</option>
										<option value="09:00:00">09:00</option>
										<option value="10:00:00">10:00</option>
										<option value="11:00:00">11:00</option>
										<option value="12:00:00">12:00</option>
										<option value="13:00:00">13:00</option>
										<option value="14:00:00">14:00</option>
										<option value="15:00:00">15:00</option>
										<option value="16:00:00">16:00</option>
										<option value="17:00:00">17:00</option>
										<option value="18:00:00">18:00</option>
										<option value="19:00:00">19:00</option>
										<option value="20:00:00">20:00</option>
										<option value="21:00:00">21:00</option>
										<option value="22:00:00">22:00</option>
										<option value="23:00:00">23:00</option>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama :
								</div>
								<div class="col-md-3">
									<input id="txtCustomerName" name="txtCustomerName" type="text" class="form-control-custom" placeholder="Nama" required />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tanggal Lahir :
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtBirthDate" name="txtBirthDate" type="text" class="form-control-custom DatePickerMonthYearUntilNow" placeholder="Tanggal Lahir" required />
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									No HP :
								</div>
								<div class="col-md-3">
									<input id="txtPhoneNumber" name="txtPhoneNumber" type="text" class="form-control-custom" placeholder="No HP" required />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Alamat :
								</div>
								<div class="col-md-6">
									<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" placeholder="Alamat" required ></textarea>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Down Payment :
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtDownPaymentDate" name="txtDownPaymentDate" type="text" class="form-control-custom DatePickerFromNow" placeholder="Tanggal Down Payment" />
									</div>
								</div>
								<div class="col-md-3">
									<input id="txtDownPaymentAmount" name="txtDownPaymentAmount" style="text-align:right;" type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Down Payment" />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Pelunasan :
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtPaymentDate" name="txtPaymentDate" type="text" class="form-control-custom DatePickerFromNow" placeholder="Tanggal Pelunasan" />
									</div>
								</div>
								<div class="col-md-3">
									<input id="txtPaymentAmount" name="txtPaymentAmount" type="text" style="text-align:right;" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Pelunasan" />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Keterangan :
								</div>
								<div class="col-md-6">
									<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" placeholder="Keterangan" ></textarea>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">
									Total :
								</div>
								<div class="col-md-3">
									<input type="text" id="txtGrandTotal" style="text-align:right;" value="0.00" name="txtGrandTotal" class="form-control-custom" readonly />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<button class="btn btn-default" id="btnSave" type="button" onclick="SubmitForm('./Transaction/CheckIn/Insert.php');" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				if($("input[name=rdRateType]:checked").val() == "Daily") {
					$("#txtStartDateHourly").attr("disabled", true);
					$("#txtStartDateHourly").attr("readonly", true);
					$("#ddlStartHour").attr("disabled", true);
					$("#ddlEndHour").attr("disabled", true);
					$("#txtStartDate").attr("disabled", false);
					$("#txtStartDate").attr("readonly", false);
					$("#txtEndDate").attr("disabled", false);
					$("#txtEndDate").attr("readonly", false);
				}
				else {
					$("#txtStartDateHourly").attr("disabled", false );
					$("#txtStartDateHourly").attr("readonly", false );
					$("#ddlStartHour").attr("disabled", false);
					$("#ddlEndHour").attr("disabled", false);
					$("#txtStartDate").attr("disabled", true);
					$("#txtStartDate").attr("readonly", true);
					$("#txtEndDate").attr("disabled", true);
					$("#txtEndDate").attr("readonly", true);
				}
				$("input[name=rdRateType]:radio").change(function() {
					if(this.value == "Daily") {
						$("#txtStartDateHourly").attr("disabled", true);
						$("#txtStartDateHourly").attr("readonly", true);
						$("#ddlStartHour").attr("disabled", true);
						$("#ddlEndHour").attr("disabled", true);
						$("#txtStartDate").attr("disabled", false);
						$("#txtStartDate").attr("readonly", false);
						$("#txtEndDate").attr("disabled", false);
						$("#txtEndDate").attr("readonly", false);
					}
					else {
						$("#txtStartDateHourly").attr("disabled", false );
						$("#txtStartDateHourly").attr("readonly", false );
						$("#ddlStartHour").attr("disabled", false);
						$("#ddlEndHour").attr("disabled", false);
						$("#txtStartDate").attr("disabled", true);
						$("#txtStartDate").attr("readonly", true);
						$("#txtEndDate").attr("disabled", true);
						$("#txtEndDate").attr("readonly", true);
					}
				});
			});
		</script>
	</body>
</html>
