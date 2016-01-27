<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			.custom-combobox {
				position: relative;
				display: inline-block;
				width: 100%;
			}
			.custom-combobox-input {
				margin: 0;
				padding: 5px 10px;
				display: block;
				width: 100%;
				height: 34px;
				padding: 6px 12px;
				font-size: 14px;
				line-height: 1.42857143;
				color: #555;
				background-color: #fff;
				background-image: none;
				border: 1px solid #ccc;
				border-radius: 4px;
				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
				-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
				transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			}
			.ui-autocomplete {
				font-family: Open Sans, sans-serif; 
				font-size: 14px;
			}
			.caret {
				display: inline-block;
				width: 0;
				height: 0;
				margin-left: 2px;
				vertical-align: middle;
				border-top: 4px solid;
				border-right: 4px solid transparent;
				border-left: 4px solid transparent;
				right: 10px;
				top: 50%;
				position: absolute;
			}
			.QTY {
				width: 40px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h2>Laporan Aset</h2>
					</div>
					<div class="panel-body">
						Grand Total: <span class="grandtotal"></span>
						<br />
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="CategoryName">Nama Kategori</th>
										<th data-column-id="ItemName">Nama Barang</th>
										<th data-column-id="UnitName">Nama Satuan</th>
										<th data-column-id="Stock">Stok</th>
										<th data-column-id="Price" data-align="right">Harga</th>
										<th data-column-id="Total" data-align="right">Total</th>
									</tr>
								</thead>
							</table>
						</div>
						<br />
						Grand Total: <span class="grandtotal"></span>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				$("#loading").show();
				$("#grid-data").bootgrid('destroy');
				$("#grid-data").bootgrid({
					ajax: true,
					rowCount: -1,
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
					responseHandler: function(response) {
						console.log(response);
						$(".grandtotal").html(response.GrandTotal);
						return response;
					},
					url: "Report/Assets/DataSource.php",
					selection: true,
					multiSelect: true,
					rowSelect: true,
					keepSelection: true
				});
				$("#dvTable").show();
				$("#loading").hide();
			});
		</script>
	</body>
</html>