<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$RoomID = mysql_real_escape_string($_GET['ID']);
		$HourlyRate = "";
		$DailyRate = "";
		$IsEdit = 0;
		$RoomInfo = "";
		$RoomNumber = "";
		
		if($RoomID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						RoomID,
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
			$DailyRate = number_format($row['DailyRate'],2,".",",");
			$HourlyRate = number_format($row['HourlyRate'],2,".",",");
			$RoomInfo = $row['RoomInfo'];
			$RoomNumber = $row['RoomNumber'];
		}
	}
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Kamar</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									No Kamar :
									<input id="hdnRoomID" name="hdnRoomID" type="hidden" <?php echo 'value="'.$RoomID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtRoomNumber" name="txtRoomNumber" type="text" class="form-control-custom" placeholder="No Kamar" required   <?php echo 'value="'.$RoomNumber.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Harga Harian :
								</div>
								<div class="col-md-3">
									<input id="txtDailyRate" name="txtDailyRate" type="text" class="form-control-custom" placeholder="Harga Harian" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" required <?php echo 'value="'.$DailyRate.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Harga per Jam :
								</div>
								<div class="col-md-3">
									<input id="txtHourlyRate" name="txtHourlyRate" type="text" class="form-control-custom" placeholder="Harga per Jam" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" required <?php echo 'value="'.$HourlyRate.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Info Kamar :
								</div>
								<div class="col-md-4">
									<textarea id="txtRoomInfo" name="txtRoomInfo" class="form-control-custom" placeholder="Info"><?php echo $RoomInfo; ?></textarea>
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Room/Insert.php');" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
		</script>
	</body>
</html>
