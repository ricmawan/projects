<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$EmployeeID = mysql_real_escape_string($_GET['ID']);
		$EmployeeName = "";
		$StartDate = "";
		$EndDate = "";
		$IsEdit = 0;
		$DailySalary = 0;
		$MenuID = "";
		$EditMenuID = "";
		$DeleteMenuID = "";
		
		if($EmployeeID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						EmployeeID,
						EmployeeName,
						CASE
							WHEN StartDate = '0000-00-00'
							THEN ''
							ELSE DATE_FORMAT(StartDate, '%d-%m-%Y') 
						END AS StartDate,
						CASE
							WHEN EndDate = '0000-00-00'
							THEN ''
							ELSE DATE_FORMAT(EndDate, '%d-%m-%Y') 
						END AS EndDate,
						IFNULL(DailySalary, 0) AS DailySalary
					FROM
						master_Employee
					WHERE
						EmployeeID = $EmployeeID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$EmployeeId = $row['EmployeeID'];
			$EmployeeName = $row['EmployeeName'];
			$StartDate = $row['StartDate'];
			$EndDate = $row['EndDate'];
			$DailySalary = $row['DailySalary'];
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
						<h2><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Karyawan</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-5" id="PostForm" method="POST" action="" >
							Nama Karyawan:<br />
							<input id="hdnEmployeeID" name="hdnEmployeeID" type="hidden" <?php echo 'value="'.$EmployeeID.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtEmployeeName" name="txtEmployeeName" type="text" class="form-control" placeholder="Nama " required   <?php echo 'value="'.$EmployeeName.'"'; ?> />
							<br />
							Mulai Bekerja:<br />
							<input id="txtStartDate" name="txtStartDate" type="text" class="form-control DatePickerMonthYearGlobal" placeholder="Tanggal Mulai " <?php echo 'value="'.$StartDate.'"'; ?> />
							<br />
							Berhenti Bekerja:<br />
							<input id="txtEndDate" name="txtEndDate" type="text" class="form-control DatePickerMonthYearGlobal" placeholder="Tanggal Berakhir " <?php echo 'value="'.$EndDate.'"'; ?> />
							<br />
							Gaji Harian:<br />
							<input id="txtDailySalary" name="txtDailySalary" type="text" class="form-control" placeholder="Gaji Harian" <?php echo 'value="'.number_format($DailySalary,2,".",",").'"'; ?> onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" required />
							<br />
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="Validate();" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function Validate() {
				var txtFromDate = $("#txtStartDate").val();
				var txtToDate = $("#txtEndDate").val();
				var PassValidate = 1;
				if(txtFromDate != "" && txtToDate != "") {
					var FromDate = txtFromDate.split("-");
					FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
					var ToDate = txtToDate.split("-");
					ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
					if(FromDate > ToDate) {
						$("#txtEndDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						PassValidate = 0;
					}
				}
				if(PassValidate == 1) SubmitForm('./Master/Employee/Insert.php');
			}
		</script>
	</body>
</html>