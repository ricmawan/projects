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
			.Price {
				width: 150px;
			}
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
						 <h5>Pembelian</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								Dari Tanggal: <br />
								<div class="ui-widget" style="width: 100%;">
									<input id="txtFromDate" style="height:34px;" name="txtFromDate" type="text" class="form-control DatePickerMonthYearGlobal" placeholder="Dari Tanggal" />
								</div>
							</div>
							<div class="col-md-4">
								Sampai Tanggal: <br />
								<div class="ui-widget" style="width: 100%;">
									<input id="txtToDate" style="height:34px;" name="txtToDate" type="text" class="form-control DatePickerMonthYearGlobal" placeholder="Sampai Tanggal" />
								</div>
							</div>
							<div class="col-md-4">
								Sales:<br />
								<div class="ui-widget" style="width: 100%;">
									<select name="ddlSupplier" id="ddlSupplier" class="form-control" placeholder="Pilih Sales" >
										<option value="" selected> </option>
										<?php
											$sql = "SELECT CustomerID, CustomerName FROM master_customer";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												if($SupplierID == $row['CustomerID']) echo "<option selected value='".$row['CustomerID']."' >".$row['CustomerName']."</option>";
												else echo "<option value='".$row['CustomerID']."' >".$row['CustomerName']."</option>";
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-default" id="btnExcel" onclick="Preview();" ><i class="fa fa-file-excel-o "></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-default" id="btnExcel" onclick="ExportExcel();" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric" data-header-css-class="QTY" >No</th>
										<th data-column-id="TransactionDate" data-header-css-class="QTY" >Tanggal</th>
										<th data-column-id="Name">Nama</th>
										<th data-column-id="Incoming" data-header-css-class="QTY" data-align="right">Masuk</th>
										<th data-column-id="Outgoing" data-header-css-class="QTY" data-align="right">Keluar</th>
										<th data-column-id="Price" data-align="right" data-header-css-class="Price">Harga</th>
										<th data-column-id="Stock" data-align="right" data-header-css-class="QTY" >Stok</th>
										<th data-column-id="Remarks" >Keterangan</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>			
			function BindItem() {
				$("#ddlItem option").each(function() {
					$(this).remove();
				});
				$("#ddlItem").append('<option value="" categoryid="" selected> </option>');
				$("#ddlItem").val("");
				$("#ddlItem").next().find("input").val("");
				$("#ddlHiddenItem option").each(function() {
					if($(this).attr("categoryid") == $("#ddlCategory").val() || $(this).attr("categoryid") == "") {
						$("#ddlItem").append($(this).clone());
					}
				});
			}
			
			function Preview() {
				var ItemID = $("#ddlItem").val();
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				$(".form-control").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
				if(PassValidate == 1) {
					if(txtFromDate != "" && txtToDate != "") {
						var FromDate = txtFromDate.split("-");
						FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
						var ToDate = txtToDate.split("-");
						ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
						if(FromDate > ToDate) {
							$("#txtToDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							PassValidate = 0;
							if(FirstFocus == 0) $("#ddlItem").next().find("input").focus();
							FirstFocus = 1;
						}
					}
				}
				if(ItemID == "") {
					$("#ddlItem").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlItem").next().find("input").focus();
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
							infos: "Menampilkan {{ctx.start}} sampai {{ctx.end}} dari {{ctx.total}} data",
							loading: "Loading...",
							noResults: "Tidak Ada Data Yang Ditemukan!",
							refresh: "Refresh",
							search: "Cari"
						},
						url: "Report/StockMutation/DataSource.php?ItemID=" + ItemID + "&txtFromDate=" + txtFromDate + "&txtToDate=" + txtToDate,
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
				var ItemID = $("#ddlItem").val();
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				$(".form-control").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
				if(PassValidate == 1) {					
					if(txtFromDate != "" && txtToDate != "") {
						var FromDate = txtFromDate.split("-");
						FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
						var ToDate = txtToDate.split("-");
						ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
						if(FromDate > ToDate) {
							$("#txtToDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							PassValidate = 0;
							if(FirstFocus == 0) $("#ddlItem").next().find("input").focus();
							FirstFocus = 1;
						}
					}
				}
				if(ItemID == "") {
					$("#ddlItem").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlItem").next().find("input").focus();
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
					$("#excelDownload").attr("src", "Report/StockMutation/ExportExcel.php?ItemID=" + ItemID + "&txtFromDate=" + txtFromDate + "&txtToDate=" + txtToDate);
					$("#loading").hide();
				}
			}
			$(document).ready(function () {
				$("#ddlCategory").combobox({
					select: function( event, ui ) {
						BindItem();						
					}
				});
				$("#ddlItem").combobox();
			});
		</script>
	</body>
</html>
