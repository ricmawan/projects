<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];		
		$ExceptionScheduleID = mysql_real_escape_string($_GET['ID']);
		$BranchID = 1;
		$DayOfWeek = 0;
		$Hour = 0;
		$Minute = "00";
		$IsAdmin = 1;
		$IsEdit = 0;
		
		if($ExceptionScheduleID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						ME.ExceptionScheduleID,
						MB.BranchID,
						ME.DayOfWeek,
						ME.BusinessHour,
						ME.IsAdmin
					FROM
						master_exceptionschedule ME
						JOIN master_branch MB
							ON MB.BranchID = ME.BranchID
					WHERE
						ExceptionScheduleID = $ExceptionScheduleID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$BusinessHour = explode(":", $row['BusinessHour']);
			$ExceptionScheduleID = $row['ExceptionScheduleID'];
			$BranchID = $row['BranchID'];
			$DayOfWeek = $row['DayOfWeek'];
			$Hour = $BusinessHour[0];
			$Minute = $BusinessHour[1];
			$IsAdmin = $row['IsAdmin'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Jadwal Praktek</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Cabang :
								</div>
								<div class="col-md-3">
									<input id="hdnExceptionScheduleID" name="hdnExceptionScheduleID" type="hidden" <?php echo 'value="'.$ExceptionScheduleID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnBranchID" name="hdnBranchID" type="hidden" <?php echo 'value="'.$BranchID.'"'; ?> />
									<input id="hdnDayOfWeek" name="hdnDayOfWeek" type="hidden" <?php echo 'value="'.$DayOfWeek.'"'; ?> />
									<input id="hdnHour" name="hdnHour" type="hidden" <?php echo 'value="'.$Hour.'"'; ?> />
									<input id="hdnMinute" name="hdnMinute" type="hidden" <?php echo 'value="'.$Minute.'"'; ?> />
									<input id="hdnIsAdmin" name="hdnIsAdmin" type="hidden" <?php echo 'value="'.$IsAdmin.'"'; ?> />
									<select id="ddlBranch" name="ddlBranch" class="form-control-custom">
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
							<div class="row">
								<div class="col-md-2 labelColumn">
									Halaman :
								</div>
								<div class="col-md-3">
									<select id="ddlPage" name="ddlPage" class="form-control-custom" >
										<option value=1>Admin</option>
										<option value=0>Registrasi Online</option>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Hari :
								</div>
								<div class="col-md-3">
									<select id="ddlDayOfWeek" name="ddlDayOfWeek" class="form-control-custom" >
										<option value=0>Minggu</option>
										<option value=1>Senin</option>
										<option value=2>Selasa</option>
										<option value=3>Rabu</option>
										<option value=4>Kamis</option>
										<option value=5>Jumat</option>
										<option value=6>Sabtu</option>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Jam :
								</div>
								<div class="col-md-1">
									<select id="ddlHour" name="ddlHour" class="form-control-custom" >
										<option value=0>00</option>
										<option value=1>01</option>
										<option value=2>02</option>
										<option value=3>03</option>
										<option value=4>04</option>
										<option value=5>05</option>
										<option value=6>06</option>
										<option value=7>07</option>
										<option value=8>08</option>
										<option value=9>09</option>
										<option value=10>10</option>
										<option value=11>11</option>
										<option value=12>12</option>
										<option value=13>13</option>
										<option value=14>14</option>
										<option value=15>15</option>
										<option value=16>16</option>
										<option value=17>17</option>
										<option value=18>18</option>
										<option value=19>19</option>
										<option value=20>20</option>
										<option value=21>21</option>
										<option value=22>22</option>
										<option value=23>23</option>
									</select>
								</div>
								<div class="col-md-1" style="width: 30px;padding: 0 5px;text-align: center;">
									-
								</div>
								<div class="col-md-1">
									<select id="ddlMinute" name="ddlMinute" class="form-control-custom" >
										<option value=00>00</option>
										<option value=15>15</option>
										<option value=30>30</option>
										<option value=45>45</option>
									</select>
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/ExceptionSchedule/Insert.php');" ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				var BranchID = $("#hdnBranchID").val();
				var IsAdmin = $("#hdnIsAdmin").val();
				var DayOfWeek = $("#hdnDayOfWeek").val();
				var Hour = $("#hdnHour").val();
				var Minute = $("#hdnMinute").val();

				$("#ddlBranch").val(BranchID);
				$("#ddlPage").val(IsAdmin);
				$("#ddlDayOfWeek").val(DayOfWeek);
				$("#ddlHour").val(Hour);
				$("#ddlMinute").val(Minute);
			});
		</script>
	</body>
</html>
