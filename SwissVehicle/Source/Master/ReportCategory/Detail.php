<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];		
		$ReportCategoryID = mysql_real_escape_string($_GET['ID']);
		$ReportCategoryName = "";
		$ReportCategoryType = "";
		$IsEdit = 0;
		
		if($ReportCategoryID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						RC.ReportCategoryID,
						RC.ReportCategoryName,
						RC.ReportCategoryType
					FROM
						master_reportcategory RC
					WHERE
						RC.ReportCategoryID = $ReportCategoryID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$ReportCategoryID = $row['ReportCategoryID'];
			$ReportCategoryName = $row['ReportCategoryName'];
			$ReportCategoryType = $row['ReportCategoryType'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Kategori Laporan</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Kategori Laporan :
								</div>
								<div class="col-md-3">
									<input id="hdnReportCategoryType" type="hidden" <?php echo 'value="'.$ReportCategoryType.'"'; ?> />
									<input id="hdnReportCategoryID" name="hdnReportCategoryID" type="hidden" <?php echo 'value="'.$ReportCategoryID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="txtReportCategoryName" name="txtReportCategoryName" type="text" class="form-control-custom" placeholder="Kategori Laporan" required <?php echo 'value="'.$ReportCategoryName.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tipe :
								</div>
								<div class="col-md-3">
									<select id="ddlCategoryType" name="ddlCategoryType" class="form-control-custom" >
										<option value="Spare Part">Spare Part</option>
										<option value="Peralatan">Peralatan</option>
									</select>
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick='SubmitForm("./Master/ReportCategory/Insert.php")' ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				$("#ddlCategoryType").val($("#hdnReportCategoryType").val());
			});
		</script>
	</body>
</html>
