<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		//include "../../GetPermission.php";
		include "../../DBConfig.php";
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
						MR.RoomNumber,
						MR.RoomInfo,
						CI.CheckInID,
						CASE
							WHEN CI.RateType = 'Daily'
							THEN 'Harian'
							ELSE 'Per Jam'
						END RateType,
						CI.RateType RateTypee,
						DATE_FORMAT(CI.StartDate, '%d-%m-%Y') StartDate,
						DATE_FORMAT(CI.EndDate, '%d-%m-%Y') EndDate,
						DATE_FORMAT(CI.StartDate, '%d-%m-%Y %H:%i') StartDateTime,
						DATE_FORMAT(CI.EndDate, '%d-%m-%Y %H:%i') EndDateTime,
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
						master_room MR
						JOIN transaction_checkin CI
							ON CI.RoomID = MR.RoomID
					WHERE
						MR.RoomID = $RoomID
						AND CI.CheckOutFlag = 0
					ORDER BY
						CI.CheckInID DESC
					LIMIT 1";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$RoomNumber = $row['RoomNumber'];
			$DailyRate = $row['DailyRate'];
			$CheckInID = $row['CheckInID'];
			$HourlyRate = $row['HourlyRate'];
			$RoomInfo = $row['RoomInfo'];
			$RateType = $row['RateType'];
			$RateTypee = $row['RateTypee'];
			$StartDate = $row['StartDate'];
			$StartDateTime = $row['StartDateTime'];
			$StartHour = $row['StartHour'];
			$EndHour = $row['EndHour'];
			$EndDate = $row['EndDate'];
			$EndDateTime = $row['EndDateTime'];
			$CustomerName = $row['CustomerName'];
			$BirthDate = $row['BirthDate'];
			$Phone = $row['Phone'];
			$Address = $row['Address'];
			$DownPaymentDate = $row['DownPaymentDate'];
			$PaymentDate = $row['PaymentDate'];
			$DownPaymentAmount = number_format($row['DownPaymentAmount'],2,".",",");
			$PaymentAmount = number_format($row['PaymentAmount'],2,".",",");
			$Remarks = $row['Remarks'];
			$DailyRate = $row['DailyRate'];
			$HourlyRate = $row['HourlyRate'];				
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
						<h5>Cek Out Kamar <?php echo $RoomNumber; ?></h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<input id="hdnCheckInID" name="hdnCheckInID" type="hidden" <?php echo 'value="'.$CheckInID.'"'; ?> />
							<input id="hdnRoomID" name="hdnRoomID" type="hidden" <?php echo 'value="'.$RoomID.'"'; ?> />
							<input id="hdnDailyRate" name="hdnDailyRate" type="hidden" <?php echo 'value="'.$DailyRate.'"'; ?> />
							<input id="hdnHourlyRate" name="hdnHourlyRate" type="hidden" <?php echo 'value="'.$HourlyRate.'"'; ?> />
							<input id="hdnRateType" name="hdnRateType" type="hidden" <?php echo 'value="'.$RateTypee.'"'; ?> />
							<input id="hdnStartDate" name="hdnStartDate" type="hidden" <?php echo 'value="'.$StartDate.'"'; ?> />
							<input id="hdnEndDate" name="hdnEndDate" type="hidden" <?php echo 'value="'.$EndDate.'"'; ?> />
							<input id="hdnStartHour" name="hdnStartHour" type="hidden" <?php echo 'value="'.$StartHour.'"'; ?> />
							<input id="hdnEndHour" name="hdnEndHour" type="hidden" <?php echo 'value="'.$EndHour.'"'; ?> />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Sewa <?php echo $RateType; ?>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Dari Tanggal :
								</div>
								<div class="col-md-4">
									<?php echo $StartDateTime." - ".$EndDateTime; ?>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama :
								</div>
								<div class="col-md-3">
									<?php echo $CustomerName?>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tanggal Lahir :
								</div>
								<div class="col-md-3">
									<?php echo $BirthDate; ?>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									No HP :
								</div>
								<div class="col-md-3">
									<?php echo $Phone; ?>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Alamat :
								</div>
								<div class="col-md-6">
									<?php echo $Address; ?>
								</div>
							</div>
							<br />
							<div class="row payment">
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
								<br />
								<br />
							</div>
							<div class="row payment">
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
									<input id="txtPaymentAmount" name="txtPaymentAmount" type="text" style="text-align:right;" class="form-control-custom" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Pelunasan" <?php echo 'value="'.$PaymentAmount.'"'; ?> />
								</div>
								<br />
								<br />
							</div>
							<div class="row payment">
								<div class="col-md-2">
									Total :
								</div>
								<div class="col-md-3">
									<input type="text" id="txtGrandTotal" style="text-align:right;" value="0.00" name="txtGrandTotal" class="form-control-custom" readonly />
								</div>
								<br />
								<br />
							</div>
							<div class="row">
								<div class="col-md-12">
									<button class="btn btn-default" id="btnSave" type="button" onclick="Save();" ><i class="fa fa-save "></i> Check Out</button>&nbsp;&nbsp;
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
				if($("#hdnRateType").val() == "Daily") {
					var txtEndDate = $("#hdnEndDate").val();
					var txtStartDate = $("#hdnStartDate").val();
					var StartDate = txtStartDate.split("-");
					StartDate = new Date(StartDate[1] + "-" + StartDate[0] + "-" + StartDate[2]);
					var EndDate = txtEndDate.split("-");
					EndDate = new Date(EndDate[1] + "-" + EndDate[0] + "-" + EndDate[2]);
					var timediff = Math.abs(EndDate.getTime() - StartDate.getTime());
					var diffDays = Math.ceil(timediff / (1000 * 3600 * 24)); 
					Total = diffDays * dailyrate;
					console.log(diffDays);
					//if(Total < (DownPaymentAmount + PaymentAmount)) $.notify("Total down payment dan pelunasan melebihi jumlah yang harus dibayar!", "warn" );
					$("#txtGrandTotal").val(returnRupiah(Total.toString()));
				}
				else {
					var ddlStartHour = parseInt($("#hdnStartHour").val());
					var ddlEndHour = parseInt($("#hdnEndHour").val());
					var diffHour = ddlEndHour - ddlStartHour;
					Total = diffHour * hourlyRate;
					//if(Total < DownPaymentAmount) $.notify("Total down payment dan pelunasan melebihi jumlah yang harus dibayar!", "warn" );
					$("#txtGrandTotal").val(returnRupiah(Total.toString()));
				}
			}
			
			function Save() {
				var DP = parseFloat($("#txtDownPaymentAmount").val().replace(/\,/g, ""));
				var Payment = parseFloat($("#txtPaymentAmount").val().replace(/\,/g, ""));
				var Total = parseFloat($("#txtGrandTotal").val().replace(/\,/g, ""));
				
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
				
				if((DP + Payment) > Total) {
					$.notify("Total down payment dan pelunasan melebihi jumlah yang harus dibayar!", "warn");
					return false;
				}
				else if((DP + Payment) < Total) {
					$.notify("Total down payment dan pelunasan kurang dari jumlah yang harus dibayar!", "warn");
					return false
				}
				else if((DP + Payment) == Total) {
					var ask=confirm("Apakah anda yakin ingin melakukan check out?");
					if(ask==true) {
						$("#loading").show();
						$.ajax({
							url: "./Transaction/CheckOut/Insert.php",
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
			}
			
			$(document).ready(function() {
				Calculate();
				var DP = parseFloat($("#txtDownPaymentAmount").val().replace(/\,/g, ""));
				var Payment = parseFloat($("#txtPaymentAmount").val().replace(/\,/g, ""));
				var Total = parseFloat($("#txtGrandTotal").val().replace(/\,/g, ""));
				
				if((DP + Payment) == Total) {
					$(".payment").hide();
				}
			});
		</script>
	</body>
</html>
