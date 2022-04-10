<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
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
						 <h5>Backup Database</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric" data-identifier="true">No</th>
										<th data-column-id="BackupDate">Tanggal</th>
										<th data-column-id="FileName" data-formatter="commands">Nama File</th>
									</tr>
								</thead>
							</table>
						</div>
						<br />
						<button class="btn btn-primary" onclick="DoBackUp();"><i class="fa fa-download"></i> Backup</button>&nbsp;
					</div>
				</div>
			</div>
		</div>
		<script>
			function DoBackUp() {
				$("#loading").show();
				$.ajax({
					url: "./Tools/BackupDB/Backup.php",
					type: "POST",
					data: "",
					dataType: "json",
					success: function(data) {
						if(data.FailedFlag == '0') {
							$.notify(data.Message, "success");
							$("html, body").animate({
								scrollTop: 0
							}, "slow");
							$("#grid-data").bootgrid('reload');
							$("#loading").hide();
						}
						else {
							$("#loading").hide();
							$.notify(data.Message, "error");
						}
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Koneksi gagal", "error");
				
					}
				});
			}
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
							url: "./Tools/BackupDB/DataSource.php",
							selection: true,
							multiSelect: false,
							rowSelect: true,
							keepSelection: true,
							formatters: {
								"commands": function(column, row)
								{
									return "<a href=\"" + row.FilePath + "\" download >" + row.FileName + "</a>";
								}
							}
						});
			});
		</script>
	</body>
</html>
