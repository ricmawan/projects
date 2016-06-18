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
						 <h5>Laba Rugi</h5>
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div style="float: left; margin-left: 20px; margin-top:2px;">
									<input type="radio" name="rdInterval" id="rdInterval" value="Daily" checked>
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtStartDate" name="txtStartDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Dari Tanggal" />
									</div>
								</div>
								<div style="float: left;">
								-
								</div>
								<div class="col-md-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input id="txtEndDate" name="txtEndDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Sampai Tanggal" />
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div style="float: left; margin-left: 20px; margin-top:2px;">
									<input type="radio" name="rdInterval" id="rdInterval" value="Monthly">
								</div>
								<div class="col-md-3">
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
								<div style="float:left;">
									<select id="ddlYear" name="ddlYear" class="form-control-custom" >
										<?php
											$date = date("Y"); 
											$date = $date - 5;
											for($i=$date;$i<$date + 5;$i++) {
												echo "<option value=$i>$i</option>";
											}
											$date = date("Y");
											for($i=$date;$i<$date + 5;$i++) {
												echo "<option value=$i>$i</option>";
											}
										?>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<button class="btn btn-info" id="btnView" type="button" onclick="Preview();" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
									<button class="btn btn-success" id="btnExcel" type="button" onclick="ExportExcel();" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
								</div>
							</div>
							<br />
							<div class="dvTotal" style="display: none;">
								Laba/Rugi: <span class="grandtotal"></span>
							</div>
							<br />
							<div class="table-responsive" id="dvTable" style="display: none;">
								<table id="grid-data" class="table table-striped table-bordered table-hover" >
									<thead>				
										<tr>
											<th data-column-id="TransactionDate" >Tanggal</th>
											<th data-column-id="Remarks" >Keterangan</th>
											<th data-column-id="Total" data-align="right">Total</th>
											<th data-column-id="Notes" >Catatan</th>
										</tr>
									</thead>
								</table>
							</div>
							<br />
							<div class="dvTotal" style="display: none;">
								Laba/Rugi: <span class="grandtotal"></span>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>			
			function Preview() {				
				var txtStartDate = $("#txtStartDate").val();
				var txtEndDate = $("#txtEndDate").val();
				var rdInterval = $("#rdInterval").val();
				var ddlMonth = $("#ddlMonth").val();
				var ddlYear = $("#ddlYear").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(PassValidate == 1) {
					if(txtStartDate != "" && txtEndDate != "") {
						var FromDate = txtStartDate.split("-");
						FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
						var ToDate = txtEndDate.split("-");
						ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
						if(FromDate > ToDate) {
							$("#txtEndDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							PassValidate = 0;
							if(FirstFocus == 0) $("#txtEndDate").focus();
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
						url: "Report/ProfitAndLoss/DataSource.php?txtStartDate=" + txtStartDate + "&txtEndDate=" + txtEndDate + "&rdInterval=" + rdInterval + "&ddlMonth=" + ddlMonth + "&ddlYear=" + ddlYear,
						selection: true,
						multiSelect: true,
						rowSelect: true,
						keepSelection: true
					});
					$("#dvTable").show();
					$(".dvTotal").show();
					$("#loading").hide();
				}
			}
			function ExportExcel() {
				var txtStartDate = $("#txtStartDate").val();
				var txtEndDate = $("#txtEndDate").val();
				var rdInterval = $("#rdInterval").val();
				var ddlMonth = $("#ddlMonth").val();
				var ddlYear = $("#ddlYear").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(PassValidate == 1) {
					if(txtStartDate != "" && txtEndDate != "") {
						var FromDate = txtStartDate.split("-");
						FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
						var ToDate = txtEndDate.split("-");
						ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
						if(FromDate > ToDate) {
							$("#txtEndDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							PassValidate = 0;
							if(FirstFocus == 0) $("#txtEndDate").focus();
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
					$("#excelDownload").attr("src", "Report/ProfitAndLoss/ExportExcel.php?&txtStartDate=" txtStartDate + "&txtEndDate=" + txtEndDate + "&rdInterval=" + rdInterval + "&ddlMonth=" + ddlMonth + "&ddlYear=" + ddlYear);
					$("#loading").hide();
				}
			}
		</script>
	</body>
</html>
