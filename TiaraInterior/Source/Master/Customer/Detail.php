<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$CustomerID = mysql_real_escape_string($_GET['ID']);
		$CustomerName = "";
		$Address = "";
		$City = "";
		$Telephone = "";
		$IsEdit = 0;
		
		if($CustomerID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						CustomerID,
						CustomerName,
						Address,
						City,
						Telephone
					FROM
						master_customer
					WHERE
						CustomerID = $CustomerID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$CustomerID = $row['CustomerID'];
			$CustomerName = $row['CustomerName'];
			$Address = $row['Address'];
			$City = $row['City'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Pelanggan</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Pelanggan :
								</div>
								<div class="col-md-3">
									<input id="hdnCustomerID" name="hdnCustomerID" type="hidden" <?php echo 'value="'.$CustomerID.'"'; ?> />
								<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
								<input id="txtCustomerName" name="txtCustomerName" type="text" class="form-control-custom" placeholder="Nama pelanggan" required   <?php echo 'value="'.$CustomerName.'"'; ?> />
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
								<div class="col-md-3">
									<textarea id="txtAddress" name="txtAddress" class="form-control-custom" placeholder="Alamat"> <?php echo $Address; ?></textarea>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Kota :
								</div>
								<div class="col-md-3">
									<input id="txtCity" name="txtCity" type="text" class="form-control-custom" placeholder="Kota" <?php echo 'value="'.$City.'"'; ?> />
								</div>
							</div>

							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Customer/Insert.php');" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>