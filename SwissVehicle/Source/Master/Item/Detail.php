<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];		
		$ItemID = mysql_real_escape_string($_GET['ID']);
		$ItemName = "";
		$ItemCode = "";
		$IsSecond = "";
		$Price = 0.00;
		$IsEdit = 0;
		
		if($ItemID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						MI.ItemID,
						MI.ItemName,
						MI.ItemCode,
						MI.IsSecond,
						MI.Price
					FROM
						master_item MI
					WHERE
						MI.ItemID = $ItemID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$ItemID = $row['ItemID'];
			$ItemName = $row['ItemName'];
			$ItemCode = $row['ItemCode'];
			$IsSecond = $row['IsSecond'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Barang</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Barang :
								</div>
								<div class="col-md-3">
									<input id="hdnItemID" name="hdnItemID" type="hidden" <?php echo 'value="'.$ItemID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="txtItemName" name="txtItemName" type="text" class="form-control-custom" placeholder="Nama Barang" required <?php echo 'value="'.$ItemName.'"'; ?> />
									<input type="hidden" name="hdnIsSecond" id="hdnIsSecond" <?php echo 'value="'.$IsSecond.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Kode Barang :
								</div>
								<div class="col-md-3">
									<input id="txtItemCode" name="txtItemCode" type="text" class="form-control-custom" placeholder="Kode Barang" required <?php echo 'value="'.$ItemCode.'"'; ?> />
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
							<div class="row">
								<div class="col-md-2 labelColumn">
								</div>
								<div class="col-md-3">
									<input type="checkbox" id="chkSecond" name="chkSecond" value=1 /> Dijual Bekas
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick='SubmitForm("./Master/Item/Insert.php")' ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				var IsSecond = $("#hdnIsSecond").val();
				if(IsSecond == true) {
					$("#chkSecond").attr("checked", true);
					$("#chkSecond").prop("checked", true);
				}
			});
		</script>
	</body>
</html>
