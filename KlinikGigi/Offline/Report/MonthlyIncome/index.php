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
						 <h5>Pendapatan Bulanan</h5>
					</div>
					<div class="panel-body">
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
						<br />
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-info" id="btnView" onclick="Preview();" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-success" id="btnExcel" onclick="ExportExcel();" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
						<br />
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric" >No</th>
										<th data-column-id="MonthName">Bulan</th>
										<th data-column-id="Cash" data-align="right">Cash</th>
										<th data-column-id="Debit" data-align="right">Debit</th>
										<th data-column-id="IncomeTotal" data-align="right">Total Pendapatan</th>
									</tr>
								</thead>
							</table>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		<script>			
			function Preview() {
				var DoctorID = $("#ddlDoctor").val();
				var ddlMonth = $("#ddlMonth").val();
				var ddlYear = $("#ddlYear").val();
				var PassValidate = 1;
				var FirstFocus = 0;					
				/*if(DoctorID == "0") {
					$("#ddlDoctor").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlDoctor").next().find("input").focus();
					FirstFocus = 1;
				}*/
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					$("#loading").show();
					$("#grid-data").bootgrid('destroy');
					$("#grid-data").bootgrid({
						ajax: true,
						rowCount: -1,
						sorting: false,
						columnSelection: false,
						post: function ()
						{
							/* To accumulate custom parameter with the request object */
							return {
								id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
							};
						},
						 templates: {
							search: ""
						},
						css: {
							iconRefresh: "none"
						},
						labels: {
							all: "Semua Data",
							//infos: "Menampilkan {{ctx.start}} sampai {{ctx.end}} dari {{ctx.total}} data",
							infos: "Menampilkan {{ctx.total}} data",
							loading: "Loading...",
							noResults: "Tidak Ada Data Yang Ditemukan!",
							refresh: "Refresh",
							search: "Cari"
						},
						url: "Report/MonthlyIncome/DataSource.php?DoctorID=" + DoctorID + "&ddlMonth=" + ddlMonth + "&ddlYear=" + ddlYear,
						selection: true,
						multiSelect: true,
						rowSelect: true,
						keepSelection: true
					});
					$("#dvTable").show();
					$("#loading").hide();
				}
			}
			function ExportExcel() {
				var DoctorID = $("#ddlDoctor").val();
				var ddlMonth = $("#ddlMonth").val();
				var ddlYear = $("#ddlYear").val();
				var PassValidate = 1;
				var FirstFocus = 0;	
				/*if(DoctorID == "0") {
					$("#ddlDoctor").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlDoctor").next().find("input").focus();
					FirstFocus = 1;
				}*/
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					$("#loading").show();
					$("#excelDownload").attr("src", "Report/MonthlyIncome/ExportExcel.php?DoctorID=" + DoctorID + "&ddlMonth=" + ddlMonth + "&ddlYear=" + ddlYear);
					$("#loading").hide();
				}
			}
			$(document).ready(function () {
				$("#ddlDoctor").combobox();
				$("#ddlDoctor").next().find("input").click(function() {
					$(this).val("");
				});
				var d = new Date();
				var currentMonth = d.getMonth();
				var currentYear = d.getFullYear();
				$("#ddlMonth").val(currentMonth + 1);
				$("#ddlYear").val(currentYear);
				
			});
		</script>
	</body>
</html>
