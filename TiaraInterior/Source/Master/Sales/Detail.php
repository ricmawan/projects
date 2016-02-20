<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$SalesID = mysql_real_escape_string($_GET['ID']);
		$SalesName = "";
		$Address = "";
		$Telephone = "";
		$IsEdit = 0;
		
		if($SalesID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						SalesID,
						SalesName,
						Address,
						Telephone
					FROM
						master_sales
					WHERE
						SalesID = $SalesID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$SalesID = $row['SalesID'];
			$SalesName = $row['SalesName'];
			$Address = $row['Address'];
			$Telephone = $row['Telephone'];
		}
	}
?>
<html>
	<head>
		<style>
			#txtAddress {
				height: 100px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Sales</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Sales :
								</div>
								<div class="col-md-3">
									<input id="hdnSalesID" name="hdnSalesID" type="hidden" <?php echo 'value="'.$SalesID.'"'; ?> />
								<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
								<input id="txtSalesName" name="txtSalesName" type="text" class="form-control-custom" placeholder="Nama Sales" required   <?php echo 'value="'.$SalesName.'"'; ?> />							
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Telepon :
								</div>
								<div class="col-md-3">
									<input id="txtTelephone" name="txtTelephone" type="text" class="form-control-custom" placeholder="Telepon" <?php echo 'value="'.$Telephone.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Alamat :
								</div>
								<div class="col-md-4">
									<textarea id="txtAddress" name="txtAddress" class="form-control-custom" placeholder="Alamat"> <?php echo $Address; ?></textarea>
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Sales/Insert.php');" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>