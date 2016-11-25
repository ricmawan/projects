<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];		
		$FuelTypeID = mysql_real_escape_string($_GET['ID']);
		$FuelTypeName = "";
		$Price = 0.00;
		$IsEdit = 0;
		
		if($FuelTypeID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						MF.FuelTypeID,
						MF.FuelTypeName,
						MF.Price
					FROM
						master_fueltype MF
					WHERE
						MF.FuelTypeID = $FuelTypeID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$FuelTypeID = $row['FuelTypeID'];
			$FuelTypeName = $row['FuelTypeName'];
			$Price = $row['Price'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Jenis BBM</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Jenis BBM :
								</div>
								<div class="col-md-3">
									<input id="hdnFuelTypeID" name="hdnFuelTypeID" type="hidden" <?php echo 'value="'.$FuelTypeID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="txtFuelTypeName" name="txtFuelTypeName" type="text" class="form-control-custom" placeholder="Jenis BBM" required <?php echo 'value="'.$FuelTypeName.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Harga :
								</div>
								<div class="col-md-3">
									<input id="txtPrice" name="txtPrice" type="text" class="form-control-custom" placeholder="Harga" style="text-align:right;" <?php echo 'value="'.number_format($Price,2,".",",").'"'; ?> onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick='SubmitForm("./Master/FuelType/Insert.php")' ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
