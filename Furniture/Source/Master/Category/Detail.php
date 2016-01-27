<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$CategoryID = mysql_real_escape_string($_GET['ID']);
		$CategoryName = "";
		$IsEdit = 0;
		$MenuID = "";
		$EditMenuID = "";
		$DeleteMenuID = "";
		
		if($CategoryID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
					CategoryID,
					CategoryName				
				FROM
					master_category
				WHERE
					CategoryID = $CategoryID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$CategoryId = $row['CategoryID'];
			$CategoryName = $row['CategoryName'];
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
						<h2><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Kategori</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-5" id="PostForm" method="POST" action="" >
							Nama Kategori:<br />
							<input id="hdnCategoryID" name="hdnCategoryID" type="hidden" <?php echo 'value="'.$CategoryID.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtCategoryName" name="txtCategoryName" type="text" class="form-control" placeholder="Nama " required   <?php echo 'value="'.$CategoryName.'"'; ?> />
							<br />
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Category/Insert.php');" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>