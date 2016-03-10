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
						 <h5>Master Data Barang</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="BrandName">Merek</th>
										<th data-column-id="TypeName">Tipe</th>
										<th data-column-id="UnitName">Nama Satuan</th>
										<th data-column-id="BatchNumber">Batch</th>
										<th data-column-id="Stock">Stok</th>
										<th data-column-id="BuyPrice" data-align="right">Harga Beli</th>
										<th data-column-id="SalePrice" data-align="right">Harga Jual</th>
									</tr>
								</thead>
							</table>
						</div>
						<!--<button class="btn btn-primary menu" link="./Master/Type/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
						<?php if($DeleteFlag == true) echo '<button class="btn btn-danger" onclick="DeleteData(\'./Master/Type/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>'; ?>-->
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
							url: "./Master/Item/DataSource.php",
							selection: true,
							multiSelect: false,
							rowSelect: true,
							keepSelection: true
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
