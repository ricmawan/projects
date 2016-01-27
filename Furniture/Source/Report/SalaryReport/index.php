<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			.custom-combobox {
				position: relative;
				display: inline-block;
				width: 100%;
			}
			.custom-combobox-input {
				margin: 0;
				padding: 5px 10px;
				display: block;
				width: 100%;
				height: 34px;
				padding: 6px 12px;
				font-size: 14px;
				line-height: 1.42857143;
				color: #555;
				background-color: #fff;
				background-image: none;
				border: 1px solid #ccc;
				border-radius: 4px;
				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
				-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
				transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			}
			.ui-autocomplete {
				font-family: Open Sans, sans-serif; 
				font-size: 14px;
			}
			.caret {
				display: inline-block;
				width: 0;
				height: 0;
				margin-left: 2px;
				vertical-align: middle;
				border-top: 4px solid;
				border-right: 4px solid transparent;
				border-left: 4px solid transparent;
				right: 10px;
				top: 50%;
				position: absolute;
			}
			.QTY {
				width: 40px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h2>Laporan Gaji</h2>
					</div>
					<div class="panel-body">
						<div class="row col-md-12">
							<div class="col-md-4">
								<div class="ui-widget" style="width: 100%;">
									<select name="ddlPeriod" id="ddlPeriod" class="form-control" placeholder="Pilih Periode" >
										<option value="" selected> </option>
										<?php
											$sql = "SELECT PeriodID, CONCAT(DATE_FORMAT(StartDate, '%d %M %Y'), ' - ', DATE_FORMAT(EndDate, '%d %M %Y')) AS PeriodRange FROM master_period";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												echo "<option value='".$row['PeriodID']."' >".$row['PeriodRange']."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="ui-widget" style="width: 100%;">
									<select name="ddlEmployee" id="ddlEmployee" class="form-control" placeholder="Pilih Karyawan" >
										<option value="" selected> </option>
										<?php
											$sql = "SELECT EmployeeID, EmployeeName FROM master_employee";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												echo "<option value='".$row['EmployeeID']."' >".$row['EmployeeName']."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<button class="btn btn-default" id="btnExcel" onclick="Preview();" ><i class="fa fa-file-excel-o "></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-default" id="btnExcel" onclick="ExportExcel();" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
						<br />
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="SalaryDate">Tanggal</th>
										<th data-column-id="ProjectName">Nama Proyek</th>
										<th data-column-id="DailySalary">Gaji/hari</th>
										<th data-column-id="Days" data-header-css-class="QTY" data-align="right">Hari</th>
										<th data-column-id="Remarks" >Keterangan</th>
										<th data-column-id="Total" data-align="right">Jumlah</th>
									</tr>
								</thead>
							</table>
						</div>
						<!--<div class="row">
							<div class="col-md-12">
								<button class="btn btn-default" id="btnPDF" onclick="ExportPDF();"><i class="fa fa-file-pdf-o "></i> Eksport PDF</button>&nbsp;&nbsp;
							</div>
						</div>-->
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				$("#ddlPeriod").combobox();
				$("#ddlEmployee").combobox();
			});
			
			function Preview() {
				var PeriodID = $("#ddlPeriod").val();
				var EmployeeID = $("#ddlEmployee").val();
				if(PeriodID == "") {
					$("#ddlPeriod").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					$("#ddlPeriod").next().find("input").focus();
				}
				else if(EmployeeID == "") {
					$("#ddlEmployee").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					$("#ddlEmployee").next().find("input").focus();
				}
				else {
					$("#loading").show();
					$("#grid-data").bootgrid('destroy');
					$("#grid-data").bootgrid({
						ajax: true,
						rowCount: -1,
						post: function ()
						{
							/* To accumulate custom parameter with the request object */
							return {
								id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
							};
						},
						labels: {
							all: "Semua Data",
							infos: "Menampilkan {{ctx.start}} sampai {{ctx.end}} dari {{ctx.total}} data",
							loading: "Loading...",
							noResults: "Tidak Ada Data Yang Ditemukan!",
							refresh: "Refresh",
							search: "Cari"
						},
						url: "Report/SalaryReport/DataSource.php?PeriodID=" + PeriodID + "&EmployeeID=" + EmployeeID,
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
				var PeriodID = $("#ddlPeriod").val();
				var EmployeeID = $("#ddlEmployee").val();
				if(PeriodID == "") {
					$("#ddlPeriod").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					$("#ddlPeriod").next().find("input").focus();
				}
				else if(EmployeeID == "") {
					$("#ddlEmployee").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					$("#ddlEmployee").next().find("input").focus();
				}
				else {
					$("#loading").show();
					$("#excelDownload").attr("src", "Report/SalaryReport/ExportExcel.php?PeriodID=" + ProjectID + "&EmployeeID=" + EmployeeID);
					$("#loading").hide();
				}
			}
		</script>
	</body>
</html>
