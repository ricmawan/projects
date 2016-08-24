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
			<form id="PostForm" method="post">
				<input type="hidden" value=0 id="hdnMedicationID" name="hdnMedicationID" />
			</form>
			<div id="dialog-confirm-print" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 40px 0;"></span>Apakah anda yakin ingin mencetak nota untuk pasien bernama <span style="font-weight: bold; font-size: 18px; color: red;" id="patientName2"></span>?</p>
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
							url: "./Transaction/Payment/DataSource.php",
							selection: false,
							multiSelect: false,
							rowSelect: true,
							keepSelection: false,
							formatters: {
								"commands": function(column, row)
								{
									return "<i style='cursor:pointer;' data-row-id=\"" + row.MedicationID + "\" data-patient-name=\"" + row.PatientName + "\" class=\"fa fa-print\" acronym title=\"Cetak Nota\"></i>&nbsp;";
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							grid.find(".fa-print").on("click", function(e)
							{
								$("#patientName2").html($(this).data("patient-name"));
								$("#hdnMedicationID").val($(this).data("row-id"));
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
									width: 400,
									close: function() {
										$(this).dialog("destroy");
									},
									modal: true,
									buttons: {
										"Ya": function() {
											$(this).dialog("destroy");
											$("#loading").show();
											$("#loading").show();
											form = $("#PostForm");
											form.attr("action", "./Transaction/Payment/Print.php");
											form.attr("target", "_blank");
											form.submit();
											$("#loading").hide();
										},
										"Tidak": function() {
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
