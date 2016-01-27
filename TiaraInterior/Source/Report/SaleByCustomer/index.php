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
						 <h2>Laporan Proyek</h2>
					</div>
					<div class="panel-body">
						<div class="row col-md-12">
							<div class="col-md-5">
								<div class="ui-widget" style="width: 100%;">
									<select name="ddlProject" id="ddlProject" class="form-control" placeholder="Pilih Proyek" >
										<option value="" selected> </option>
										<?php
											$sql = "SELECT ProjectID, ProjectName FROM master_project";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												if($ProjectID == $row['ProjectID']) echo "<option selected value='".$row['ProjectID']."' >".$row['ProjectName']."</option>";
												else echo "<option value='".$row['ProjectID']."' >".$row['ProjectName']."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-5">
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
										<th data-column-id="TransactionDate">Tanggal</th>
										<th data-column-id="Name">Nama</th>
										<th data-column-id="ItemName">Bahan</th>
										<th data-column-id="Quantity" data-header-css-class="QTY" data-align="right">Qty</th>
										<th data-column-id="UnitName" data-header-css-class="QTY" >Satuan</th>
										<th data-column-id="Price" data-align="right">Harga</th>
										<th data-column-id="Debit" data-align="right">Debit</th>
										<th data-column-id="Credit" data-align="right">Kredit</th>
										<th data-column-id="Balance" data-sortable="false" data-align="right">Saldo</th>
										<th data-column-id="Remarks" >Keterangan</th>
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
				$("#ddlProject").combobox();
			});
			
			function ExportPDF() {
				if($("#ddlProject").val() == "") {
					$("#ddlProject").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					$("#ddlProject").next().find("input").focus();
				}
				else {
					$("#loading").show();
					form = $("#PostForm");
					form.attr("action", "./Report/ProjectReport/ExportPDF.php");
					form.submit();
					$("#loading").hide();
				}
			}
			function Preview() {
				var ProjectID = $("#ddlProject").val();
				if(ProjectID == "") {
					$("#ddlProject").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					$("#ddlProject").next().find("input").focus();
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
						url: "Report/ProjectReport/DataSource.php?ProjectID=" + ProjectID,
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
				var ProjectID = $("#ddlProject").val();
				if(ProjectID == "") {
					$("#ddlProject").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					$("#ddlProject").next().find("input").focus();
				}
				else {
					$("#loading").show();
					$("#excelDownload").attr("src", "Report/ProjectReport/ExportExcel.php?ProjectID=" + ProjectID);
					$("#loading").hide();
				}
			}
		</script>
	</body>
</html>
