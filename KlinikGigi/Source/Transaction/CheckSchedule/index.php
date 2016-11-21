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
						 <h5>Jadwal Periksa</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Tanggal :
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtFromDate" onchange="LoadData();" name="txtFromDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Dari Tanggal" />
								</div>
							</div>
							<div style="float:left;" class="labelColumn">
								-
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtToDate" onchange="LoadData();" name="txtToDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Sampai Tanggal" />
								</div>
							</div>
						</div>
						<div class="table-responsive">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="ScheduledDate">Jadwal Periksa</th>
										<th data-column-id="PatientNumber">ID Pasien</th>
										<th data-column-id="PatientName">Nama Pasien</th>
										<th data-column-id="BirthDate">Tanggal Lahir</th>
										<th data-column-id="Address">Alamat</th>
										<th data-column-id="City">Kota</th>
										<th data-column-id="Telephone">Telepon</th>
									</tr>
								</thead>
							</table>
						</div>
						<button class="btn btn-primary menu" link="./Transaction/CheckSchedule/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
					</div>
				</div>
			</div>
		</div>
		<script>
			function LoadData() {
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(PassValidate == 1) {
					if(txtFromDate != "" && txtToDate != "") {
						var FromDate = txtFromDate.split("-");
						FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
						var ToDate = txtToDate.split("-");
						ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
						if(FromDate > ToDate) {
							$("#txtToDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							PassValidate = 0;
							if(FirstFocus == 0) $("#txtToDate").focus();
							FirstFocus = 1;
						}
					}
				}
				
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					$("#grid-data").bootgrid("destroy");
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
						url: "./Transaction/CheckSchedule/DataSource.php?Filter=1&txtFromDate=" + txtFromDate + "&txtToDate=" + txtToDate,
						selection: false,
						multiSelect: true,
						rowSelect: true,
						keepSelection: true
					});
				}
			}
			$(document).ready(function() {
				$("#txtFromDate").datepicker({
					dateFormat: 'DD, dd-mm-yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu" ]
				});
				$("#txtFromDate").attr("readonly", "true");
				$("#txtFromDate").css({
					"background-color": "#FFF",
					"cursor": "text"
				});
				
				$("#txtToDate").datepicker({
					dateFormat: 'DD, dd-mm-yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu" ]
				});
				$("#txtToDate").attr("readonly", "true");
				$("#txtToDate").css({
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
								url: "./Transaction/CheckSchedule/DataSource.php?Filter=0",
								selection: false,
								multiSelect: true,
								rowSelect: true,
								keepSelection: true
							});
			});
		</script>
	</body>
</html>
