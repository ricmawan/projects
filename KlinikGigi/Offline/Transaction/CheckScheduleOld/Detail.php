<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
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
						<h5>Tambah Data Jadwal Periksa</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Pasien :
								</div>
								<div class="col-md-3">
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
													echo "<option value='".$row['PatientID']."' patientnumber='".$row['PatientNumber']."'>[".$row['PatientNumber']."] ".$row['PatientName']." - ".$row['Address']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Jadwal Periksa :
								</div>
								<div class="col-md-3">
									<input id="txtNextSchedule" name="txtNextSchedule" type="text" class="form-control-custom" placeholder="Jadwal Berikutnya" required />
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick='SubmitValidate()' ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
		</div>
		<script>
			function SubmitValidate() {
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

				if($("#ddlPatient").val() == "") {
					PassValidate = 0;
					$("#ddlPatient").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#ddlPatient").next().find("input").focus();
					FirstFocus = 1;
				}

				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
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
								$("#loading").show();
								$.ajax({
									url: "./Transaction/CheckSchedule/Insert.php",
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
				$("#txtNextSchedule").datepicker({
					dateFormat: 'DD, dd-mm-yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu" ]
				});
				$("#txtNextSchedule").attr("readonly", "true");
				$("#txtNextSchedule").css({
					"background-color": "#FFF",
					"cursor": "text"
				});
				
				$("#ddlPatient").combobox({
					select: function( event, ui ) {
						var PatientNumber = $("#ddlPatient option:selected").attr("patientnumber");				
						$("#txtPatientNumber").val(PatientNumber);
					}
				});
			});
		</script>
	</body>
</html>
