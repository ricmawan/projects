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
		$TableColumn = "<td>No</td><td>Konsultan</td><td>Cakar</td>";
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
		$sql = "SELECT KonsultanID, Nama FROM master_konsultan";
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 0;
		while ($row=mysql_fetch_row($result)) {
			$RowNumber ++;
			$TableBody .= "<tr><td>".$RowNumber."</td><td>".$row[1]."</td>";

			$sql2 = "SELECT
						COUNT(RCS.TransaksiID)
					FROM
						master_konsultan MK
						LEFT JOIN transaksi_rincicustomerservice RCS
							ON RCS.KonsultanID = MK.KonsultanID
						LEFT JOIN transaksi_customerservice CS
							ON RCS.TransaksiID = CS.TransaksiID
					WHERE
						MK.KonsultanID = ".$row[0]."
						AND MONTH(CS.Tanggal) = $Month
						AND YEAR(CS.Tanggal) = $Year
					GROUP BY
						MK.KonsultanID
					ORDER BY
						MK.KonsultanID";
		
			if (! $result2=mysql_query($sql2, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$cekrow = mysql_num_rows($result2);
			if($cekrow == 0) $TableBody .= "<td>0</td>";
			else {
				while ($row2=mysql_fetch_row($result2)) {
					$TableBody .= "<td>".$row2[0]."</td>";
				}
			}

			$sql3 = "SELECT
						L.LayananID						
					FROM
						master_layanan L
					ORDER BY
						L.LayananID";
					
			if (! $result3=mysql_query($sql3, $dbh)) {
				echo mysql_error();
				return 0;
			}
			while ($row3=mysql_fetch_row($result3)) {
				//$TableColumn .= "<td>".$row2[0]."</td>";
				$sql4 = "SELECT
						MK.KonsultanID,
						MK.Nama,
						CS.TransaksiID,
						L.Jenis,
						L.LayananID,
						(SELECT 
							COUNT(1)
						 FROM
							transaksi_rincicustomerservice RCS
							JOIN transaksi_customerservice CS
								ON CS.TransaksiID = RCS.TransaksiID
						 WHERE
							RCS.LayananID = L.LayananID
							AND RCS.KonsultanID = MK.KonsultanID
							AND MONTH(CS.Tanggal) = $Month
							AND YEAR(CS.Tanggal) = $Year
						) as Count,
						SUM(RCS2.Harga)
					FROM
						master_konsultan MK
						LEFT JOIN transaksi_rincicustomerservice RCS2
							ON RCS2.KonsultanID = MK.KonsultanID
						LEFT JOIN transaksi_customerservice CS
							ON RCS2.TransaksiID = CS.TransaksiID
						LEFT JOIN master_layanan L
							ON RCS2.LayananID = L.LayananID
					WHERE
						MK.KonsultanID = ".$row[0]."
						AND L.LayananID = ".$row3[0]."
						AND MONTH(CS.Tanggal) = $Month
						AND YEAR(CS.Tanggal) = $Year
					GROUP BY
						MK.KonsultanID,
						L.LayananID
					ORDER BY
						MK.KonsultanID";
			
				if (! $result4=mysql_query($sql4, $dbh)) {
					echo mysql_error();
					return 0;
				}
				$cekrow = mysql_num_rows($result4);
				if($cekrow == 0) $TableBody .= "<td>0</td><td align='right'>0,00</td>";
				else {
					$Count = 0;
					$Harga = 0;
					while ($row4=mysql_fetch_row($result4)) {
						$Count += $row4[5];
						$Harga = $row4[6];
					}
					$TableBody .= "<td>".$Count."</td><td align='right'>".number_format($Harga,2,",",".")."</td>";
				}
			}
			$sql6 = "SELECT
						IFNULL(COUNT(PiketID), 0)
					FROM
						master_konsultan MK
						LEFT JOIN piket_konsultan PK
							ON MK.KonsultanID = PK.KonsultanID
					 WHERE
						MK.KonsultanID = ".$row[0]."
						AND MONTH(PK.TanggalPiket) = $Month
						AND YEAR(PK.TanggalPiket) = $Year
					 GROUP BY
						MK.KonsultanID";
			if (! $result6=mysql_query($sql6, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$cekrow = mysql_num_rows($result6);
			if($cekrow == 0) $TableBody .= "<td>0</td><td align='right'>0,00</td>";
			else {
				while ($row6=mysql_fetch_row($result6)) {
					$TableBody .= "<td>".$row6[0]."</td><td align='right'>".number_format(($row6[0] * $CONSULTANT_PICKET),2,",",".")."</td>";
				}
			}
			$TableBody .= "</tr>";
		}
		$sql5 = "SELECT
					L.Jenis,
					L.LayananID,
					L.Harga
				FROM
					master_layanan L
				ORDER BY
					L.LayananID";
				
		if (! $result5=mysql_query($sql5, $dbh)) {
			echo mysql_error();
			return 0;
		}
		while ($row5=mysql_fetch_row($result5)) {
			$TableColumn .= "<td>".$row5[0]."</td><td>Total</td>";
		}
		$TableColumn .= "<td>Piket</td><td>JML @".$CONSULTANT_PICKET."</td>";
		echo "<input type='hidden' id='hdnMonthYear' value='".$Month."-".$Year."' />";
	}
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Laporan Honorium Konsultan</h2>   
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
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-3">
										<select id="ddlMonthYear" name="ddlMonthYear" class="form-control">
											<?php
												$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember');
												$bulan = $array_bulan[date('n')];
												$tahun = date('Y') - 2;
												for($i=2; $i>=0;$i--) {
													for($j=0; $j< 12; $j++) {
														echo "<option value=".($j+1)."-".$tahun.">".$array_bulan[$j+1]." - ".$tahun."</option>";
													}
													$tahun++;
												}
											?>
										</select>
									</div>
									<div class="col-md-3">
										<button type="button" class="btn btn-default" onclick="LoadReport()"><i class="fa fa-file-excel-o"></i> Excel File</button>
										<input type="hidden" id="hdnPostBack" name="hdnPostBack" value=1 />
									</div>
								</div>
							</div>
								<!--<div class="table-responsive">
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
									<button class="btn btn-primary menu" link="./CustomerService/CustomerService/Detail.php?id=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;<button class="btn btn-danger" onclick="DeleteData('./CustomerService/CustomerService/Delete.php');" ><i class="fa fa-close"></i> Hapus</button>
								</div>-->
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
			function LoadReport() {
				var MonthYear = $("#ddlMonthYear").val();
				$("#loading").show();
				$("#excelDownload").attr("src", "Laporan/HonoriumKonsultan/Export.php?ddlMonthYear=" + MonthYear + "&hdnPostBack=1");
				$("#loading").hide();
			}
		</script>
	</body>
</html>
