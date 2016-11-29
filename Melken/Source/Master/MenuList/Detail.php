<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];		
		$MenuListID = mysql_real_escape_string($_GET['ID']);
		$MenuName = "";
		$MenuListCategoryID = 0;
		$Price = "0.00";
		$IsEdit = 0;
		
		if($MenuListID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						ML.MenuName,
						ML.MenuListCategoryID,
						ML.Price
					FROM
						master_menulist ML
					WHERE
						ML.MenuListID = $MenuListID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$MenuName = $row['MenuName'];
			$Price = number_format($row['Price'],2,".",",");
			$MenuListCategoryID = $row['MenuListCategoryID'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Menu</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Kategori Menu :
								</div>
								<div class="col-md-4">
									<select id="ddlCategory" name="ddlCategory" class="form-control-custom" >
										<?php
											$sql = "SELECT MenuListCategoryID, MenuListCategoryName FROM master_menulistcategory";
											if (! $result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											
											while($row = mysql_fetch_array($result)) {
												if($MenuListCategoryID == $row['MenuListCategoryID']) echo "<option selected value='".$row['MenuListCategoryID']."' >".$row['MenuListCategoryName']."</option>";
												else echo "<option value='".$row['MenuListCategoryID']."' >".$row['MenuListCategoryName']."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Menu :
								</div>
								<div class="col-md-4">
									<input id="hdnMenuListID" name="hdnMenuListID" type="hidden" <?php echo 'value="'.$MenuListID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="txtMenuName" name="txtMenuName" type="text" class="form-control-custom" placeholder="Nama Menu" required <?php echo 'value="'.$MenuName.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Harga :
								</div>
								<div class="col-md-4">
									<input id="txtPrice" name="txtPrice" type="text" class="form-control-custom" placeholder="Harga" style="text-align:right;" <?php echo 'value="'.$Price.'"'; ?> onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
								</div>
							</div>
							<br />
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick='SubmitForm("./Master/MenuList/Insert.php")' ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
