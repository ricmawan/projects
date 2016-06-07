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
						MR.DailyRate,
						MR.HourlyRate,
						MR.RoomInfo,
						CI.CheckInID,
						CASE
							WHEN CI.RateType = 'Daily'
							THEN 'Harian'
							ELSE 'Per Jam'
						END RateType,
						DATE_FORMAT(CI.StartDate, '%d-%m-%Y %H:%i') StartDate,
						DATE_FORMAT(CI.EndDate, '%d-%m-%Y %H:%i') EndDate,
						CI.CustomerName,
						DATE_FORMAT(CI.BirthDate, '%d-%m-%Y') BirthDate,
						CI.Phone,
						CI.Address,
						DATE_FORMAT(CI.DownPaymentDate, '%d-%m-%Y') DownPaymentDate,
						CI.DownPaymentAmount,
						DATE_FORMAT(CI.PaymentDate, '%d-%m-%Y') PaymentDate,
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
			$StartDate = $row['StartDate'];
			$EndDate = $row['EndDate'];
			$CustomerName = $row['CustomerName'];
			$BirthDate = $row['BirthDate'];
			$Phone = $row['Phone'];
			$Address = $row['Address'];
			$DownPaymentDate = $row['DownPaymentDate'];
			$PaymentDate = $row['PaymentDate'];
			$DownPaymentAmount = $row['DownPaymentAmount'];
			$PaymentAmount = $row['PaymentAmount'];
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
						<h5>Cek In Kamar <?php echo $RoomNumber; ?></h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<input id="hdnCheckInID" name="hdnCheckInID" type="hidden" <?php echo 'value="'.$CheckInID.'"'; ?> />
							<input id="hdnRoomID" name="hdnRoomID" type="hidden" <?php echo 'value="'.$RoomID.'"'; ?> />
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
									<?php echo $StartDate." - ".$EndDate; ?>
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
			function Save() {
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
		</script>
	</body>
</html>
