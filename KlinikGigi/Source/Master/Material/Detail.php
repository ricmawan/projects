<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];		
		$MaterialID = mysql_real_escape_string($_GET['ID']);
		$MaterialName = "";
		$SalePrice = 0.00;
		$IsEdit = 0;
		
		if($MaterialID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						MaterialID,
						MaterialName,
						SalePrice
					FROM
						master_material
					WHERE
						MaterialID = $MaterialID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$MaterialID = $row['MaterialID'];
			$MaterialName = $row['MaterialName'];
			$SalePrice = $row['SalePrice'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Material</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Material :
								</div>
								<div class="col-md-3">
									<input id="hdnMaterialID" name="hdnMaterialID" type="hidden" <?php echo 'value="'.$MaterialID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="txtMaterialName" name="txtMaterialName" type="text" class="form-control-custom" placeholder="Nama Material" required <?php echo 'value="'.$MaterialName.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Harga :
								</div>
								<div class="col-md-3">
									<input id="txtSalePrice" name="txtSalePrice" type="text" class="form-control-custom" placeholder="Harga" style="text-align:right;" <?php echo 'value="'.number_format($SalePrice,2,".",",").'"'; ?> onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick='SubmitForm("./Master/Material/Insert.php")' ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
