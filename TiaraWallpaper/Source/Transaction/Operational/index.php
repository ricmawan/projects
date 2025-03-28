<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			th[data-column-id="Opsi"] {
				width: 75px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Operasional</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<input id="hdnEditFlag" name="hdnEditFlag" type="hidden" <?php echo 'value='.$EditFlag; ?> />
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="OperationalID" data-type="numeric" data-visible="false" data-identifier="true">ID Transaksi</th>
										<th data-column-id="TransactionDate" data-type="numeric">Tanggal</th>
										<th data-column-id="Total" data-align="right">Total</th>
										<th data-column-id="Remarks">Catatan</th>
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
									</tr>
								</thead>
							</table>
						</div>
						<button class="btn btn-primary menu" link="./Transaction/Operational/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
						<?php if($DeleteFlag == true) echo '<button class="btn btn-danger" onclick="DeleteData(\'./Transaction/Operational/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>'; ?>
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
							url: "./Transaction/Operational/DataSource.php",
							selection: true,
							multiSelect: true,
							rowSelect: true,
							keepSelection: true,
							formatters: {
								"commands": function(column, row)
								{
									var option;
									option = "<i style='cursor:pointer;' data-row-id=\"" + row.OperationalID + "\" class=\"fa fa-edit\" data-link=\"./Transaction/Operational/Detail.php?ID=" + row.OperationalID + "\" acronym title=\"Ubah Data\"></i>";
									return option;
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