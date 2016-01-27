<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$BrandID = mysql_real_escape_string($_GET['ID']);
		$BrandName = "";
		$IsEdit = 0;
		
		if($BrandID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						BrandID,
						BrandName				
					FROM
						master_brand
					WHERE
						BrandID = $BrandID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$BrandID = $row['BrandID'];
			$BrandName = $row['BrandName'];
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
						<h2><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Merek</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-5" id="PostForm" method="POST" action="" >
							Merek:<br />
							<input id="hdnBrandID" name="hdnBrandID" type="hidden" <?php echo 'value="'.$BrandID.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtBrandName" name="txtBrandName" type="text" class="form-control" placeholder="Merek" required   <?php echo 'value="'.$BrandName.'"'; ?> />
							<br />
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Brand/Insert.php');" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>