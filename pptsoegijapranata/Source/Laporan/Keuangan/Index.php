<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
	//echo $_SERVER['REQUEST_URI'];
	$Content = "";
	$EditFlag = "";
	$DeleteFlag = "";
	if($cek==0) {
		$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
	}
	else {
		//$Content = "Place the content here";
		$row = mysql_fetch_row($result);
		$EditFlag = $row[1];
		$DeleteFlag = $row[2];
		//$Content = "Place the content here";
		$TableColumn = "<td>No</td><td>Kode Client</td><td>Nama Client</td><td>Tanggal</td><td>Jumlah Peserta</td><td>Total</td><td>No Kwitansi</td><td>Keterangan</td>";
		$TableBody = "";
		if(!isset($_SESSION['startDate'])){
			//set the session
			$_SESSION['startDate']=date('d')."-".date('m')."-".date('Y');
			$_SESSION['endDate']=date('d')."-".date('m')."-".date('Y');
		}
		
		if(isset($_POST['hdnPostBack'])) {
			$_SESSION['startDate']=$_POST['startDate'];
			$_SESSION['endDate']=$_POST['endDate'];
		}
		
	
		//no,kode client,nama,kode psikotest,tanggal transaksi,jumlah peserta,jumlah,no kwitansi,lunas keterangan
		$sql = "SELECT
					MK.KlienID,
					MK.Nama,
					TC.TransaksiID,
					DATE_FORMAT(TC.Tanggal, '%d-%m-%Y'),
					COUNT(TRC.TransaksiID),
					TC.Pembayaran,
					TC.UangMuka
				
				FROM
					transaksi_customerservice TC
					
				JOIN 
					transaksi_rincicustomerservice TRC
					
				ON 
					TRC.TransaksiID = TC.TransaksiID
					AND TC.Tanggal BETWEEN '".$_SESSION['startDate']."' AND '".$_SESSION['endDate']."'
				JOIN 
					master_klien MK
				ON
					MK.KlienID = TC.KlienID
					
				GROUP BY TC.TransaksiID";
									 
			if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;			
			}
											
			$RowNumber = 0;
			$total= 0;
			$peserta=0;
			$No = "";
			$Keterangan="";
			for($i = 0; $i< (4- (INT)strlen($row[2])); $i++) {
					$No .= "0";
			}
			while ($row=mysql_fetch_row($result)) {
				
				if ($row[6]<=$row[5]){
					$Keterangan="Lunas";
				}else{
					$Keterangan="Belum Lunas";
				}
				$RowNumber++;
				$jumlah=$row[5]+$row[6];
				$TableBody .= "<tr><td>".$RowNumber."</td><td>".$row[0]."</td><td>".$row[1]."</td><td align='right'>".$row[3]."</td><td align='center'>".$row[4]."</td><td>".number_format(($jumlah),2,".",",")."</td><td align='right'>".$No.$row[2]."/".$row[2].date('m').date('Y')."/PPT/".date('m').date('Y')."</td><td align='center'>".$Keterangan."</td>";	
				$total=$total+$jumlah;
				$peserta=$peserta+$row[4];
			
			}				
			echo "<input type='hidden' id='hdnstartDate' value=" .$_SESSION['startDate']."  />";
			echo "<input type='hidden' id='hdnendDate' value=" .$_SESSION['endDate']."  />";
			
	
		}		
?>

<html>
	<head>
	<script>
	$(function() {
		$( "#startDate" ).datepicker({
		showButtonPanel: true,
		dateFormat: 'yy-mm-dd'
		});
		
		$( "#endDate" ).datepicker({
		showButtonPanel: true,
		dateFormat: 'yy-mm-dd'
		});
	});
	
	</script>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Laporan Keuangan</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<?php
					if($cek == 0) echo $Content;
					else {
				?>
						<div class="panel panel-default">
							<div class="panel-heading">
								Laporan Keuangan
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-3">
										<form id="PostForm" method="POST" action="">
											<div class="row">
												<div class="col-md-12">
												&nbsp;Start Date :
												<input id="startDate" name="startDate" type="text" onchange="loadReport()" class="form-control " placeholder="Tanggal" required value="<?php echo $_SESSION['startDate']; ?>" >
												</br>
												&nbsp;End Date :
												<input id="endDate" name="endDate" type="text" onchange="loadReport()" class="form-control" placeholder="Tanggal" required value="<?php echo $_SESSION['endDate']; ?>" >
												</div>
											</div>
											<input type="hidden" id="hdnPostBack" name="hdnPostBack" value=1 />
										</form>
									</div>
								</div>
								<br />
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover" id="DataTable">
										<thead>				
											<tr>
												<?php echo $TableColumn; ?>
											</tr>
										</thead>
										<tbody>
												<?php echo $TableBody; ?>
										</tbody>
										
									</table>
								</div>
								<br />
								<div class="col-md-12">
									<div class="col-md-2">
										Grand Total:
									</div>
									<div class="col-md-3">
										<input type="text" name="txtTotal" style="text-align:right;" id="txtTotal" class="form-control col-md-3"  <?php echo 'value="'.number_format(($total),2,",",".").'"'; ?> readonly />
									</div>
									
									<button type="button" class="btn btn-default" onclick="{window.location.href='./Laporan/Keuangan/Print.php';}"><i class="fa fa-file-excel-o"></i> Print</button>	
									
								</div>
								
							</div>
						</div>
				<?php
					}
				?>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				$('#DataTable').dataTable();
				$("#startDate").val($("#hdnstartDate").val());
				$("#endDate").val($("#hdnendDate").val());
			});
			function loadReport() {
				var startDate = $("#startDate").val();
				var endDate = $("#endDate").val();
				$("#loading").show();
				$("#page-inner").html("");
				$.ajax({
					url: 'Laporan/Keuangan/',
					type: "POST",
					data: { startDate : startDate, endDate : endDate,hdnPostBack : 1 },
					dataType: "html",
					success: function(data) {
						$("#page-inner").html(data);
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						$("#loading").hide();
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Koneksi gagal", "error");
					}
				});
				
			}
			
		
		</script>
	</body>
</html>
