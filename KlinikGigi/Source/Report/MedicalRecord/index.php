<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			.actionBar {
				display: none;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Rekam Medis</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Pasien :
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<select name="ddlPatient" id="ddlPatient" class="form-control-custom" placeholder="Pilih Pasien" >
										<option value="" ></option>
										<?php
											$sql = "SELECT PatientID, PatientName, PatientNumber FROM master_patient";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												echo "<option value='".$row['PatientID']."' >".$row['PatientNumber']." - ".$row['PatientName']."</option>";
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<br />
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-info" id="btnView" onclick="Preview();" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-success" id="btnExcel" onclick="ExportExcel();" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
						<br />
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric" >No</th>
										<th data-column-id="PatientName">Pasien</th>
										<th data-column-id="TransactionDate">Tanggal</th>
										<th data-column-id="ExaminationName">Pemeriksaan</th>
										<th data-column-id="Remarks">Keterangan</th>
									</tr>
								</thead>
							</table>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		<script>			
			function Preview() {
				var PatientID = $("#ddlPatient").val();
				var PassValidate = 1;
				var FirstFocus = 0;					
				if(PatientID == 0) {
					$("#ddlPatient").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
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
					$("#loading").show();
					$("#grid-data").bootgrid('destroy');
					$("#grid-data").bootgrid({
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
							//infos: "Menampilkan {{ctx.total}} data",
							loading: "Loading...",
							noResults: "Tidak Ada Data Yang Ditemukan!",
							refresh: "Refresh",
							search: "Cari"
						},
						url: "Report/MedicalRecord/DataSource.php?PatientID=" + PatientID,
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
				var PatientID = $("#ddlPatient").val();
				var PassValidate = 1;
				var FirstFocus = 0;	
				if(PatientID == 0) {
					$("#ddlPatient").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
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
					$("#loading").show();
					$("#excelDownload").attr("src", "Report/MedicalRecord/ExportExcel.php?PatientID=" + PatientID);
					$("#loading").hide();
				}
			}
			$(document).ready(function () {
				$("#ddlPatient").combobox();
				$("#ddlPatient").next().find("input").click(function() {
					$(this).val("");
				});
			});
		</script>
	</body>
</html>
