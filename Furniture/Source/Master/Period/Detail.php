<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$PeriodID = mysql_real_escape_string($_GET['ID']);
		$StartDate = "";
		$EndDate = "";
		$IsEdit = 0;
			
		if($PeriodID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						PeriodID,
						DATE_FORMAT(StartDate, '%d-%m-%Y') AS StartDate,
						DATE_FORMAT(EndDate, '%d-%m-%Y') AS EndDate
					FROM
						master_period
					WHERE
						PeriodID = $PeriodID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$PeriodID = $row['PeriodID'];
			$StartDate = $row['StartDate'];
			$EndDate = $row['EndDate'];
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
						<h2><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Periode Gaji</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-5" id="PostForm" method="POST" action="" >
							Tanggal Mulai:<br />
							<input id="hdnPeriodID" name="hdnPeriodID" type="hidden" <?php echo 'value="'.$PeriodID.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtStartDate" name="txtStartDate" type="text" class="form-control DatePickerMonthYearGlobal" placeholder="Tanggal Mulai " required <?php echo 'value="'.$StartDate.'"'; ?> />
							<br />
							Tanggal Berakhir:<br />
							<input id="txtEndDate" name="txtEndDate" type="text" class="form-control DatePickerMonthYearGlobal" placeholder="Tanggal Berakhir " required <?php echo 'value="'.$EndDate.'"'; ?> />
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
				if(txtFromDate != "" && txtToDate != "") {
					var FromDate = txtFromDate.split("-");
					FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
					var ToDate = txtToDate.split("-");
					ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
					if(FromDate > ToDate) {
						$("#txtEndDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					}
					else {
						SubmitForm('./Master/Period/Insert.php');
					}
				}
			}
		</script>
	</body>
</html>