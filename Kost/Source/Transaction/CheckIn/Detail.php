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
									<input type="radio" name="rdRateType" id="rdRateType" onclick="Calculate();" value="Daily" checked>
									Harian :
									<input id="hdnRoomID" name="hdnRoomID" type="hidden" <?php echo 'value="'.$RoomID.'"'; ?> />
									<input id="hdnDailyRate" name="hdnDailyRate" type="hidden" <?php echo 'value="'.$DailyRate.'"'; ?> />
									<input id="hdnHourlyRate" name="hdnHourlyRate" type="hidden" <?php echo 'value="'.$HourlyRate.'"'; ?> />
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtStartDate" onchange="Calculate();" name="txtStartDate" type="text" class="form-control-custom DatePickerFromNow" placeholder="Dari Tanggal" />
									</div>
								</div>
								<div style="float: left;">
								-
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtEndDate" onchange="Calculate();" name="txtEndDate" type="text" class="form-control-custom DatePickerFromNow" placeholder="Sampai Tanggal" />
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
										<input id="txtStartDateHourly" name="txtStartDateHourly" type="text" class="form-control-custom DatePickerFromNow" placeholder="Tanggal" />
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
									<textarea id="txtAddress" name="txtAddress" class="form-control-custom" placeholder="Alamat" required ></textarea>
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
									<input id="txtDownPaymentAmount" name="txtDownPaymentAmount" style="text-align:right;" type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" value="0.00" placeholder="Down Payment" />
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
									<input id="txtPaymentAmount" name="txtPaymentAmount" type="text" style="text-align:right;" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" value="0.00" placeholder="Pelunasan" />
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
							$("#txtGrandTotal").val(returnRupiah(dailyrate.toString()));
						}
						else {
							var timediff = Math.abs(EndDate.getTime() - StartDate.getTime());
							var diffDays = Math.ceil(timediff / (1000 * 3600 * 24)); 
							Total = diffDays * dailyrate;
							$("#txtGrandTotal").val(returnRupiah(Total.toString()));
						}
					}
				}
				else {
					var ddlStartHour = parseInt($("#ddlStartHour").val());
					var ddlEndHour = parseInt($("#ddlEndHour").val());
					if(ddlStartHour >= ddlEndHour) {
						$("#ddlEndHour").val(addZero(ddlStartHour + 1));
						$("#txtGrandTotal").val(returnRupiah(hourlyRate.toString()));
					}
					else {
						var diffHour = ddlEndHour - ddlStartHour;
						Total = diffHour * hourlyRate;
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
				if(PassValidate == 1) {
					$.ajax({
						url: "./Transaction/CheckIn/Insert.php",
						type: "POST",
						data: $("#PostForm").serialize(),
						dataType: "json",
						success: function(data) {
							if(data.FailedFlag == '0') {
								$.notify(data.Message, "success");
								$("#loading").show();
								$("#page-inner-right").html("");
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
				
				$("#txtEndDate").val(nextDate);
				$("#txtStartDate").val(currentDate);
				$("#txtStartDateHourly").val(currentDate);
				$("#ddlStartHour").val(addZero(currentHour));
				$("#ddlEndHour").val(addZero(currentHour + 1));
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
