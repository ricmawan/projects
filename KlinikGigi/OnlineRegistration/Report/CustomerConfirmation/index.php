<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../DBConfig.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<style>
			.actionBar {
				display: none;
			}
			th[data-column-id="TransactionDate"] {
			    width: 85px !important;
			}

			th[data-column-id="ReceivedDate"] {
			    width: 140px !important;
			}

			th[data-column-id="IncomingReceiptNumber"] {
			    width: 140px !important;
			}
		</style>
		<link href="../../assets/css/bootstrap.css" rel="stylesheet" />
		<link href="../../assets/css/font-awesome.css" rel="stylesheet" />
		<link href="../../assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
		<link href="../../assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
		<link href="../../assets/css/custom.css" rel="stylesheet" />
		<link href="../../assets/css/jquery.bootgrid.css" rel="stylesheet" />
	</head>
	<body style="overflow: hidden !important;" >
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Konfirmasi Pendaftaran Pelanggan</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Tanggal :
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtFromDate" name="txtFromDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Dari Tanggal" />
								</div>
							</div>
							<div style="float:left;" class="labelColumn">
								-
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtToDate" name="txtToDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Sampai Tanggal" />
								</div>
							</div>
						</div>
						<br />
						<div class="row" >
							<div class="col-md-1 labelColumn">
								Status :
							</div>
							<div class="col-md-2">
								<select id="ddlStatus" name="ddlStatus" class="form-control-custom" >
									<option value='A'>Semua</option>
									<option value='Y'>Hadir</option>
									<option value='N'>Batal</option>
									<option value=''>Tidak Konfirmasi</option>
								</select>
							</div>
						</div>
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
										<th data-column-id="BranchName" >Cabang</th>
										<th data-column-id="DoctorName" >Dokter</th>
										<th data-column-id="PatientName" >Pasien</th>
										<th data-column-id="ScheduledDate" >Tanggal</th>
										<th data-column-id="CustomerConfirmation" >Konfirmasi</th>
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
		</div>
		<iframe id='excelDownload' src='' style='display:none'></iframe>
		<script src="../../assets/js/jquery-1.10.2.js"></script>
		<script src="../../assets/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="../../assets/js/bootstrap.min.js"></script>
		<script src="../../assets/js/jquery.metisMenu.js"></script>
		<script src="../../assets/js/custom.js"></script>
		<script src="../../assets/js/notify.js"></script>
		<script src="../../assets/js/global.js"></script>
		<script src="../../assets/js/jquery.bootgrid.js"></script>
		<script type="text/javascript" src="../../assets/js/jquery.fancybox.js"></script>
		<script>
			function Preview() {
				var Status = $("#ddlStatus").val();
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var PassValidate = 1;
				$("#loading").show();
				$("#grid-data").bootgrid('destroy');
				$("#grid-data").bootgrid({
					ajax: true,
					rowCount: -1,
					sorting: false,
					columnSelection: false,
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
					css: {
						iconRefresh: "none"
					},
					labels: {
						all: "Semua Data",
						//infos: "Menampilkan {{ctx.start}} sampai {{ctx.end}} dari {{ctx.total}} data",
						infos: "Menampilkan {{ctx.total}} data",
						loading: "Loading...",
						noResults: "Tidak Ada Data Yang Ditemukan!",
						refresh: "Refresh",
						search: "Cari"
					},
					url: "./DataSource.php?Status=" + Status + "&txtFromDate=" + txtFromDate + "&txtToDate=" + txtToDate,
					selection: false,
					multiSelect: true,
					rowSelect: true,
					keepSelection: true
				});
				$("#dvTable").show();
				$("#loading").hide();
			}
			function ExportExcel() {
				var IsReceived = $("#ddlIsReceived").val();
				$("#loading").show();
				$("#excelDownload").attr("src", "./ExportExcel.php?Status=" + Status + "&txtFromDate=" + txtFromDate + "&txtToDate=" + txtToDate);
				$("#loading").hide();
			}
		</script>
	</body>
</html>