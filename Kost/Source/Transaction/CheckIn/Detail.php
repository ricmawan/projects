<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//include "../../DBConfig.php";
		//echo $_SERVER['REQUEST_URI'];
		$RoomID = mysql_real_escape_string($_GET['ID']);
		$RoomNumber = "";
		$DailyRate = 0;
		$HourlyRate = 0;
		$RoomInfo = "";
		$CheckInID = 0;
		$BookingID = 0;
		$RateType = "";
		$StartDate = "";
		$EndDate = "";
		$StartHour = "";
		$EndHour = "";
		$CustomerName = "";
		$BirthDate = "";
		$Phone = "";
		$Address = "";
		$DownPaymentDate = "";
		$DownPaymentAmount = "0.00";
		$PaymentDate = "";
		$PaymentAmount = "0.00";
		$Remarks = "";
		$IsEdit = 0;
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
			
			if(isset($_GET['BookingID'])) {
				$BookingID = mysql_real_escape_string($_GET['BookingID']);
				$sql = "SELECT
							BO.RateType,
							DATE_FORMAT(BO.StartDate, '%d-%m-%Y') StartDate,
							DATE_FORMAT(BO.EndDate, '%d-%m-%Y') EndDate,
							DATE_FORMAT(BO.StartDate, '%H') StartHour,
							DATE_FORMAT(BO.EndDate, '%H') EndHour,
							BO.CustomerName,
							DATE_FORMAT(BO.BirthDate, '%d-%m-%Y') BirthDate,
							BO.Phone,
							BO.Address,
							DATE_FORMAT(BO.DownPaymentDate, '%d-%m-%Y') DownPaymentDate,
							BO.DownPaymentAmount,
							BO.Remarks,
							BO.DailyRate,
							BO.HourlyRate
						FROM
							transaction_booking BO
						WHERE
							BO.BookingID = $BookingID";
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_array($result);
				$RateType = $row['RateType'];
				$StartDate = $row['StartDate'];
				$EndDate = $row['EndDate'];
				$StartHour = $row['StartHour'];
				$EndHour = $row['EndHour'];
				$CustomerName = $row['CustomerName'];
				$BirthDate = $row['BirthDate'];
				$Phone = $row['Phone'];
				$Address = $row['Address'];
				$DownPaymentDate = $row['DownPaymentDate'];
				$DownPaymentAmount = $row['DownPaymentAmount'];
				$Remarks = $row['Remarks'];
				$DailyRate = $row['DailyRate'];
				$HourlyRate = $row['HourlyRate'];				
			}
			if(isset($_GET['CheckInID'])) {
				$IsEdit = 1;
				$CheckInID = mysql_real_escape_string($_GET['CheckInID']);
				$sql = "SELECT
							CI.RateType,
							DATE_FORMAT(CI.StartDate, '%d-%m-%Y') StartDate,
							DATE_FORMAT(CI.EndDate, '%d-%m-%Y') EndDate,
							DATE_FORMAT(CI.StartDate, '%H') StartHour,
							DATE_FORMAT(CI.EndDate, '%H') EndHour,
							CI.CustomerName,
							DATE_FORMAT(CI.BirthDate, '%d-%m-%Y') BirthDate,
							CI.Phone,
							CI.Address,
							CASE
								WHEN CI.DownPaymentDate = '0000-00-00'
								THEN ''
								ELSE DATE_FORMAT(CI.DownPaymentDate, '%d-%m-%Y') 
							END DownPaymentDate,
							CI.DownPaymentAmount,
							CASE
								WHEN CI.PaymentDate = '0000-00-00'
								THEN ''
								ELSE DATE_FORMAT(CI.PaymentDate, '%d-%m-%Y') 
							END PaymentDate,
							CI.PaymentAmount,
							CI.Remarks,
							CI.DailyRate,
							CI.HourlyRate
						FROM
							transaction_checkin CI
						WHERE
							CI.CheckInID = $CheckInID";
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_array($result);
				$RateType = $row['RateType'];
				$StartDate = $row['StartDate'];
				$EndDate = $row['EndDate'];
				$StartHour = $row['StartHour'];
				$EndHour = $row['EndHour'];
				$CustomerName = $row['CustomerName'];
				$BirthDate = $row['BirthDate'];
				$Phone = $row['Phone'];
				$Address = $row['Address'];
				$DownPaymentDate = $row['DownPaymentDate'];
				$DownPaymentAmount = number_format($row['DownPaymentAmount'],2,".",",");
				$PaymentDate = $row['PaymentDate'];
				$PaymentAmount = number_format($row['PaymentAmount'],2,".",",");
				$Remarks = $row['Remarks'];
				$DailyRate = $row['DailyRate'];
				$HourlyRate = $row['HourlyRate'];				
			}
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
						<h5>Check In Kamar <?php echo $RoomNumber; ?></h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									<input type="radio" name="rdRateType" id="rdRateType" onclick="Calculate();" value="Daily" checked>
									Harian :
									<input id="hdnRoomID" name="hdnRoomID" type="hidden" <?php echo 'value="'.$RoomID.'"'; ?> />
									<input id="hdnDailyRate" name="hdnDailyRate" type="hidden" <?php echo 'value="'.$DailyRate.'"'; ?> />
									<input id="hdnHourlyRate" name="hdnHourlyRate" type="hidden" <?php echo 'value="'.$HourlyRate.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnCheckInID" name="hdnCheckInID" type="hidden" <?php echo 'value="'.$CheckInID.'"'; ?> />
									<input id="hdnBookingID" name="hdnBookingID" type="hidden" <?php echo 'value="'.$BookingID.'"'; ?> />
									<input id="hdnRateType" name="hdnRateType" type="hidden" <?php echo 'value="'.$RateType.'"'; ?> />
									<input id="hdnStartHour" name="hdnStartHour" type="hidden" <?php echo 'value="'.$StartHour.'"'; ?> />
									<input id="hdnEndHour" name="hdnEndHour" type="hidden" <?php echo 'value="'.$EndHour.'"'; ?> />
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtStartDate" onchange="Calculate();" name="txtStartDate" type="text" class="form-control-custom DatePickerFromNow" placeholder="Dari Tanggal" <?php echo 'value="'.$StartDate.'"'; ?> />
									</div>
								</div>
								<div style="float: left;">
								-
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtEndDate" onchange="Calculate();" name="txtEndDate" type="text" class="form-control-custom DatePickerFromNow" placeholder="Sampai Tanggal" <?php echo 'value="'.$EndDate.'"'; ?> />
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									<input type="radio" name="rdRateType" id="rdRateType" onclick="Calculate();" value="Hourly" >
									Per Jam :
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtStartDateHourly" name="txtStartDateHourly" type="text" class="form-control-custom DatePickerFromNow" placeholder="Tanggal" <?php echo 'value="'.$StartDate.'"'; ?> />
									</div>
								</div>
								<div style="float: left;">
									<select id="ddlStartHour" onchange="Calculate();" name="ddlStartHour" class="form-control-custom">
										<option value="00">00:00</option>
										<option value="01">01:00</option>
										<option value="02">02:00</option>
										<option value="03">03:00</option>
										<option value="04">04:00</option>
										<option value="05">05:00</option>
										<option value="06">06:00</option>
										<option value="07">07:00</option>
										<option value="08">08:00</option>
										<option value="09">09:00</option>
										<option value="10">10:00</option>
										<option value="11">11:00</option>
										<option value="12">12:00</option>
										<option value="13">13:00</option>
										<option value="14">14:00</option>
										<option value="15">15:00</option>
										<option value="16">16:00</option>
										<option value="17">17:00</option>
										<option value="18">18:00</option>
										<option value="19">19:00</option>
										<option value="20">20:00</option>
										<option value="21">21:00</option>
										<option value="22">22:00</option>
										<option value="23">23:00</option>
									</select>
								</div>
								<div style="float: left;margin: 0 10px 0 10px;">
								-
								</div>
								<div style="float: left;">
									<select id="ddlEndHour" name="ddlEndHour" onchange="Calculate();" class="form-control-custom">
										<option value="00">00:00</option>
										<option value="01">01:00</option>
										<option value="02">02:00</option>
										<option value="03">03:00</option>
										<option value="04">04:00</option>
										<option value="05">05:00</option>
										<option value="06">06:00</option>
										<option value="07">07:00</option>
										<option value="08">08:00</option>
										<option value="09">09:00</option>
										<option value="10">10:00</option>
										<option value="11">11:00</option>
										<option value="12">12:00</option>
										<option value="13">13:00</option>
										<option value="14">14:00</option>
										<option value="15">15:00</option>
										<option value="16">16:00</option>
										<option value="17">17:00</option>
										<option value="18">18:00</option>
										<option value="19">19:00</option>
										<option value="20">20:00</option>
										<option value="21">21:00</option>
										<option value="22">22:00</option>
										<option value="23">23:00</option>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama :
								</div>
								<div class="col-md-3">
									<input id="txtCustomerName" name="txtCustomerName" type="text" class="form-control-custom" placeholder="Nama" required <?php echo 'value="'.$CustomerName.'"'; ?> />
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
										<input id="txtBirthDate" name="txtBirthDate" type="text" class="form-control-custom DatePickerMonthYearUntilNow" placeholder="Tanggal Lahir" required <?php echo 'value="'.$BirthDate.'"'; ?> />
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									No HP :
								</div>
								<div class="col-md-3">
									<input id="txtPhoneNumber" name="txtPhoneNumber" type="text" class="form-control-custom" placeholder="No HP" required <?php echo 'value="'.$Phone.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Alamat :
								</div>
								<div class="col-md-6">
									<textarea id="txtAddress" name="txtAddress" class="form-control-custom" placeholder="Alamat" required ><?php echo $Address; ?></textarea>
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
										<input id="txtDownPaymentDate" name="txtDownPaymentDate" type="text" class="form-control-custom DatePickerFromNow" placeholder="Tanggal Down Payment" <?php echo 'value="'.$DownPaymentDate.'"'; ?> />
									</div>
								</div>
								<div class="col-md-3">
									<input id="txtDownPaymentAmount" name="txtDownPaymentAmount" style="text-align:right;" type="text" class="form-control-custom" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Down Payment" <?php echo 'value="'.$DownPaymentAmount.'"'; ?> />
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
										<input id="txtPaymentDate" name="txtPaymentDate" type="text" class="form-control-custom DatePickerFromNow" placeholder="Tanggal Pelunasan" <?php echo 'value="'.$PaymentDate.'"'; ?> />
									</div>
								</div>
								<div class="col-md-3">
									<input id="txtPaymentAmount" name="txtPaymentAmount" type="text" style="text-align:right;" class="form-control-custom" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" value="0.00" placeholder="Pelunasan" <?php echo 'value="'.$PaymentAmount.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Keterangan :
								</div>
								<div class="col-md-6">
									<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" placeholder="Keterangan" ><?php echo $Remarks; ?></textarea>
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
									<button class="btn btn-default" id="btnSave" type="button" onclick="Save();" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function Calculate() {
				var Total = 0;
				var dailyrate = parseInt($("#hdnDailyRate").val());
				var hourlyRate = parseInt($("#hdnHourlyRate").val());
				var DownPaymentAmount = parseFloat($("#txtDownPaymentAmount").val().replace(/\,/g, ""));
				var PaymentAmount = parseFloat($("#txtPaymentAmount").val().replace(/\,/g, ""));
				if($("input[name=rdRateType]:checked").val() == "Daily") {
					var txtStartDate = $("#txtStartDate").val();
					var txtEndDate = $("#txtEndDate").val();
					if(txtStartDate != "" && txtEndDate != "") {
						var StartDate = txtStartDate.split("-");
						StartDate = new Date(StartDate[1] + "-" + StartDate[0] + "-" + StartDate[2]);
						var EndDate = txtEndDate.split("-");
						EndDate = new Date(EndDate[1] + "-" + EndDate[0] + "-" + EndDate[2]);
						if(StartDate >= EndDate) {
							//$("#txtEndDate").notify("Tanggal akhir harus lebih besar!", { position:"right", className:"warn", autoHideDelay: 2000 });
							//$("#txtEndDate").focus();
							EndDate = StartDate;
							EndDate.setDate(StartDate.getDate() + 1);
							var nextDate1 = addZero(EndDate.getDate()) + "-" + addZero(EndDate.getMonth() + 1) + "-" + EndDate.getFullYear();
							$("#txtEndDate").val(nextDate1);
							if(dailyrate < (DownPaymentAmount + PaymentAmount)) $.notify("Total down payment dan pelunasan melebihi jumlah yang harus dibayar!", "warn" );
							$("#txtGrandTotal").val(returnRupiah(dailyrate.toString()));
						}
						else {
							var timediff = Math.abs(EndDate.getTime() - StartDate.getTime());
							var diffDays = Math.ceil(timediff / (1000 * 3600 * 24)); 
							Total = diffDays * dailyrate;
							if(Total < (DownPaymentAmount + PaymentAmount)) $.notify("Total down payment dan pelunasan melebihi jumlah yang harus dibayar!", "warn" );
							$("#txtGrandTotal").val(returnRupiah(Total.toString()));
						}
					}
				}
				else {
					var ddlStartHour = parseInt($("#ddlStartHour").val());
					var ddlEndHour = parseInt($("#ddlEndHour").val());
					if(ddlStartHour >= ddlEndHour) {
						$("#ddlEndHour").val(addZero(ddlStartHour + 1));
						if(hourlyRate < DownPaymentAmount) $.notify("Total down payment dan pelunasan melebihi jumlah yang harus dibayar!", "warn" );
						$("#txtGrandTotal").val(returnRupiah(hourlyRate.toString()));
					}
					else {
						var diffHour = ddlEndHour - ddlStartHour;
						Total = diffHour * hourlyRate;
						if(Total < DownPaymentAmount) $.notify("Total down payment dan pelunasan melebihi jumlah yang harus dibayar!", "warn" );
						$("#txtGrandTotal").val(returnRupiah(Total.toString()));
					}
				}
			}
			function Save() {
				var PassValidate = 1;
				var FirstFocus = 0;
				if($("input[name=rdRateType]:checked").val() == "Daily") {
					var txtStartDate = $("#txtStartDate").val();
					var txtEndDate = $("#txtEndDate").val();
					if(txtStartDate != "" && txtEndDate != "") {
						var StartDate = txtStartDate.split("-");
						StartDate = new Date(StartDate[1] + "-" + StartDate[0] + "-" + StartDate[2]);
						var EndDate = txtEndDate.split("-");
						EndDate = new Date(EndDate[1] + "-" + EndDate[0] + "-" + EndDate[2]);
						if(StartDate >= EndDate) {
							$("#txtEndDate").notify("Tanggal akhir harus lebih besar!", { position:"right", className:"warn", autoHideDelay: 2000 });
							PassValidate = 0;
							if(FirstFocus == 0) $("#txtEndDate").focus();
							FirstFocus = 1;
						}
					}
				}
				else {
					var ddlStartHour = parseInt($("#ddlStartHour").val());
					var ddlEndHour = parseInt($("#ddlEndHour").val());
					if(ddlStartHour >= ddlEndHour) {
						$("#ddlEndHour").notify("Jam akhir harus lebih besar!", { position:"right", className:"warn", autoHideDelay: 2000 });
					}
				}
				
				$(".form-control-custom").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
				
				if($("#txtDownPaymentAmount").val() != "0.00" && $("#txtDownPaymentDate").val() == "") {
					PassValidate = 0;
					$("#txtDownPaymentDate").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#txtDownPaymentDate").focus();
					FirstFocus = 1;
				}
				
				if($("#txtPaymentAmount").val() != "0.00" && $("#txtPaymentDate").val() == "") {
					PassValidate = 0;
					$("#txtPaymentDate").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#txtPaymentDate").focus();
					FirstFocus = 1;
				}
				
				var Total = $("#txtGrandTotal").val().replace(/\,/g, "");
				var DownPaymentAmount = parseFloat($("#txtDownPaymentAmount").val().replace(/\,/g, ""));
				var PaymentAmount = parseFloat($("#txtPaymentAmount").val().replace(/\,/g, ""));
				if(Total < (DownPaymentAmount + PaymentAmount)) {
					$.notify("Total down payment dan pelunasan melebihi jumlah yang harus dibayar!", "warn" );
					PassValidate = 0;
				}
				
				if(PassValidate == 1) {
					$("#loading").show();
					$.ajax({
						url: "./Transaction/CheckIn/Insert.php",
						type: "POST",
						data: $("#PostForm").serialize(),
						dataType: "json",
						success: function(data) {
							if(data.FailedFlag == '0') {
								$.notify(data.Message, "success");
								$("#loading").hide();
								$("#page-inner-right").html("");
								LoadRoom();
							}
							else {
								$("#loading").hide();
								$.notify(data.Message, "error");					
							}
						},
						error: function(data) {
							$("#loading").hide();
							$.notify("Koneksi gagal", "error");
						}
					});
				}
			}
			
			$(document).ready(function() {
				var d = new Date();
				var currentHour = d.getHours();
				var currentDay = d.getDate();
				var nextDay = d;
				var currentDate = addZero(d.getDate()) + "-" + addZero(d.getMonth() + 1) + "-" + d.getFullYear();
				nextDay.setDate(currentDay + 1);
				var nextDate = addZero(nextDay.getDate()) + "-" + addZero(nextDay.getMonth() + 1) + "-" + nextDay.getFullYear();
				if($("#hdnBookingID").val() != 0 || $("#hdnCheckInID").val() != 0) {
					$("input[name=rdRateType][value=" + $("#hdnRateType").val() + "]").attr("checked", true);
					$("input[name=rdRateType][value=" + $("#hdnRateType").val() + "]").attr("prop", true);
						
					if($("#hdnRateType").val() == "Daily") {
						$("#txtStartDateHourly").val(currentDate);
						$("#ddlStartHour").val(addZero(currentHour));
						$("#ddlEndHour").val(addZero(currentHour + 1));
					}
					else {
						$("#ddlStartHour").val($("#hdnStartHour").val());
						$("#ddlEndHour").val($("#hdnEndHour").val());
						$("#txtEndDate").val(nextDate);
						$("#txtStartDate").val(currentDate);
					}
				}
				else {
					$("#txtEndDate").val(nextDate);
					$("#txtStartDate").val(currentDate);
					$("#txtStartDateHourly").val(currentDate);
					$("#ddlStartHour").val(addZero(currentHour));
					$("#ddlEndHour").val(addZero(currentHour + 1));
				}
				
				if($("input[name=rdRateType]:checked").val() == "Daily") {
					$("#txtStartDateHourly").attr("disabled", true);
					$("#txtStartDateHourly").attr("readonly", true);
					$("#txtStartDateHourly").removeAttr("required");
					$("#ddlStartHour").attr("disabled", true);
					$("#ddlEndHour").attr("disabled", true);
					$("#txtStartDate").attr("disabled", false);
					$("#txtStartDate").attr("readonly", false);
					$("#txtStartDate").attr("required", "required");
					$("#txtEndDate").attr("disabled", false);
					$("#txtEndDate").attr("readonly", false);
					$("#txtEndDate").attr("required", "required");
				}
				else {
					$("#txtStartDateHourly").attr("disabled", false );
					$("#txtStartDateHourly").attr("readonly", false );
					$("#txtStartDateHourly").attr("required", "required" );
					$("#ddlStartHour").attr("disabled", false);
					$("#ddlEndHour").attr("disabled", false);
					$("#txtStartDate").attr("disabled", true);
					$("#txtStartDate").attr("readonly", true);
					$("#txtStartDate").removeAttr("required");
					$("#txtEndDate").attr("disabled", true);
					$("#txtEndDate").attr("readonly", true);
					$("#txtEndDate").removeAttr("required");
				}
				
				
				$("input[name=rdRateType]:radio").change(function() {
					if(this.value == "Daily") {
						$("#txtStartDateHourly").attr("disabled", true);
						$("#txtStartDateHourly").attr("readonly", true);
						$("#txtStartDateHourly").removeAttr("required");
						$("#ddlStartHour").attr("disabled", true);
						$("#ddlEndHour").attr("disabled", true);
						$("#txtStartDate").attr("disabled", false);
						$("#txtStartDate").attr("readonly", false);
						$("#txtStartDate").attr("required", "required");
						$("#txtEndDate").attr("disabled", false);
						$("#txtEndDate").attr("readonly", false);
						$("#txtEndDate").attr("required", "required");
					}
					else {
						$("#txtStartDateHourly").attr("disabled", false );
						$("#txtStartDateHourly").attr("readonly", false );
						$("#txtStartDateHourly").attr("required", "required" );
						$("#ddlStartHour").attr("disabled", false);
						$("#ddlEndHour").attr("disabled", false);
						$("#txtStartDate").attr("disabled", true);
						$("#txtStartDate").attr("readonly", true);
						$("#txtStartDate").removeAttr("required");
						$("#txtEndDate").attr("disabled", true);
						$("#txtEndDate").attr("readonly", true);
						$("#txtEndDate").removeAttr("required");
					}
				});
				Calculate();
			});
		</script>
	</body>
</html>
