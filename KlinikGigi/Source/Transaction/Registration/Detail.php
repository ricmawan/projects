<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$MedicationID = mysql_real_escape_string($_GET['ID']);
		$MedicationNumber = "";
		$IsEdit = 0;
	}
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h5>Pendaftaran Pasien</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									ID Pasien :
								</div>
								<div class="col-md-3">
									<input type="text" autocomplete=off id="txtPatientNumber" name="txtPatientNumber" class="form-control-custom" placeholder="ID Pasien" onkeypress="CheckPatientID(event, this.value);" />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Pasien :
								</div>
								<div class="col-md-3">
									<input id="hdnPatientID" name="hdnPatientID" type="hidden" value="0" />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" value="0" />
									<input id="IsExists" type="hidden" value="0" />
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
									<input id="txtPatientName" style="display: none;" name="txtPatientName" type="text" class="form-control-custom" placeholder="Nama Pasien" readonly required />
								</div>
							</div>
							<span id="PatientForm" style="display: none;">
								<br />
								<div class="row">
									<div class="col-md-2 labelColumn">
										Tanggal Lahir :
									</div>
									<div class="col-md-3">
										<input id="txtBirthDate" name="txtBirthDate" type="text" class="form-control-custom DatePickerMonthYearUntilNow" placeholder="Tanggal Lahir" required />
									</div>
								</div>
								<br />
								<div class="row">
									<div class="col-md-2 labelColumn">
										Telepon :
									</div>
									<div class="col-md-3">
										<input id="txtTelephone" name="txtTelephone" maxlength=30 type="text" class="form-control-custom" placeholder="Telepon" required />
									</div>
								</div>
								<br />
								<div class="row">
									<div class="col-md-2 labelColumn">
										Email :
									</div>
									<div class="col-md-3">
										<input id="txtEmail" name="txtEmail" type="text" class="form-control-custom" placeholder="Email" />
									</div>
								</div>
								<br />
								<div class="row">
									<div class="col-md-2 labelColumn">
										Alamat:
									</div>
									<div class="col-md-3">
										<textarea id="txtAddress" name="txtAddress" class="form-control-custom" placeholder="Alamat" required ></textarea>
									</div>
								</div>
								<br />
								<div class="row">
									<div class="col-md-2 labelColumn">
										Kota :
									</div>
									<div class="col-md-3">
										<input id="txtCity" maxlength=30 name="txtCity" type="text" class="form-control-custom" placeholder="Kota" />
									</div>
								</div>
								<br />
								<div class="row">
									<div class="col-md-2 labelColumn">
										Alergi :
									</div>
									<div class="col-md-3">
										<textarea id="txtAllergy" name="txtAllergy" class="form-control-custom" placeholder="Alergi"></textarea>
									</div>
								</div>
							</span>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nomor Urut :
								</div>
								<div class="col-md-3">
									<input id="txtOrderNumber" name="txtOrderNumber" type="text" class="form-control-custom" placeholder="Nomor Urut" readonly />
								</div>
							</div>
							<br />
						</form>
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-default" id="btnRegister" onclick="SubmitValidate();" ><i class="fa fa-save "></i> Daftar</button>&nbsp;&nbsp;								
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
		</div>
		<script>
			function CheckPatientID(evt, value) {
				var e = evt || window.event;
				var charCode = e.which || e.keyCode;
				if (charCode == 13) {
					$("#loading").show();
					$.ajax({
						url: "./Transaction/Registration/CheckPatient.php",
						type: "POST",
						data: { PatientNumber : value },
						dataType: "json",
						success: function(data) {
							$("#loading").hide();
							$("#IsExists").val(data.IsExists);
							if(data.IsExists == '0') {
								$.notify(data.Message, "warn");
								$("#PatientForm").show();
								$("#txtPatientName").show();
								$("#txtPatientName").attr("readonly", false);
								$("#txtPatientName").val("");
								$("#dvPatient").hide();
								$("#txtBirthDate").val("");
								$("#txtAddress").val("");
								$("#txtAllergy").val("");
								$("#txtCity").val("");
								$("#txtTelephone").val("");
								$("#hdnPatientID").val("0");
							}
							else {
								$("#PatientForm").hide();
								$("#txtPatientName").show();
								$("#dvPatient").hide();
								$("#txtPatientName").attr("readonly", "readonly");
								$("#txtPatientName").val(data.PatientName);
								$("#hdnPatientID").val(data.ID);
							}
						},
						error: function(data) {
							$("#loading").hide();
							$.notify("Terjadi kesalahan sistem!", "error");
						}
					});
				}
			}
			
			function SubmitValidate() {
				if($("#txtPatientNumber").val() == "") {
					$("#txtPatientNumber").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					$("#txtPatientNumber").focus();
					return false;
				}
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
						close: function() {
							$(this).dialog("destroy");
						},
						modal: true,
						buttons: {
							"Ya": function() {
								$(this).dialog("destroy");
								//click button without enter first
								if($("#hdnPatientID").val() == "0" && $("#PatientForm").css('display') != "inline") {
									$("#loading").show();
									$.ajax({
										url: "./Transaction/Registration/CheckPatient.php",
										type: "POST",
										data: { PatientNumber : $("#txtPatientNumber").val() },
										dataType: "json",
										success: function(data) {
											$("#loading").hide();
											$("#IsExists").val(data.IsExists);
											if(data.IsExists == '0') {
												$.notify(data.Message, "warn");
												$("#PatientForm").show();
												$("#txtPatientName").attr("readonly", false);
												$("#txtPatientName").val("");
												$("#txtBirthDate").val("");
												$("#txtAddress").val("");
												$("#txtAllergy").val("");
												$("#txtCity").val("");
												$("#txtTelephone").val("");
												$("#hdnPatientID").val("0");
											}
											else {
												$("#PatientForm").hide();
												$("#txtPatientName").attr("readonly", "readonly");
												$("#txtPatientName").val(data.PatientName);
												$("#hdnPatientID").val(data.ID);
												$("#loading").show();
												$.ajax({
													url: "./Transaction/Registration/Insert.php",
													type: "POST",
													data: $("#PostForm").serialize(),
													dataType: "json",
													success: function(data) {
														if(data.FailedFlag == '0') {
															$.notify(data.Message, "success");
															$("#txtOrderNumber").val(data.OrderNumber);
															setTimeout(Back, 2000);
														}
														else {
															$("#loading").hide();
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
										error: function(data) {
											$("#loading").hide();
											$.notify("Terjadi kesalahan sistem!", "error");
										}
									});
								}
								//click button with enter first
								else {
									if($("#IsExists").val() == "0") {
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
										
										if(PassValidate == 0) {
											$("html, body").animate({
												scrollTop: 0
											}, "slow");
											return false;
										}
										else {
											$("#loading").show();
											$.ajax({
												url: "./Master/Patient/Insert.php",
												type: "POST",
												data: $("#PostForm").serialize(),
												dataType: "json",
												success: function(data) {
													if(data.FailedFlag == '0') {
														//$.notify(data.Message, "success");
														$("#hdnPatientID").val(data.ID);
														$.ajax({
															url: "./Transaction/Registration/Insert.php",
															type: "POST",
															data: $("#PostForm").serialize(),
															dataType: "json",
															success: function(data) {
																if(data.FailedFlag == '0') {
																	$.notify(data.Message, "success");
																	$("#txtOrderNumber").val(data.OrderNumber);
																	setTimeout(Back, 2000);
																}
																else {
																	$("#loading").hide();
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
														$("#loading").hide();
														$.notify(data.Message, "error");
													}
												},
												error: function(data) {
													$("#loading").hide();
													$.notify("Terjadi kesalahan sistem!", "error");
												}
											});
										}
									}
									else {
										$("#loading").show();
										$.ajax({
											url: "./Transaction/Registration/Insert.php",
											type: "POST",
											data: $("#PostForm").serialize(),
											dataType: "json",
											success: function(data) {
												if(data.FailedFlag == '0') {
													$.notify(data.Message, "success");
													$("#txtOrderNumber").val(data.OrderNumber);
													setTimeout(Back, 2000);
												}
												else {
													$("#loading").hide();
													$.notify(data.Message, "error");					
												}
											},
											error: function(data) {
												$("#loading").hide();
												$.notify("Terjadi kesalahan sistem!", "error");
											}
										});
									}
								}
							},
							"Tidak": function() {
								$(this).dialog("destroy");
								return false;
							}
						}
					}).dialog("open");
				}
			}
			
			$(document).ready(function() {
				$("#ddlPatient").combobox({
					select: function( event, ui ) {
						var PatientNumber = $("#ddlPatient option:selected").attr("patientnumber");				
						$("#txtPatientNumber").val(PatientNumber);
					}
				});
				$("#ddlPatient").next().find("input").click(function() {
					$(this).val("");
				});
			});
		</script>
	</body>
</html>
