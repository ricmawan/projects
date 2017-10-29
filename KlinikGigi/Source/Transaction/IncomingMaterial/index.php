<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Pembelian Material</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Tanggal :
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtFromDate" onchange="LoadData();" name="txtFromDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Dari Tanggal" />
								</div>
							</div>
							<div style="float:left;" class="labelColumn">
								-
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtToDate" onchange="LoadData();" name="txtToDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Sampai Tanggal" />
								</div>
							</div>
						</div>
						<div class="table-responsive">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="IncomingDetailsID" data-type="numeric" data-visible="false" data-identifier="true">ID Transaksi</th>
										<th data-column-id="TransactionDate" data-type="numeric">Tanggal</th>
										<th data-column-id="MaterialName" data-type="numeric">Material</th>
										<th data-column-id="SupplierName" data-type="numeric">Supplier</th>
										<th data-column-id="Quantity" data-type="numeric">Jumlah</th>
										<th data-column-id="Remarks">Keterangan</th>
										<?php if($EditFlag == true) echo '<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>'; ?>
									</tr>
								</thead>
							</table>
						</div>
						<button class="btn btn-primary menu" link="./Transaction/IncomingMaterial/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
						<?php if($DeleteFlag == true) echo '<button class="btn btn-danger" onclick="DeleteData(\'./Transaction/IncomingMaterial/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>'; ?>
					</div>
				</div>
			</div>
			<div id="dialog-edit-transaction" title="Edit Pembelian Material" style="display: none;">
				<form class="col-md-12" id="EditForm" method="POST" action="" >
					<input type="hidden" id="hdnIncomingDetailsID" name="hdnIncomingDetailsID" value=0 autofocus="autofocus" />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Material:
						</div>
						<div class="col-md-6" >
							<span style="font-weight: bold; font-size: 18px; color: red;" id="MaterialName"></span>
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Supplier:
						</div>
						<div class="col-md-6">
							<input type="text" id="txtSupplierName" name="txtSupplierName" class="form-control-custom" />
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Jumlah:
						</div>
						<div class="col-md-6">
							<input type="text" autocomplete="off" id="txtQuantity" name="txtQuantity" onkeypress="return isNumberKey(event)" class="form-control-custom" style="text-align: right;" value=1 />
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Keterangan:
						</div>
						<div class="col-md-6">
							<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" ></textarea>
						</div>
					</div>
				</form>
			</div>
		</div>
		<script>
			function EditData(IncomingDetailsID, MaterialName, SupplierName, Quantity, Remarks) {
				$("#hdnIncomingDetailsID").val(IncomingDetailsID);
				$("#MaterialName").html(MaterialName);
				$("#txtSupplierName").val(SupplierName);
				$("#txtQuantity").val(Quantity);
				$("#txtRemarks").val(Remarks);
				$("#dialog-edit-transaction").dialog({
					autoOpen: false,
					show: {
						effect: "fade",
						duration: 500
					},
					hide: {
						effect: "fade",
						duration: 500
					},
					resizable: false,
					height: "auto",
					width: 600,
					modal: true,
					close: function() {
						$(this).dialog("destroy");
					},
					buttons: {
						"Simpan": function() {
							$(this).dialog("destroy");
							$.ajax({
								url: "./Transaction/IncomingMaterial/Update.php",
								type: "POST",
								data: $("#EditForm").serialize(),
								dataType: "json",
								success: function(data) {
									$("#loading").hide();
									if(data.FailedFlag == '0') {
										$.notify(data.Message, "success");
										$("#grid-data").bootgrid("reload");
									}
									else {
										$.notify(data.Message, "error");					
									}
								},
								error: function(data) {
									$("#loading").hide();
									$.notify("Terjadi kesalahan sistem!", "error");
								}
							});
						},
						"Batal": function() {
							$(this).dialog("destroy");
							return false;
						}
					}
				}).dialog("open");
			}
			
			function LoadData() {
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
				
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					$("#grid-data").bootgrid("destroy");
					var grid = $("#grid-data").bootgrid({
									ajax: true,
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
									url: "./Transaction/IncomingMaterial/DataSource.php?Filter=1&txtFromDate=" + txtFromDate + "&txtToDate=" + txtToDate,
									selection: false,
									multiSelect: true,
									rowSelect: true,
									keepSelection: true,
									formatters: {
										"commands": function(column, row)
										{
											return "<i style='cursor:pointer;' data-row-id=\"" + row.IncomingDetailsID + "\" data-material-name=\"" + row.MaterialName + "\" data-supplier-name=\"" + row.SupplierName + "\" data-quantity=\"" + row.Quantity + "\" data-remarks=\"" + row.Remarks + "\" class=\"fa fa-edit\" acronym title=\"Ubah Data\"></i>&nbsp;";
										}
									}
									}).on("loaded.rs.jquery.bootgrid", function()
									{
										/* Executes after data is loaded and rendered */
										grid.find(".fa-edit").on("click", function(e)
										{
											//Redirect($(this).data("link"));
											var IncomingDetailsID = $(this).data("row-id");
											var MaterialName = $(this).data("material-name");
											var SupplierName = $(this).data("supplier-name");
											var Quantity = $(this).data("quantity");
											var Remarks = $(this).data("remarks");
											EditData(IncomingDetailsID, MaterialName, SupplierName, Quantity, Remarks);
										});
									});
				}
			}
			
			$(document).ready(function() {
				$("#txtFromDate").datepicker({
					dateFormat: 'DD, dd-mm-yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu" ]
				});
				$("#txtFromDate").attr("readonly", "true");
				$("#txtFromDate").css({
					"background-color": "#FFF",
					"cursor": "text"
				});
				
				$("#txtToDate").datepicker({
					dateFormat: 'DD, dd-mm-yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu" ]
				});
				$("#txtToDate").attr("readonly", "true");
				$("#txtToDate").css({
					"background-color": "#FFF",
					"cursor": "text"
				});
				
				var grid = $("#grid-data").bootgrid({
								ajax: true,
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
								labels: {
									all: "Semua Data",
									infos: "Menampilkan {{ctx.start}} sampai {{ctx.end}} dari {{ctx.total}} data",
									loading: "Loading...",
									noResults: "Tidak Ada Data Yang Ditemukan!",
									refresh: "Refresh",
									search: "Cari"
								},
								url: "./Transaction/IncomingMaterial/DataSource.php?Filter=0",
								selection: true,
								multiSelect: true,
								rowSelect: true,
								keepSelection: true,
								formatters: {
									"commands": function(column, row)
									{
										return "<i style='cursor:pointer;' data-row-id=\"" + row.IncomingDetailsID + "\" data-material-name=\"" + row.MaterialName + "\" data-supplier-name=\"" + row.SupplierName + "\" data-quantity=\"" + row.Quantity + "\" data-remarks=\"" + row.Remarks + "\" class=\"fa fa-edit\" acronym title=\"Ubah Data\"></i>&nbsp;";
									}
								}
							}).on("loaded.rs.jquery.bootgrid", function()
							{
								/* Executes after data is loaded and rendered */
								grid.find(".fa-edit").on("click", function(e)
								{
									//Redirect($(this).data("link"));
									var IncomingDetailsID = $(this).data("row-id");
									var MaterialName = $(this).data("material-name");
									var SupplierName = $(this).data("supplier-name");
									var Quantity = $(this).data("quantity");
									var Remarks = $(this).data("remarks");
									EditData(IncomingDetailsID, MaterialName, SupplierName, Quantity, Remarks);
								});
							});
			});
		</script>
	</body>
</html>
