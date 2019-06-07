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
						 <h5>Pendaftaran</h5>
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
										<!--<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>-->
									</tr>
								</thead>
							</table>
						</div>
						<button class="btn btn-primary menu" link="./Transaction/Registration/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
						<button class="btn btn-danger" onclick="DeleteData('./Transaction/Registration/Delete.php');" ><i class="fa fa-close"></i> Batal</button>
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
							url: "./Transaction/Registration/DataSource.php",
							selection: true,
							multiSelect: true,
							rowSelect: true,
							keepSelection: true
							/*formatters: {
								"commands": function(column, row)
								{
									return "<i style='cursor:pointer;' data-row-id=\"" + row.MedicationID + "\" class=\"fa fa-edit\" data-link=\"./Transaction/Registration/Detail.php?ID=" + row.MedicationID + "\" acronym title=\"Ubah Data\"></i>&nbsp;";
								}
							}*/
						}); /*.on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered
							grid.find(".fa-edit").on("click", function(e)
							{
								Redirect($(this).data("link"));
							});
						});*/
				setInterval(function(){ $("#grid-data").bootgrid("reload") }, 900000);
			});
		</script>
	</body>
</html>
