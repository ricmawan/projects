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
						 <h5>Pembayaran Kekurangan</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="PatientIDName" data-visible="false" data-type="numeric" data-identifier="true">PatientID</th>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="PatientNumber">ID Pasien</th>
										<th data-column-id="PatientName">Nama Pasien</th>
										<th data-column-id="TransactionDate">Tanggal</th>
										<th data-column-id="Total" data-align="right">Total</th>
										<th data-column-id="Cash" data-align="right">Cash</th>
										<th data-column-id="Debit" data-align="right">Debit</th>
										<th data-column-id="Debt" data-align="right">Kekurangan</th>
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="dialog-confirm-print" title="Pembayaran Kekurangan" style="display: none;">
				<form class="col-md-12" id="PostForm" method="POST" action="" >
					<input type="hidden" value=0 id="hdnMedicationID" name="hdnMedicationID" />
					<div class="row">
						<div class="col-md-3 labelColumn">
							Pasien :
						</div>
						<div class="col-md-6" id="patientName" style="font-weight: bold; font-size: 15px; color: red;">
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-md-3 labelColumn">
							Kekurangan :
						</div>
						<div class="col-md-6" id="Total" style="color: red;text-align: right">
							<input type="text" id="txtDebt" style="color: red;text-align: right" name="txtDebt" class="form-control-custom" readonly />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-md-3 labelColumn">
							Cash :
						</div>
						<div class="col-md-6">
							<input type="text" value="0.00" style="text-align:right;" id="txtCash" name="txtCash" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-md-3 labelColumn">
							Debit :
						</div>
						<div class="col-md-6">
							<input type="text" value="0.00" style="text-align:right;" id="txtDebit" name="txtDebit" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
						</div>
					</div>
					<br />
				</form>
			</div>
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
		</div>
		<script>
			$(document).ready(function() {
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
							url: "./Transaction/DebtPayment/DataSource.php",
							selection: false,
							multiSelect: true,
							rowSelect: true,
							keepSelection: true,
							formatters: {
								"commands": function(column, row)
								{
									return "<i style='cursor:pointer;' data-debt=\"" + row.Debt + "\" data-row-id=\"" + row.MedicationID + "\" data-patient-name=\"" + row.PatientName + "\" class=\"fa fa-print\" acronym title=\"Bayar Kekurangan\"></i>&nbsp;";
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							grid.find(".fa-print").on("click", function(e)
							{
								$("#hdnMedicationID").val($(this).data("row-id"));
								$("#patientName").html($(this).data("patient-name"));
								$("#txtDebt").val(returnRupiah($(this).data("debt")));
								$("#txtCash").val("0.00");
								$("#txtDebit").val("0.00");
								//LoadDetails();
								$("#dialog-confirm-print").dialog({
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
									width: 675,
									close: function() {
										$(this).dialog("destroy");
									},
									modal: true,
									buttons: {
										"Simpan": function() {
											var Cash = parseFloat($("#txtCash").val().replace(/\,/g, ""));
											var Debit = parseFloat($("#txtDebit").val().replace(/\,/g, ""));
											var Total = parseFloat($("#txtDebt").val().replace(/\,/g, ""));
											if((Cash + Debit) > Total) {
												$("#txtCash").notify("Total cash dan debit melebihi total yang harus dibayar!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
												$("#txtDebit").notify("Total cash dan debit melebihi total yang harus dibayar!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
												return false;
											}
											else if((Cash + Debit) < Total) {
												$("#txtCash").notify("Total cash dan debit kurang dari total yang harus dibayar!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
												$("#txtDebit").notify("Total cash dan debit kurang dari total yang harus dibayar!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
												return false
											}
											else if((Cash + Debit) == Total) {
												$("#dialog-confirm").dialog({
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
													width: 400,
													modal: true,
													close: function() {
														$(this).dialog("destroy");
													},
													buttons: {
														"Ya": function() {
															$(this).dialog("destroy");
															$("#loading").show();
															$.ajax({
																url: "./Transaction/DebtPayment/Insert.php",
																type: "POST",
																data: $("#PostForm").serialize(),
																dataType: "json",
																success: function(data) {
																	$("#loading").hide();
																	if(data.FailedFlag == '0') {
																		$.notify(data.Message, "success");
																		$("#loading").show();
																		form = $("#PostForm");
																		form.attr("action", "./Transaction/DebtPayment/Print.php");
																		form.attr("target", "_blank");
																		form.submit();
																		$("#loading").hide();
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
														"Tidak": function() {
															$(this).dialog("destroy");
															return false;
														}
													}
												}).dialog("open");
											}
										},
										"Tutup": function() {
											$(this).dialog("destroy");
											return false;
										}
									}
								}).dialog("open");
							});
						});
			});
		</script>
	</body>
</html>
