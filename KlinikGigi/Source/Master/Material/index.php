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
						 <h5>Material</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="MaterialIDName" data-visible="false" data-type="numeric" data-identifier="true">MaterialID</th>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="MaterialName">Nama</th>
										<th data-column-id="SalePrice" data-align="right">Harga</th>
										<?php if($EditFlag == true) echo '<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>'; ?>
									</tr>
								</thead>
							</table>
						</div>
						<button class="btn btn-primary menu" link="./Master/Material/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
						<?php if($DeleteFlag == true) echo '<button class="btn btn-danger" onclick="DeleteData(\'./Master/Material/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>'; ?>
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
							url: "./Master/Material/DataSource.php",
							selection: true,
							multiSelect: true,
							rowSelect: true,
							keepSelection: true,
							formatters: {
								"commands": function(column, row)
								{
									return "<i style='cursor:pointer;' data-row-id=\"" + row.MaterialID + "\" class=\"fa fa-edit\" data-link=\"./Master/Material/Detail.php?ID=" + row.MaterialID + "\" acronym title=\"Ubah Data\"></i>&nbsp;";
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							grid.find(".fa-edit").on("click", function(e)
							{
								Redirect($(this).data("link"));
							});
						});
			});
		</script>
	</body>
</html>
