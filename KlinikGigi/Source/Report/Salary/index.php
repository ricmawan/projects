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
						 <h5>Pendapatan Dokter</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-2 labelColumn">
								<input type="radio" style="vertical-align: sub;" name="chkInterval" value="Daily" checked /> &nbsp; Tanggal :
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtFromDate" name="txtFromDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Dari Tanggal" />
								</div>
							</div>
							<div style="float:left;" class="labelColumn">
								-
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtToDate" name="txtToDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Sampai Tanggal" />
								</div>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn">
								<input type="radio" style="vertical-align: sub;" name="chkInterval" value="Monthly" /> &nbsp; Bulan :
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
										<th data-column-id="TransactionDate" >Tanggal</th>
										<th data-column-id="DoctorName" >Dokter</th>
										<th data-column-id="IncomeTotal" data-align="right">Total Pemasukan</th>
										<th data-column-id="Commision" data-align="right">Komisi</th>
										<th data-column-id="ToolsFee" data-align="right">Biaya Alat</th>
										<th data-column-id="Earning" data-align="right">Pendapatan</th>
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
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var ddlMonth = $("#ddlMonth").val();
				var ddlYear = $("#ddlYear").val();
				var chkInterval = $("input:radio[name=chkInterval]:checked").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(chkInterval == "Daily") {
					if(txtFromDate != "" && txtToDate != "") {
						var FromDate = txtFromDate.split("-");
						FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
						var ToDate = txtToDate.split("-");
						ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
						if(FromDate > ToDate) {
							$("#txtToDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							PassValidate = 0;
							if(FirstFocus == 0) $("#txtToDate").focus();
							FirstFocus = 1;
						}
					}
				}
				
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
						responseHandler: function(response) {
							$(".grandtotal").html(response.GrandTotal);
							return response;
						},
						url: "Report/Salary/DataSource.php?ddlMonth=" + ddlMonth + "&ddlYear=" + ddlYear + "&txtFromDate=" + txtFromDate + "&txtToDate=" + txtToDate + "&chkInterval=" + chkInterval,
						selection: true,
						multiSelect: true,
						rowSelect: true,
						keepSelection: true
					}).on("loaded.rs.jquery.bootgrid", function (e)
					{
						/* your code goes here */
						if($("input:radio[name=chkInterval]:checked").val() == "Daily") {
							$("th[data-column-id='TransactionDate']").css({
								"display" : "table-cell"
							});
							
							$("#grid-data tbody tr td:nth-child(2)").css({
								"display" : "table-cell"
							});
						}
						else {
							$("th[data-column-id='TransactionDate']").css({
								"display" : "none"
							});
							
							setTimeout(function () {
								$("#grid-data tbody tr td:nth-child(2)").css({
									"display" : "none"
								});	
							}, 100);
							
						}
					});
					
					$("#dvTable").show();
					$("#loading").hide();
				}
			}
			function ExportExcel() {
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var ddlMonth = $("#ddlMonth").val();
				var ddlYear = $("#ddlYear").val();
				var chkInterval = $("input:radio[name=chkInterval]:checked").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(chkInterval == "Daily") {
					if(txtFromDate != "" && txtToDate != "") {
						var FromDate = txtFromDate.split("-");
						FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
						var ToDate = txtToDate.split("-");
						ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
						if(FromDate > ToDate) {
							$("#txtToDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							PassValidate = 0;
							if(FirstFocus == 0) $("#txtToDate").focus();
							FirstFocus = 1;
						}
					}
				}
				
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					$("#loading").show();
					if(chkInterval == "Monthly") $("#excelDownload").attr("src", "Report/Salary/ExportExcel.php?ddlMonth=" + ddlMonth + "&ddlYear=" + ddlYear);
					else $("#excelDownload").attr("src", "Report/Salary/ExportExcelDaily.php?txtFromDate=" + txtFromDate + "&txtToDate=" + txtToDate);
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