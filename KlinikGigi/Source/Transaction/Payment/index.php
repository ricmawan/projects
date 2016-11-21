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
						 <h5>Pembayaran</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="MedicationIDNo" data-visible="false" data-type="numeric" data-identifier="true">UserID</th>
										<th data-column-id="OrderNumber" data-type="numeric">No Urut</th>
										<th data-column-id="PatientNumber">ID Pasien</th>
										<th data-column-id="PatientName">Nama Pasien</th>
										<th data-column-id="Allergy">Alergi</th>
										<th data-column-id="Total" data-align="right">Total</th>
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="dialog-confirm-print" title="Pembayaran" style="display: none;">
				<form class="col-md-12" id="PostForm" method="POST" action="" >
					<input type="hidden" value=0 id="hdnMedicationID" name="hdnMedicationID" />
					<input type="hidden" value=0 id="hdnPatientID" name="hdnPatientID" />
					<div class="row">
						<div class="col-md-4 labelColumn">
							Pasien :
						</div>
						<div class="col-md-6" id="patientName" style="font-weight: bold; font-size: 15px; color: red;">
						</div>
					</div>
					<br />
					<div class="row" id="rowDebt" style="display:none;">
						<div class="col-md-4 labelColumn">
							Kekurangan :
						</div>
						<div class="col-md-6" id="Total" style="color: red;text-align: right">
							<input type="text" id="txtDebt" style="color: red;text-align: right" name="txtDebt" class="form-control-custom" readonly />
						</div>
						<br />
						<br />
					</div>
					<div class="row">
						<div class="col-md-4 labelColumn">
							Total :
						</div>
						<div class="col-md-6" id="Total" style="color: red;text-align: right">
							<input type="text" id="txtTotal" style="color: red;text-align: right" name="txtTotal" class="form-control-custom" readonly />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-md-4 labelColumn">
							Cash :
						</div>
						<div class="col-md-6">
							<input type="text" value="0.00" style="text-align:right;" id="txtCash" name="txtCash" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-md-4 labelColumn">
							Debit :
						</div>
						<div class="col-md-6">
							<input type="text" value="0.00" style="text-align:right;" id="txtDebit" name="txtDebit" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-md-4 labelColumn">
							Jadwal Berikutnya :
						</div>
						<div class="col-md-6">
							<input id="txtNextSchedule" name="txtNextSchedule" type="text" class="form-control-custom" placeholder="Jadwal Berikutnya" />
						</div>
					</div>
					<br />
					<p id="lblInfo" style="display:none;"><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Masih terdapat kekurangan pembayaran dari transaksi sebelumnya!</p>
				</form>
			</div>
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
		</div>
		<script>
			function LoadDetails() {
				$("#loading").show();
				$("#txtCash").val("0.00");
				$("#txtDebit").val("0.00");
				var MedicationID = $("#hdnMedicationID").val();
				$.ajax({
					url: "./Transaction/Payment/Detail.php",
					type: "POST",
					data: { MedicationID : MedicationID },
					dataType: "json",
					success: function(data) {
						$("#loading").hide();
						$("#txtCash").val(returnRupiah(data[0].Cash));
						$("#txtDebit").val(returnRupiah(data[0].Debit));	
						if(data[0].Debt > 0) {
							$("#rowDebt").show();
							$("#lblInfo").show();
							$("#txtDebt").val(returnRupiah(data[0].Debt));
							$('.ui-dialog-buttonpane button:contains("Simpan")').button().hide();
						}
						else {
							$("#rowDebt").hide();
							$("#lblInfo").hide();
							$('.ui-dialog-buttonpane button:contains("Simpan")').button().show();
						}
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
			$(document).ready(function() {
				$("#txtNextSchedule").datepicker({
					dateFormat: 'DD, dd-mm-yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu" ]
				});
				$("#txtNextSchedule").attr("readonly", "true");
				$("#txtNextSchedule").css({
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
							labels: {
								all: "Semua Data",
								infos: "Menampilkan {{ctx.start}} sampai {{ctx.end}} dari {{ctx.total}} data",
								loading: "Loading...",
								noResults: "Tidak Ada Data Yang Ditemukan!",
								refresh: "Refresh",
								search: "Cari"
							},
							url: "./Transaction/Payment/DataSource.php",
							selection: false,
							multiSelect: false,
							rowSelect: true,
							keepSelection: false,
							formatters: {
								"commands": function(column, row)
								{
									return "<i style='cursor:pointer;' data-total=\"" + row.Total + "\" data-row-id=\"" + row.MedicationID + "\" data-patient-id=\"" + row.PatientID + "\" data-patient-name=\"" + row.PatientName + "\" class=\"fa fa-print\" acronym title=\"Cetak Nota\"></i>&nbsp;";
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							grid.find(".fa-print").on("click", function(e)
							{
								$("#hdnMedicationID").val($(this).data("row-id"));
								$("#hdnPatientID").val($(this).data("patient-id"));
								$("#patientName").html($(this).data("patient-name"));
								$("#txtTotal").val(returnRupiah($(this).data("total")));
								LoadDetails();
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
											var Total = parseFloat($("#txtTotal").val().replace(/\,/g, ""));
											if((Cash + Debit) > Total) {
												$("#txtCash").notify("Total cash dan debit melebihi total yang harus dibayar!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
												$("#txtDebit").notify("Total cash dan debit melebihi total yang harus dibayar!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
												return false;
											}
											/*else if((Cash + Debit) < Total) {
												$("#txtCash").notify("Total cash dan debit kurang dari total yang harus dibayar!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
												$("#txtDebit").notify("Total cash dan debit kurang dari total yang harus dibayar!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
												return false
											}*/
											//else if((Cash + Debit) == Total) {
											else {
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
																url: "./Transaction/Payment/Insert.php",
																type: "POST",
																data: $("#PostForm").serialize(),
																dataType: "json",
																success: function(data) {
																	$("#loading").hide();
																	if(data.FailedFlag == '0') {
																		$.notify(data.Message, "success");
																		$("#loading").show();
																		form = $("#PostForm");
																		form.attr("action", "./Transaction/Payment/Print.php");
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
