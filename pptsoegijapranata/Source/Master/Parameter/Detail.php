<?php
	if(isset($_GET['id'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$Id = $_GET['id'];
		$Nama = "";
		$Value = "";
		$Keterangan = "";
		$IsNumber = "";
		$IsEdit = 0;
		
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			if($Id !=0) {
				$IsEdit = 1;
				//$Content = "Place the content here";
				$sql = "SELECT
							*
						FROM
							master_parameter
						WHERE
							ParameterID = $Id";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Nama = $row[1];
				$Value = $row[2];
				$Keterangan = $row[3];
				$IsNumber = $row[4];
				
			}
		}
	}
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Master Data Parameter</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data Parameter</strong>  
					</div>
					<div class="panel-body">
						<form class="col-md-6" id="PostForm" method="POST" action="" >
							Nama:<br />
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="txtNama" name="txtNama" type="text" class="form-control" placeholder="Nama" required <?php echo 'value="'.$Nama.'"'; ?> />
							<!--<br />-->
							<br />
							Nilai:<br />
							<?php 
								if($IsNumber == 0) { echo '<input id="txtValue" name="txtValue" type="text" class="form-control" placeholder="Value" required value="'.$Value.'" />'; }
								else { echo '<input id="txtValue" name="txtValue" onkeypress="return isNumberKey(event)" type="text" class="form-control" placeholder="Value" required value="'.$Value.'" />'; }
							?>
							<br />
							Keterangan:<br />
							<textarea placeholder="Keterangan" id="txtKeterangan" name="txtKeterangan" AdultRequired><?php echo $Keterangan; ?></textarea>
							<br />
							<input type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Parameter/Insert.php');" />
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
