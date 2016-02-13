<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$SupplierID = mysql_real_escape_string($_GET['ID']);
		$SupplierName = "";
		$Address = "";
		$Telephone = "";
		$IsEdit = 0;
		
		if($SupplierID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
					SupplierID,
					SupplierName,
					Address,
					Telephone
				FROM
					master_supplier
				WHERE
					SupplierID = $SupplierID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$SupplierId = $row['SupplierID'];
			$SupplierName = $row['SupplierName'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Supplier</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Supplier :
								</div>
								<div class="col-md-3">
									<input id="hdnSupplierID" name="hdnSupplierID" type="hidden" <?php echo 'value="'.$SupplierID.'"'; ?> />
								<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
								<input id="txtSupplierName" name="txtSupplierName" type="text" class="form-control-custom" placeholder="Nama supplier" required   <?php echo 'value="'.$SupplierName.'"'; ?> />								
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Telepon :
								</div>
								<div class="col-md-3">
									<input id="txtTelephone" name="txtTelephone" type="text" class="form-control-custom" placeholder="Telephone" <?php echo 'value="'.$Telephone.'"'; ?> />
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
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Supplier/Insert.php');" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>