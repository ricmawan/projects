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
						 <h2>Arus Kas</h2>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-5">
								Bulan: <br />
								<select id="ddlMonth" name="ddlMonth" class="form-control" required >
									<option value=0>-Pilih Bulan-</option>
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
							<div class="col-md-5">
								Tahun: <br />
								<select id="ddlYear" name="ddlYear" class="form-control" required >
									<option value=0>-Pilih Tahun-</option>
									<?php
										$EndYear = (int)date("Y");
										for($StartYear = 2015;$StartYear <= $EndYear;$StartYear++) {
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
								<button class="btn btn-default" id="btnExcel" onclick="Preview();" ><i class="fa fa-file-excel-o "></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-default" id="btnExcel" onclick="ExportExcel();" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="TransactionDate">Tanggal</th>
										<th data-column-id="ItemName">Bahan</th>
										<th data-column-id="Quantity" data-header-css-class="QTY" data-align="right">Qty</th>
										<th data-column-id="Price" data-align="right">Harga</th>
										<th data-column-id="Debit" data-align="right">Debit</th>
										<th data-column-id="Credit" data-align="right">Kredit</th>
										<th data-column-id="Balance" data-sortable="false" data-align="right">Saldo</th>
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
			function Preview() {
				var ddlMonth = $("#ddlMonth").val();
				var ddlYear = $("#ddlYear").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				$(".form-control").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "0") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
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
						url: "Report/CashFlow/DataSource.php?ddlMonth=" + ddlMonth + "&ddlYear=" + ddlYear,
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
				var ddlMonth = $("#ddlMonth").val();
				var ddlYear = $("#ddlYear").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				$(".form-control").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "0") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});				
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					$("#loading").show();
					$("#excelDownload").attr("src", "Report/CashFlow/ExportExcel.php?ddlMonth=" + ddlMonth + "&ddlYear=" + ddlYear);
					$("#loading").hide();
				}
			}
		</script>
	</body>
</html>
