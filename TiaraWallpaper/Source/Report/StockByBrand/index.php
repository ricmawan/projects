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
						 <h5>Stok Per Merek</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Merek:
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<select name="ddlBrand" id="ddlBrand" class="form-control-custom" placeholder="Pilih Merek" >
										<option value="" > </option>
										<option value=0 selected>-Pilih Semua Merek-</option>
										<?php
											$sql = "SELECT BrandID, BrandName FROM master_brand ORDER BY BrandName";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												echo "<option value='".$row['BrandID']."' >".$row['BrandName']."</option>";
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
						</div>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-info" id="btnView" onclick="Preview();" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-success" id="btnExcel" onclick="ExportExcel();" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
						<br />
						<!--Grand Total: <span class="grandtotal"></span>
						<br />-->
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<!--<th data-column-id="RowNumber" data-sortable="false" data-type="numeric" >No</th>-->
										<th data-column-id="BrandName">Merek</th>
										<th data-column-id="Stock" data-align="right">Stok</td>
										<!--<th data-column-id="Price" data-align="right">Harga Jual/Beli</th>
										<th data-column-id="Discount" data-align="right">Diskon</th>
										<th data-column-id="Total" data-align="right">Total</th>-->
									</tr>
								</thead>
							</table>
						</div>
						<!--<br />
						Grand Total: <span class="grandtotal"></span>-->
					</div>
				</div>
			</div>
		</div>
		<script>			
			function Preview() {
				var BrandID = $("#ddlBrand").val();
				var txtFromDate = $("#txtFromDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(BrandID == "") {
					$("#ddlBrand").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlBrand").next().find("input").focus();
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
						url: "Report/StockByBrand/DataSource.php?BrandID=" + BrandID + "&txtFromDate=" + txtFromDate,
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
				var BrandID = $("#ddlBrand").val();
				var txtFromDate = $("#txtFromDate").val();
				var TypeID = $("#ddlType").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				
				if(BrandID == "") {
					$("#ddlBrand").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlBrand").next().find("input").focus();
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
					$("#excelDownload").attr("src", "Report/StockByBrand/ExportExcel.php?BrandID=" + BrandID + "&txtFromDate=" + txtFromDate );
					$("#loading").hide();
				}
			}
						
			$(document).ready(function () {
				$("#ddlBrand").combobox();
				$("#ddlBrand").next().find("input").click(function() {
					$(this).val("");
				});
				
			});
		</script>
	</body>
</html>
