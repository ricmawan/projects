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
						 <h5>Barang Keluar</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
						<input id="hdnEditFlag" name="hdnEditFlag" type="hidden" <?php echo 'value='.$EditFlag; ?> />
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="OutgoingIDNo" data-type="numeric" data-visible="false" data-identifier="true">ID Transaksi</th>
										<th data-column-id="OutgoingNumber">No Nota</th>
										<th data-column-id="TransactionDate" data-type="numeric">Tanggal</th>
										<th data-column-id="CustomerName">Nama Pelanggan</th>
										<!--<th data-column-id="SalesName">Nama Sales</th>-->
										<th data-column-id="DeliveryCost" data-align="right">Ongkos Kirim</th>
										<!--<th data-column-id="SubTotal" data-align="right">Sub Total</th>-->
										<th data-column-id="Total" data-align="right">Total</th>
										<th data-column-id="Remarks">Catatan</th>
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
										<?php //if($EditFlag == true) echo '<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>'; ?>
									</tr>
								</thead>
							</table>
						</div>
						<button class="btn btn-primary menu" link="./Transaction/Outgoing/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
						<?php if($DeleteFlag == true) echo '<button class="btn btn-danger" onclick="DeleteData(\'./Transaction/Outgoing/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>'; ?>
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
							url: "./Transaction/Outgoing/DataSource.php",
							selection: true,
							multiSelect: true,
							rowSelect: true,
							keepSelection: true,
							formatters: {
								"commands": function(column, row)
								{
									var test;
									if($("#hdnEditFlag").val() == true) {
										test = "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" class=\"fa fa-edit\" data-link=\"./Transaction/Outgoing/Detail.php?ID=" + row.OutgoingID + "\" acronym title=\"Ubah Data\"></i>&nbsp;&nbsp;&nbsp;<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" class=\"fa fa-print\" data-link=\"./Transaction/Outgoing/Detail.php?ID=" + row.OutgoingID + "\" acronym title=\"Cetak Nota\"></i>&nbsp;&nbsp;&nbsp;<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" class=\"fa fa-truck\" data-link=\"./Transaction/Outgoing/Detail.php?ID=" + row.OutgoingID + "\" acronym title=\"Cetak Surat Jalan\"></i>&nbsp;";
									}
									else {
										test = "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" class=\"fa fa-print\" data-link=\"./Transaction/Outgoing/Detail.php?ID=" + row.OutgoingID + "\" acronym title=\"Cetak Nota\"></i>&nbsp;&nbsp;&nbsp;<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" class=\"fa fa-truck\" data-link=\"./Transaction/Outgoing/Detail.php?ID=" + row.OutgoingID + "\" acronym title=\"Cetak Surat Jalan\"></i>&nbsp;";
									}
									return test;
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
