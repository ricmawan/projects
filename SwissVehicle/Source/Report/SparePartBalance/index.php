<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			.actionBar {
				display: none;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Saldo Spare Part</h5>
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-1 labelColumn">
									Bulan :
								</div>
								<div class="col-md-2">
									<select id="ddlMonth" name="ddlMonth" class="form-control-custom" >
										<option value=1>Januari</option>
										<option value=2>Februari</option>
										<option value=3>Maret</option>
										<option value=4>April</option>
										<option value=5>Mei</option>
										<option value=6>Juni</option>
										<option value=7>Juli</option>
										<option value=8>Agustus</option>
										<option value=9>September</option>
										<option value=10>Oktober</option>
										<option value=11>November</option>
										<option value=12>Desember</option>
									</select>
								</div>
								<div style="float:left;" class="labelColumn">
									-
								</div>
								<div class="col-md-2">
									<select id="ddlYear" name="ddlYear" class="form-control-custom" style="width:auto;">
										<?php
											$EndYear = (int)date("Y");
											for($StartYear = 2016;$StartYear <= $EndYear;$StartYear++) {
												echo "<option value=$StartYear>$StartYear</option>";
											}
										?>
									</select>
								</div>
							</div>
						</form>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-success" id="btnExport" onclick="PrintReport(1);" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>			
			function PrintReport(PrintType) {
				var ddlMonth = $("#ddlMonth").val();
				var ddlYear = $("#ddlYear").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					$("#loading").show();
					$("#excelDownload").attr("src", "Report/SparePartBalance/ExportExcel.php?ddlMonth=" + ddlMonth + "&ddlYear=" + ddlYear);
					$("#loading").hide();
				}
			}
			$(document).ready(function () {
				var d = new Date();
				var currentMonth = d.getMonth();
				var currentYear = d.getFullYear();
				$("#ddlMonth").val(currentMonth + 1);
				$("#ddlYear").val(currentYear);
			});
		</script>
	</body>
</html>
