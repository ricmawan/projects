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
						 <h5>Rujukan</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<?php
								echo '<input id="hdnUserTypeID" name="hdnUserTypeID" type="hidden" value="'.$_SESSION['UserTypeID'].'" />';
							?>
							<input id="hdnScheduledDate" name="hdnScheduledDate" type="hidden" />
							<input id="hdnPatientName" name="hdnPatientName" type="hidden" />
							<input id="hdnPhone" name="hdnPhone" type="hidden" />
							<input id="hdnEmail" name="hdnEmail" type="hidden" />
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="MedicationIDNo" data-visible="false" data-type="numeric" data-identifier="true">UserID</th>
										<th data-column-id="OrderNumber" data-type="numeric">No Urut</th>
										<th data-column-id="PatientNumber">ID Pasien</th>
										<th data-column-id="PatientName">Nama Pasien</th>
										<th data-column-id="Email">Email</th>
										<th data-column-id="Telephone">Nomor HP</th>
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
			
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>

			<div id="dialog-referral" title="Rujukan" style="display: none;">
				<div id="left-side" >
					<form class="col-md-8" id="PostForm" method="POST" action="" >
						<input type="hidden" id="hdnMedicationID" name="hdnMedicationID" value=0 />
						<div class="row" >
							<div class="col-md-2 labelColumn" >
								Pasien:
							</div>
							<div class="col-md-4">
								<span id="patientName" style="font-weight: bold; font-size: 15px; color: red;"></span>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn" >
								Pilih Cabang:
							</div>
							<div class="col-md-4" >
								<select id="ddlBranch" name="ddlBranch" class="form-control-custom" onchange="reloadSchedule();">
									<?php
										$sql = "SELECT BranchID, BranchName, StartHour, EndHour FROM master_branch";
										if(!$result = mysql_query($sql, $dbh)) {
											echo mysql_error();
											return 0;
										}
										while($row = mysql_fetch_array($result)) {
											echo "<option value='".$row['BranchID']."' startHour=".$row['StartHour']." endHour=".$row['EndHour']." >".$row['BranchName']."</option>";
										}
									?>
								</select>
							</div>
						</div>
						<br />
						<div class="row" >
							<div class="col-md-2 labelColumn" >
								Tanggal:
							</div>
							<div class="col-md-4">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtScheduledDate" name="txtScheduledDate" type="text" class="form-control-custom" placeholder="Tanggal Rujukan" />
								</div>
							</div>
						</div>
						<br />
						<div class="row">
							<iframe class="col-md-12" id="scheduleFrame" style="border: 0;overflow: hidden;width:1090px;height:310px;" scrolling="none">
								
							</iframe>
						</div>
					</form>
				</div>
			</div>
		</div>
		<script>
			function reloadSchedule() {
				var BranchID = $("#ddlBranch").val();
				var ScheduledDate = $("#hdnScheduledDate").val();
				var PatientName = $("#hdnPatientName").val();
				var Phone = $("#hdnPhone").val();
				var Email = $("#hdnEmail").val();
				$("#loading").show();
				$("#scheduleFrame").attr("src", "http://imdentalspecialist.com/Transaction/Referral/Schedule.php?BranchID=" + BranchID + "&ScheduledDate=" + ScheduledDate + "&PatientName=" + PatientName + "&Phone=" + Phone + "&Email=" + Email );
			}

			window.addEventListener("message", function(event) {
				if(event.data == "loaded") {
					$("#loading").hide();
					$("#scheduleFrame").css({ "width": (parseFloat($("#scheduleFrame").css("width")) + 1) });
					$("#scheduleFrame").css({ "width": (parseFloat($("#scheduleFrame").css("width")) - 1) });
				}
				else if(event.data == "Error") {
					$.notify(data.Message, "error");
				}
				else {
					$.notify(event.data, "success");
					reloadSchedule();
				}
			});
			
			$(document).ready(function() {
				$("#txtScheduledDate").datepicker({
					dateFormat: 'DD, dd M yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu" ],
					monthNames: [ "Jan", "Feb", "Mar", "Apr", "Mey", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des" ],
					minDate : "+0D",
					onSelect: function(dateText, obj) {
						var ScheduledDate = obj.selectedYear + "-" + ("0" + (obj.selectedMonth + 1)).slice(-2) + "-" + ("0" + obj.selectedDay).slice(-2);
						$("#hdnScheduledDate").val(ScheduledDate);
						reloadSchedule();
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
							url: "./Transaction/Referral/DataSource.php",
							selection: false,
							multiSelect: false,
							rowSelect: true,
							keepSelection: false,
							formatters: {
								"commands": function(column, row)
								{
									return "<i style='cursor:pointer;' data-row-id=\"" + row.MedicationID + "\" data-email=\"" + row.Email + "\" data-phone=\"" + row.Telephone + "\" data-patient-name=\"" + row.PatientName + "\" class=\"fa fa-ambulance\" acronym title=\"Tambah Rujukan\"></i>";
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							grid.find(".fa-ambulance").on("click", function(e)
							{
								$("#patientName").html($(this).data("patient-name"));
								$("#hdnMedicationID").val($(this).data("row-id"));
								$("#hdnPatientName").val($(this).data("patient-name"));
								$("#hdnPhone").val($(this).data("phone"));
								$("#hdnEmail").val($(this).data("email"));
								//LoadMedicationDetails($(this).data("row-id"));
								$("#dialog-referral").dialog({
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
									height: 600,
									width: 1200,
									modal: true,
									close: function() {
										$(this).dialog("destroy");
									}
								}).dialog("open");
							});
						});
				//setInterval(function(){ $("#grid-data").bootgrid("reload") }, 900000);
			});
		</script>
	</body>
</html>
