<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			th[data-column-id="Opsi"] {
				width: 80px !important;
			}
			.ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
				float: left;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Tindakan</h5>
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
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="dialog-confirm-finish" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 40px 0;"></span>Apakah anda yakin pasien bernama <span style="font-weight: bold; font-size: 18px; color: red;" id="patientName2"></span> sudah menjalani seluruh pemeriksaan & tindakan?</p>
			</div>
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
			<div id="dialog-medication" title="Tambah Tindakan" style="display: none;">
				<div id="left-side" style="display: inline-block;width: 45%; height: 100%; float: left;">
					<form class="col-md-12" id="PostForm" method="POST" action="" >
						<input type="hidden" id="hdnMedicationID" name="hdnMedicationID" value=0 />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								Pasien:
							</div>
							<div class="col-md-8">
								<span id="patientName" style="font-weight: bold; font-size: 15px; color: red;"></span>
							</div>
						</div>
						<br />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								Tindakan:
							</div>
							<div class="col-md-8" >
								<div class="ui-widget" style="width: 100%;">
									<select name="ddlExamination" id="ddlExamination" class="form-control-custom" placeholder="Pilih Tindakan" >
										<option value="" selected> </option>
										<?php
											$sql = "SELECT ExaminationID, ExaminationName, Price FROM master_examination";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												echo "<option value='".$row['ExaminationID']."' price=".$row['Price']." >".$row['ExaminationName']."</option>";
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<br />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								Harga:
							</div>
							<div class="col-md-8">
								<input type="text" id="txtExaminationPrice" name="txtExaminationPrice" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" class="form-control-custom" style="text-align: right;" value="0.00" />
							</div>
						</div>
						<br />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								Jumlah:
							</div>
							<div class="col-md-8">
								<input type="text" autocomplete="off" id="txtQuantity" name="txtQuantity" onkeypress="return isNumberKey(event)" onchange="Calculate();" class="form-control-custom" style="text-align: right;" value=1 />
							</div>
						</div>
						<br />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								Total:
							</div>
							<div class="col-md-8">
								<input type="text" id="txtTotal" name="txtTotal" class="form-control-custom" style="text-align: right;" readonly value="0.00" />
							</div>
						</div>
						<br />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								Keterangan:
							</div>
							<div class="col-md-8">
								<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" ></textarea>
							</div>
						</div>
					</div>
				</form>
				<div style=" width:1px; background-color:#000; position:absolute; top:0; bottom:0; left:calc(45% - 10px);float:left;">
				</div>
				<div id="right-side" style="display: inline-block; width: calc(55% - 5px); height: 100%; float: left;">
					Tindakan Sebelumnya: 
					<table class="table table-striped table-bordered table-hover" style="width:auto;" id="datainput">
						<thead style="background-color: black;color:white;height:25px;display:block;width:523px;margin-right:17px;">
							<td align="center" style="width:33px;">No</td>
							<td align="center" style="width: 200px;" >Tindakan</td>
							<td align="center" style="width: 80px;" >Jumlah</td>
							<td align="center" style="width: 210px;" >Keterangan</td>
						</thead>
						<tbody style="display:block;max-height:200px;height:100%;overflow-y:auto;" id="tableContent">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<script>
			function LoadMedicationDetails(MedicationID) {				
				$("#loading").show();
				$.ajax({
					url: "./Transaction/Medication/Detail.php",
					type: "POST",
					data: { MedicationID : MedicationID },
					dataType: "json",
					success: function(data) {
						$("#loading").hide();
						if(data.FailedFlag == '0') {
							$("#tableContent").html(data.MedicationDetails);
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
			}
			
			function Calculate() {
				var price = $("#txtExaminationPrice").val().replace(/\,/g, "");
				var qty = $("#txtQuantity").val();
				if(qty == "") {
					qty = 1;
					$("#txtQuantity").val(1);
				}
				var Total = parseFloat(price) * parseFloat(qty);
				$("#txtTotal").val(returnRupiah(Total.toFixed(2).toString()));
			}
						
			$(document).ready(function() {
				$("#ddlExamination").combobox({
					select: function( event, ui ) {
						$("#txtExaminationPrice").val(returnRupiah($("#ddlExamination option:selected").attr("price")));
						Calculate();
					}
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
							url: "./Transaction/Medication/DataSource.php",
							selection: false,
							multiSelect: false,
							rowSelect: true,
							keepSelection: false,
							formatters: {
								"commands": function(column, row)
								{
									return "<i style='cursor:pointer;' data-row-id=\"" + row.MedicationID + "\" data-patient-name=\"" + row.PatientName + "\" class=\"fa fa-medkit\" acronym title=\"Tambah Tindakan\"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i style='cursor:pointer;' data-patient-name=\"" + row.PatientName + "\" data-row-id=\"" + row.MedicationID + "\" class=\"fa fa-check-square-o\" acronym title=\"Selesai\"></i>";
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							grid.find(".fa-medkit").on("click", function(e)
							{
								$("#patientName").html($(this).data("patient-name"));
								$("#hdnMedicationID").val($(this).data("row-id"));
								LoadMedicationDetails($(this).data("row-id"));
								$("#dialog-medication").dialog({
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
									width: 1000,
									modal: true,
									close: function() {
										$(this).dialog("destroy");
									},
									buttons: {
										"Tambah": function() {
											//$(this).dialog("close");
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
														var PassValidate = 1;
														if($("#ddlExamination").val() == "") {
															PassValidate = 0;
															$("#ddlExamination").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
															if(FirstFocus == 0) $("#ddlExamination").next().find("input").focus();
															FirstFocus = 1;
														}
														
														if($("#txtQuantity").val() == "") {
															$("#txtQuantity").val(1);
															Calculate();
														}
														if(PassValidate == 0) return false;
														else {
															$("#loading").show();
															$.ajax({
																url: "./Transaction/Medication/Insert.php",
																type: "POST",
																data: $("#PostForm").serialize(),
																dataType: "json",
																success: function(data) {
																	$("#loading").hide();
																	if(data.FailedFlag == '0') {
																		$.notify(data.Message, "success");
																		$("#ddlExamination").val("");
																		$("#ddlExamination").next().find("input").val("");
																		$("#txtExaminationPrice").val("0.00");
																		$("#txtTotal").val("0.00");
																		$("#txtQuantity").val(1);
																		$("#txtRemarks").val("");
																		LoadMedicationDetails($("#hdnMedicationID").val());
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
														}
													},
													"Tidak": function() {
														$(this).dialog("destroy");
														return false;
													}
												}
											}).dialog("open");
										},
										"Tutup": function() {
											$(this).dialog("destroy");
										}
									}
								}).dialog("open");
							});
							
							grid.find(".fa-check-square-o").on("click", function(e)
							{
								$("#patientName2").html($(this).data("patient-name"));
								$("#hdnMedicationID").val($(this).data("row-id"));
								$("#dialog-confirm-finish").dialog({
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
											$.ajax({
												url: "./Transaction/Medication/Finish.php",
												type: "POST",
												data: { MedicationID : $("#hdnMedicationID").val() },
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
										"Tidak": function() {
											$(this).dialog("destroy");
											return false;
										}
									}
								}).dialog("open");
							});
						});
				setInterval(function(){ $("#grid-data").bootgrid("reload") }, 900000);
			});
		</script>
	</body>
</html>
