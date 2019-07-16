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
						 <h5>Kirim Model</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<?php
								echo '<input id="hdnUserTypeID" name="hdnUserTypeID" type="hidden" value="'.$_SESSION['UserTypeID'].'" />';
							?>
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="OutgoingModelIDReceiptNumber" data-visible="false" data-type="numeric" data-identifier="true">OutgoingModelIDReceiptNumber</th>
										<th data-column-id="TransactionDate">Tanggal</th>
										<th data-column-id="ReceiptNumber" data-type="numeric">No Resi</th>
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
									</tr>
								</thead>
							</table>
						</div>
						<button class="btn btn-primary" onclick="LoadOutgoingModelDetails(0);"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
						<button class="btn btn-danger" onclick="DeleteData('./Transaction/OutgoingModel/Delete.php');" ><i class="fa fa-close"></i> Hapus</button>
					</div>
				</div>
			</div>
			<div id="dialog-delete-outgoing-model" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 40px 0;"></span>Apakah anda yakin ingin menghapus pengiriman model dengen resi <span style="font-weight: bold; font-size: 18px; color: red;" id="ReceiptNumber"></span>?</p>
			</div>
			<div id="dialog-delete" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 40px 0;"></span>Apakah anda yakin ingin menghapus data terpilih?</p>
			</div>
			<div id="dialog-edit-outgoing-model-detail" title="Edit Tindakan" style="display: none;">
				<form class="col-md-12" id="EditForm" method="POST" action="" >
					<input type="hidden" id="hdnOutgoingModelDetailsID" name="hdnOutgoingModelDetailsID" value=0 autofocus="autofocus" />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Dokter:
						</div>
						<div class="col-md-6" >
							<div class="ui-widget" style="width: 100%;">
								<select name="ddlDoctor2" id="ddlDoctor2" class="form-control-custom" placeholder="Pilih Dokter" >
									<?php
										$sql = "SELECT UserID, UserName FROM master_user WHERE UserTypeID = 2";
										if(!$result = mysql_query($sql, $dbh)) {
											echo mysql_error();
											return 0;
										}
										while($row = mysql_fetch_array($result)) {
											echo "<option value='".$row['UserID']."' >".$row['UserName']."</option>";
										}
									?>
								</select>
							</div>
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Pasien:
						</div>
						<div class="col-md-6">
							<input id="hdnPatientID2" name="hdnPatientID2" type="hidden" value="0" />
							<div class="ui-widget" style="width: 100%;" id="dvPatient">
								<select name="ddlPatient2" id="ddlPatient2" class="form-control-custom" placeholder="Pilih Pasien" >
									<?php
										$sql = "SELECT PatientID, PatientName, PatientNumber, Address FROM master_patient";
										if(!$result = mysql_query($sql, $dbh)) {
											echo mysql_error();
											return 0;
										}
										while($row = mysql_fetch_array($result)) {
											echo "<option value='".$row['PatientID']."' patientnumber='".$row['PatientNumber']."'>".$row['PatientName']." - ".$row['Address']."</option>";
										}
									?>
								</select>
							</div>
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Tindakan:
						</div>
						<div class="col-md-6">
							<input type="text" autocomplete="off" id="txtExaminationName2" name="txtExaminationName2" class="form-control-custom" />
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Keterangan:
						</div>
						<div class="col-md-6">
							<textarea id="txtRemarks2" name="txtRemarks2" class="form-control-custom" ></textarea>
						</div>
					</div>
				</form>
			</div>
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
			<div id="dialog-outgoing-model" title="Tambah Pengiriman" style="display: none;">
				<div id="left-side" style="display: inline-block;width: 30%; height: 100%; float: left;">
					<form class="col-md-12" id="PostForm" method="POST" action="" >
						<input type="hidden" id="hdnOutgoingModelID" name="hdnOutgoingModelID" value=0 />
						<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								Tanggal:
							</div>
							<div class="col-md-8">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" style="width: 87%; display: inline-block;margin-right: 5px;" />
								</div>
							</div>
						</div>
						<br />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								No Resi:
							</div>
							<div class="col-md-8" >
								<input type="text" autocomplete="off" id="txtReceiptNumber" name="txtReceiptNumber" class="form-control-custom" onchange="updateHeader();" required autofocus />
							</div>
						</div>
						<hr style="border-top: 2px solid black;margin-top: 10px;margin-bottom: 10px;margin-right: 30px;margin-left: 1px;" />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								Dokter:
							</div>
							<div class="col-md-8">
								<div class="ui-widget" style="width: 100%;">
								<select name="ddlDoctor" id="ddlDoctor" class="form-control-custom" placeholder="Pilih Dokter" >
									<option value="" >-Pilih Dokter-</option>
									<?php
										$sql = "SELECT UserID, UserName FROM master_user WHERE UserTypeID = 2";
										if(!$result = mysql_query($sql, $dbh)) {
											echo mysql_error();
											return 0;
										}
										while($row = mysql_fetch_array($result)) {
											echo "<option value='".$row['UserID']."' >".$row['UserName']."</option>";
										}
									?>
								</select>
							</div>
							</div>
						</div>
						<br />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								Pasien:
							</div>
							<div class="col-md-8">
								<input id="hdnPatientID2" name="hdnPatientID2" type="hidden" value="0" />
								<div class="ui-widget" style="width: 100%;" id="dvPatient">
									<select name="ddlPatient" id="ddlPatient" class="form-control-custom" placeholder="Pilih Pasien" >
										<option value="" ></option>
										<?php
											$sql = "SELECT PatientID, PatientName, PatientNumber, Address FROM master_patient";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												echo "<option value='".$row['PatientID']."' patientnumber='".$row['PatientNumber']."'>".$row['PatientName']." - ".$row['Address']."</option>";
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<br />
						<div class="row" >
							<div class="col-md-3 labelColumn" >
								Tindakan:
							</div>
							<div class="col-md-8">
								<input type="text" autocomplete="off" id="txtExaminationName" name="txtExaminationName" class="form-control-custom" required />
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
					</form>
				</div>
				<div style=" width:1px; background-color:#000; position:absolute; top:0; bottom:0; left:calc(30% - 10px);float:left;">
				</div>
				<div style=" width:1px; background-color:#000; position:absolute; top:0; bottom:0; left:calc(30% - 12px);float:left;">
				</div>
				<div id="right-side" style="display: inline-block; width: calc(70% - 5px); height: 100%; float: left;">
					Detail Pengiriman: 
					<table class="table table-striped table-bordered table-hover" style="width:auto;padding-right:17px;" id="datainput">
						<thead style="background-color: black;color:white;height:25px;display:block;width:810px;">
							<td align="center" style="width:40px;">No</td>
							<td align="center" style="width: 165px;" >Dokter</td>
							<td align="center" style="width: 165px;" >Pasien</td>
							<td align="center" style="width: 200px;" >Tindakan</td>
							<td align="center" style="width: 180px;" >Keterangan</td>
							<td align="center" style="width: 60px;" >Opsi</td>
						</thead>
						<tbody style="display:block;max-height:200px;height:100%;overflow-y:auto;" id="tableContent">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<script>
			var oldSession;
			function guid() {
				function s4() {
					return Math.floor((1 + Math.random()) * 0x10000)
					.toString(16)
					.substring(1);
				}
				return s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4();
			}
			
			function EditData(OutgoingModelID, OutgoingModelDetailsID, DoctorID, PatientID, Remarks, ExaminationName) {
				$("#hdnOutgoingModelDetailsID").val(OutgoingModelDetailsID);
				$("#txtExaminationName2").val(ExaminationName);
				$("#ddlDoctor2").val(DoctorID);
				$("#ddlPatient2").val(PatientID);
				$("#ddlPatient2").next().find("input").val($("#ddlPatient2 option:selected").text());
				$("#txtRemarks2").val(Remarks);
				$("#dialog-edit-outgoing-model-detail").dialog({
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
							$.ajax({
								url: "./Transaction/OutgoingModel/Update.php",
								type: "POST",
								data: $("#EditForm").serialize(),
								dataType: "json",
								success: function(data) {
									$("#loading").hide();
									if(data.FailedFlag == '0') {
										$.notify(data.Message, "success");
										$.ajax({
											url: "./Transaction/OutgoingModel/Detail.php",
											type: "POST",
											data: { OutgoingModelID : OutgoingModelID },
											dataType: "json",
											success: function(data) {
												$("#loading").hide();
												if(data.FailedFlag == '0') {
													$("#tableContent").html(data.OutgoingModelDetails);
													$("#dialog-edit-outgoing-model-detail").dialog("destroy");
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
			
			function DeleteOutgoingModelDetails(OutgoingModelID, OutgoingModelDetailsID) {
				$("#dialog-delete").dialog({
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
							$.ajax({
								url: "./Transaction/OutgoingModel/DeleteDetail.php",
								type: "POST",
								data: { ID : OutgoingModelDetailsID },
								dataType: "html",
								success: function(data) {
									$("#loading").hide();
									var datadelete = data.split("+");
									var berhasil = datadelete[0];
									var gagal = datadelete [1];
									if(berhasil!="") {
										$.notify(berhasil, "success");
										$.ajax({
											url: "./Transaction/OutgoingModel/Detail.php",
											type: "POST",
											data: { OutgoingModelID : OutgoingModelID },
											dataType: "json",
											success: function(data) {
												$("#loading").hide();
												if(data.FailedFlag == '0') {
													$("#tableContent").html(data.OutgoingModelDetails);
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
									if(gagal!="") $.notify(gagal, "error");
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

			function updateHeader() {
				if($("#hdnOutgoingModelID").val() != 0) {
					$.ajax({
						url: "./Transaction/OutgoingModel/UpdateHeader.php",
						type: "POST",
						data: { OutgoingModelID : $("#hdnOutgoingModelID").val(), TransactionDate : $("#hdnTransactionDate").val(), ReceiptNumber : $("#txtReceiptNumber").val() },
						dataType: "json",
						success: function(data) {
							if(data.FailedFlag == '0') {

							}
							else {

							}
						},
						error: function(data) {
							$("#loading").hide();
							$.notify("Terjadi kesalahan sistem!", "error");
						}
					});
				}
			}

			function LoadOutgoingModelDetails(OutgoingModelID) {
				$("#loading").show();
				$.ajax({
					url: "./Transaction/OutgoingModel/Detail.php",
					type: "POST",
					data: { OutgoingModelID : OutgoingModelID },
					dataType: "json",
					success: function(data) {
						$("#loading").hide();
						if(data.FailedFlag == '0') {
							$("#tableContent").html(data.OutgoingModelDetails);
							$("#dialog-outgoing-model").dialog({
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
								width: 1200,
								modal: true,
								close: function() {
									$(this).dialog("destroy");
									$("#ddlDoctor").val("");
									$("#ddlDoctor").next().find("input").val("");
									$("#ddlPatient").val("");
									$("#ddlPatient").next().find("input").val("");
									$("#txtExaminationName").val("");
									$("#txtReceiptNumber").val("");
									$("#hdnOutgoingModelID").val(0);
									var transactionDate = new Date();
									transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
									$("#hdnTransactionDate").val(transactionDate);
									$("#grid-data").bootgrid("reload");
								},
								buttons: {
									"Simpan": function() {
										//$(this).dialog("close");
										var PassValidate = 1;
										var FirstFocus = 0;

										$(".form-control-custom").each(function() {
											if($(this).hasAttr('required')) {
												if($(this).val() == "") {
													PassValidate = 0;
													$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
													if(FirstFocus == 0) $(this).focus();
													FirstFocus = 1;
												}
											}
										});

										if($("#ddlDoctor").val() == "") {
											PassValidate = 0;
											$("#ddlDoctor").notify("Harus dipilih!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
											if(FirstFocus == 0) $("#ddlDoctor").focus();
											FirstFocus = 1;
										}
										
										if($("#ddlPatient").val() == "") {
											PassValidate = 0;
											$("#ddlPatient").next().find("input").notify("Harus dipilih!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
											if(FirstFocus == 0) $("#ddlPatient").next().find("input").focus();
											FirstFocus = 1;
										}

										if(PassValidate == 0) return false;
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
															url: "./Transaction/OutgoingModel/Insert.php",
															type: "POST",
															data: $("#PostForm").serialize(),
															dataType: "json",
															success: function(data) {
																$("#loading").hide();
																if(data.FailedFlag == '0') {
																	$.notify(data.Message, "success");
																	$("#ddlDoctor").val("");
																	$("#ddlDoctor").next().find("input").val("");
																	$("#ddlPatient").val("");
																	$("#ddlPatient").next().find("input").val("");
																	$("#txtExaminationName").val("");
																	$("#hdnOutgoingModelID").val(data.ID);
																	var transactionDate = new Date();
																	transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
																	$("#hdnTransactionDate").val(transactionDate);
																	//LoadOutgoingModelDetails($("#hdnOutgoingModelID").val());
																	$.ajax({
																		url: "./Transaction/OutgoingModel/Detail.php",
																		type: "POST",
																		data: { OutgoingModelID : data.ID },
																		dataType: "json",
																		success: function(data) {
																			$("#loading").hide();
																			if(data.FailedFlag == '0') {
																				$("#tableContent").html(data.OutgoingModelDetails);
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
										$("#ddlDoctor").val("");
										$("#ddlDoctor").next().find("input").val("");
										$("#ddlPatient").val("");
										$("#ddlPatient").next().find("input").val("");
										$("#txtExaminationName").val("");
										$("#txtReceiptNumber").val("");
										$("#hdnOutgoingModelID").val(0);
										var transactionDate = new Date();
										transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
										$("#hdnTransactionDate").val(transactionDate);
										$("#grid-data").bootgrid("reload");
									}
								}
							}).dialog("open");
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

			$(document).ready(function() {
				$("#ddlPatient").combobox();
				$("#ddlPatient").next().find("input").click(function() {
					$(this).val("");
				});

				$("#ddlPatient2").combobox();
				$("#ddlPatient2").next().find("input").click(function() {
					$(this).val("");
				});

				$("#txtTransactionDate").datepicker({
					dateFormat: 'DD, dd M yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu" ],
					monthNames: [ "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember" ],
					monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des" ],
					maxDate : "+0D",
					showOn: "button",
					buttonImage: "./assets/img/calendar.gif",
					buttonImageOnly: true,
					buttonText: "Pilih Tanggal",
					onSelect: function(dateText, obj) {
						transactionDate = obj.selectedYear + "-" + ("0" + (obj.selectedMonth + 1)).slice(-2) + "-" + ("0" + obj.selectedDay).slice(-2);
						$("#hdnTransactionDate").val(transactionDate);
						updateHeader();
						$("#txtReceiptNumber").focus();
					}
				}).datepicker("setDate", new Date());

				var transactionDate = new Date();
				transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
				$("#hdnTransactionDate").val(transactionDate);

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
							url: "./Transaction/OutgoingModel/DataSource.php",
							selection: true,
							multiSelect: true,
							rowSelect: true,
							keepSelection: false,
							formatters: {
								"commands": function(column, row)
								{
									return "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingModelID + "\" data-transaction-date=\"" + row.TransactionDate + "\" data-receipt-number=\"" + row.ReceiptNumber + "\" class=\"fa fa-edit\" acronym title=\"Edit Pengiriman\"></i>";
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							grid.find(".fa-edit").on("click", function(e)
							{
								$("#txtTransactionDate").val($(this).data("transaction-date"));
								$("#txtReceiptNumber").val($(this).data("receipt-number"));
								$("#hdnOutgoingModelID").val($(this).data("row-id"));
								LoadOutgoingModelDetails($(this).data("row-id"));
								$("#ddlDoctor").val("");
								$("#ddlDoctor").next().find("input").val("");
								$("#ddlPatient").val("");
								$("#ddlPatient").next().find("input").val("");
								$("#txtExaminationName").val("");
								$(".hdnSessionID").val(guid());
							});
						});
				setInterval(function(){ $("#grid-data").bootgrid("reload") }, 900000);
			});
		</script>
	</body>
</html>