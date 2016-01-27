<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$ProjectID = mysql_real_escape_string($_GET['ID']);
		$ProjectName = "";
		$Remarks = "";
		$IsEdit = 0;
		$IsDone = 0;
		$Data = "";
		if($ProjectID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
					ProjectID,
					ProjectName,
					Remarks,
					IsDone					
				FROM
					master_project
				WHERE
					ProjectID = $ProjectID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$ProjectId = $row['ProjectID'];
			$ProjectName = $row['ProjectName'];
			$Remarks = $row['Remarks'];
			$IsDone = $row['IsDone'];
		}
	}
?>
<html>
	<head>
		<style>
			#txtRemarks {
				height: 100px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Proyek</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-5">
									Nama Proyek:<br />
									<input id="hdnProjectID" name="hdnProjectID" type="hidden" <?php echo 'value="'.$ProjectID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnIsDone" name="hdnIsDone" type="hidden" <?php echo 'value="'.$IsDone.'"'; ?> />
									<input id="txtProjectName" name="txtProjectName" type="text" class="form-control" placeholder="Nama Proyek" required   <?php echo 'value="'.$ProjectName.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-5">
									Status:<br />
									<select class="form-control" name="ddlIsDone" id="ddlIsDone" required >
										<option value="0">Belum Selesai</option>
										<option value="1">Sudah Selesai</option>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-5">
									Keterangan:<br />
									<textarea placeholder="Keterangan" id="txtRemarks" class="form-control" name="txtRemarks" ><?php echo $Remarks; ?></textarea>
								</div>
								
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Project/Insert.php');" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				$("#ddlIsDone").val($("#hdnIsDone").val());
			});
		</script>
	</body>
</html>
