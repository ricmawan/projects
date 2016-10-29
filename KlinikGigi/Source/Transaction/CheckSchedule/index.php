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
						 <h5>Jadwal Periksa Hari Ini</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="PatientIDName" data-visible="false" data-type="numeric" data-identifier="true">PatientID</th>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="PatientNumber">ID Pasien</th>
										<th data-column-id="PatientName">Nama Pasien</th>
										<th data-column-id="BirthDate">Tanggal Lahir</th>
										<th data-column-id="Address">Alamat</th>
										<th data-column-id="City">Kota</th>
										<th data-column-id="Telephone">Telepon</th>
										<th data-column-id="Allergy">Alergi</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
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
								url: "./Transaction/CheckSchedule/DataSource.php",
								selection: false,
								multiSelect: true,
								rowSelect: true,
								keepSelection: true
							});
			});
		</script>
	</body>
</html>
