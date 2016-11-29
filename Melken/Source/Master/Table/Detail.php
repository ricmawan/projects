<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];		
		$TableID = mysql_real_escape_string($_GET['ID']);
		$TableNumber = "";
		$IsEdit = 0;
		
		if($TableID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						MT.TableID,
						MT.TableNumber
					FROM
						master_table MT
					WHERE
						MT.TableID = $TableID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$TableID = $row['TableID'];
			$TableNumber = $row['TableNumber'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Meja</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nomor Meja :
								</div>
								<div class="col-md-3">
									<input id="hdnTableID" name="hdnTableID" type="hidden" <?php echo 'value="'.$TableID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="txtTableNumber" name="txtTableNumber" type="text" class="form-control-custom" placeholder="Nomor Meja" required <?php echo 'value="'.$TableNumber.'"'; ?> />
								</div>
							</div>
							<div class="row" style="display: none;">
								<div class="col-md-2 labelColumn">
									test :
								</div>
								<div class="col-md-3">
									<input id="txtPrice" name="txtPrice" type="text" class="form-control-custom" placeholder="Harga" style="text-align:right;" />
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick='SubmitForm("./Master/Table/Insert.php")' ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
