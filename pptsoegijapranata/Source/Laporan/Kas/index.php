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
		$TableColumn = "<td>No</td><td>Tanggal</td><td>Keterangan</td><td>Jumlah</td>";
		$TableBody = "";
		if(isset($_POST['hdnPostBack'])) {
			$MonthYear = explode("-", $_POST['ddlMonthYear']);
			$Month = $MonthYear[0];
			$Year = $MonthYear[1];
		}
		else {
			$Month = date('n');
			$Year = date('Y');
		}
		$sql = "SELECT
					TK.TransaksiID,
					DATE_FORMAT(TK.Tanggal, '%d-%m-%Y'),
					TR.Keterangan,
					TR.Jumlah
				FROM
					transaksi_kas TK
				JOIN 
					transaksi_rincikas TR
				ON 
					TK.TransaksiID = TR.TransaksiID
					AND MONTH(TK.Tanggal) = $Month
					AND YEAR(TK.Tanggal) = $Year";
														
			if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;			
			}
											
			$RowNumber = 0;
			$total= 0;
			while ($row=mysql_fetch_row($result)) {
				
				$RowNumber++;
				$TableBody .= "<tr><td>".$RowNumber."</td><td>".$row[1]."</td><td>".$row[2]."</td><td align='right'>".number_format(($row[3]),2,".",",")."</td>";	
				
				$total=$total+$row[3];
			}				
			echo "<input type='hidden' id='hdnMonthYear' value='".$Month."-".$Year."' />";
		}		
?>

<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Laporan Kas Keluar</h2>   
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
								Laporan Kas Keluar
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-3">
										<form id="PostForm" method="POST" action="">
											<select id="ddlMonthYear" onchange="loadReport()" name="ddlMonthYear" class="form-control col-md-3">
												<?php
													$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember');
													$bulan = $array_bulan[date('n')];
													$tahun = date('Y')-2;
													for($i=2; $i>=0;$i--) {
														for($j=0; $j< 12; $j++) {
															echo "<option value=".($j+1)."-".$tahun.">".$array_bulan[$j+1]." - ".$tahun."</option>";
														}
														$tahun++;
													}
												?>
											</select>
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
										Total Pengeluaran:
									</div>
									<div class="col-md-3">
										<input type="text" name="txtTotal" style="text-align:right;" id="txtTotal" class="form-control col-md-3"  <?php echo 'value="'.number_format(($total),2,",",".").'"'; ?> readonly />
									</div>
									<button type="button" class="btn btn-default" onclick="{window.location.href='./Laporan/Kas/print.php';}"><i class="fa fa-file-excel-o"></i> Print</button>	
									
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
				$("#ddlMonthYear").val($("#hdnMonthYear").val());
			});
			function loadReport() {
				var MonthYear = $("#ddlMonthYear").val();
				$("#loading").show();
				$("#page-inner").html("");
				$.ajax({
					url: 'Laporan/Kas/',
					type: "POST",
					data: { ddlMonthYear : MonthYear, hdnPostBack : 1 },
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
				$("#pdfDownload").attr("src", "Laporan/Kas/Print.php?ddlMonthYear=" + MonthYear + "&hdnPostBack=1");
				
			}
			
		
		</script>
	</body>
</html>
