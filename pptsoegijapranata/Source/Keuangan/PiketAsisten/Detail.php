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
						master_asisten MK
					WHERE
						MK.AsistenID = ".$Id;

				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}
				$row = mysql_fetch_row($result);
				$Id = $row[0];
				$Nama = $row[1];

				$sql = "SELECT
						PA.TanggalPiket
					FROM
						piket_asisten PA
					WHERE
						PA.AsistenID = ".$Id."
						AND MONTH(PA.TanggalPiket) = MONTH(NOW())
						AND YEAR(PA.TanggalPiket) = YEAR(NOW())";

				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}

				$Tanggal = array();
				while($row=mysql_fetch_row($result)) {
					array_push($Tanggal, $row[0]);
				}
				$Tanggal = implode(", ", $Tanggal);
			}
		}
	}
?>
<html>
	<head>
		<!-- loads mdp -->
		<script type="text/javascript" src="assets/js/jquery-ui.multidatespicker.js"></script>	
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Piket Asisten</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Ubah"; ?> Data Piket Asisten</strong>  
					</div>
					<div class="panel-body">
						<form id="PostForm" method="POST" action="" >
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="hdnTanggal" name="hdnTanggal" type="hidden" <?php echo 'value="'.$Tanggal.'"'; ?> />
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-2">
										Nama Asisten :
									</div>
									<div class="col-md-3">
										<?php echo $Nama; ?>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-2">
										Tanggal :
									</div>
									<div class="col-md-3">
										<div id="withAltField" class="box">
											<div id="dvTanggal">
											</div>
											<br />
											<input class="form-control" type="hidden" id="txtTanggal" name="txtTanggal" readonly <?php echo 'value="'.$Tanggal.'"'; ?> />
										</div>
									</div>
								</div>
							</div>
							<br />
						</form>	
						<button class="btn btn-default" id="btnSave" onclick="SubmitForm('./Keuangan/PiketAsisten/Insert.php');" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				var today = new Date();
				var currentDate = today.getDate();
				var date = new Date();
				var ArrayTanggal = "";
				if($("#hdnTanggal").val() != "") {
					ArrayTanggal =  new Array();
					var tanggal = $("#hdnTanggal").val().split(",");
					var tanggaltemp;
					for(var i=0; i< tanggal.length; i++) {
						tanggaltemp = tanggal[i].split("-");
						ArrayTanggal.push(date.setDate(parseInt(tanggaltemp[2])));
					}
				}
				$('#dvTanggal').multiDatesPicker({
					minDate: "-" + (currentDate - 1) + "D",
					maxDate: "+1M -" + currentDate + "D",
					altField: '#txtTanggal',
					dateFormat: 'yy-mm-dd',
					beforeShowDay: $.datepicker.noWeekends,
					addDates: ArrayTanggal
				});
//				$('#dvTanggal').multiDatesPicker('value', '5-1-2015');
			});
		</script>
	</body>
</html>
