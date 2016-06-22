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
						 <h5>Penjualan Per Pelanggan</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Pelanggan:
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<select name="ddlCustomer" id="ddlCustomer" class="form-control-custom" placeholder="Pilih Pelanggan" >
										<option value="" > </option>
										<?php
											$sql = "SELECT CustomerID, CustomerName, Address1 FROM master_customer ORDER BY CustomerName";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												echo "<option value='".$row['CustomerID']."' >".$row['CustomerName']." - ".$row['Address1']."</option>";
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-1 labelColumn">
								Tanggal :
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
							<div class="col-md-12">
								<button class="btn btn-info" id="btnView" onclick="Preview();" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-success" id="btnExcel" onclick="ExportExcel();" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
						<br />
						Grand Total: <span class="grandtotal"></span>
						<br />
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric" >No</th>
										<th data-column-id="OutgoingNumber" data-sortable="false" data-type="numeric" >No Nota</th>
										<th data-column-id="TransactionDate" >Tanggal</th>
										<th data-column-id="DeliveryCost" data-align="right">Ongkos Kirim</th>
										<th data-column-id="SubTotal" data-align="right">Sub Total</th>
										<th data-column-id="Total" data-align="right">Total</th>
										<th data-column-id="Remarks" >Keterangan</th>
									</tr>
								</thead>
							</table>
						</div>
						<br />
						Grand Total: <span class="grandtotal"></span>
					</div>
				</div>
			</div>
		</div>
		<script>			
			function Preview() {
				var CustomerID = $("#ddlCustomer").val();
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(PassValidate == 1) {
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
				if(CustomerID == 0) {
					$("#ddlCustomer").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlCustomer").next().find("input").focus();
					FirstFocus = 1;
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
						url: "Report/SaleByCustomer/DataSource.php?CustomerID=" + CustomerID + "&txtFromDate=" + txtFromDate + "&txtToDate=" + txtToDate,
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
				var CustomerID = $("#ddlCustomer").val();
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(PassValidate == 1) {
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
				if(CustomerID == 0) {
					$("#ddlCustomer").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlCustomer").next().find("input").focus();
					FirstFocus = 1;
				}
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					$("#loading").show();
					$("#excelDownload").attr("src", "Report/SaleByCustomer/ExportExcel.php?CustomerID=" + CustomerID + "&txtFromDate=" + txtFromDate + "&txtToDate=" + txtToDate);
					$("#loading").hide();
				}
			}
			$(document).ready(function () {
				$("#ddlCustomer").combobox();
			});
		</script>
	</body>
</html>
