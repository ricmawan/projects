<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../DBConfig.php";
	include "../GetSession.php";
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading" style="padding: 1px 15px;">
						<h5>Notifikasi</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive" style="overflow-x:hidden;" id="divStock" >
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th>No</th>
										<th>Cabang</th>
										<th>Kode</th>
										<th>Nama</th>
										<th>Kategori</th>
										<th>Min Stok</th>
										<th>Stok</th>
										<th>Stok Fisik</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="table-responsive" style="overflow-x:hidden;" id="divDebt" >
							<table id="grid-debt" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th>No</th>
										<th>No. Invoice</th>
										<th>Tanggal</th>
										<th>Jatuh Tempo</th>
										<th>Supplier</th>
										<th>Total</th>
										<th>Pembayaran</th>
										<th>Kekurangan</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="divNotification" style="display:none;">
			<select id="ddlNotification" name="ddlNotification" class="form-control-custom" onchange="notificationChange(this.value);">
				<option value=1>Stok Minimal</option>
				<option value=2>Hutang Jatuh Tempo</option>
			</select>
		</div>
		<script>
			function notificationChange(notificationID) {
				$("div.toolbar").find("select").val(notificationID);
				if(notificationID == 1) {
					$("#divDebt").hide();
					$("#divStock").show();
					table.columns.adjust().draw();
				}
				else {
					$("#divDebt").show();
					$("#divStock").hide();
					table2.columns.adjust().draw();
				}
			}

			var table;
			var table2;
			var waitForFinalEvent = (function () {
		        var timers = {};
		        return function (callback, ms, uniqueId) {
		            if (!uniqueId) {
		                uniqueId = "Don't call this twice without a uniqueId";
		            }
		            if (timers[uniqueId]) {
		                clearTimeout(timers[uniqueId]);
		            }
		            timers[uniqueId] = setTimeout(callback, ms);
		        };
		    })();
			
			$(document).ready(function() {
				$( window ).resize(function() {
					waitForFinalEvent(function () {
		               setTimeout(function() {
							table.columns.adjust().draw();
						}, 0);
		            }, 500, "resizeWindow");
				});
				
				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Notification/index.php");
					Lobibox.alert("error",
					{
						msg: "Terjadi kesalahan. Memuat ulang halaman.",
						width: 480,
						delay: 2000,
						beforeClose: function() {
							if(counterError == 0) {
								//location.reload();
								counterError = 1;
							}
						}
					});
				};
				
				var counterItem = 0;
				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollX":  false,
								"scrollY": "330px",
								"rowId": "ItemID",
								"scrollCollapse": true,
								"order": [],
								"columns": [
									{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center dt-body-right", "orderable": false },
									{ className: "dt-head-center dt-body-right", "orderable": false }	
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Notification/DataSource.php",
								"language": {
									"info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
									"infoFiltered": "",
									"infoEmpty": "",
									"zeroRecords": "Data tidak ditemukan",
									"lengthMenu": "&nbsp;&nbsp;_MENU_ data",
									"search": "Cari",
									"processing": "Memproses",
									"paginate": {
										"next": ">",
										"previous": "<",
										"last": "»",
										"first": "«"
									}
								},
								"sDom": '<"toolbar">frtip'
						});

				table2 = $("#grid-debt").DataTable({
								"keys": true,
								"scrollX":  false,
								"scrollY": "330px",
								"rowId": "ItemID",
								"scrollCollapse": true,
								"order": [],
								"columns": [
									{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center dt-body-right", "orderable": false },
									{ className: "dt-head-center dt-body-right", "orderable": false }	
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Notification/DebtDataSource.php",
								"language": {
									"info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
									"infoFiltered": "",
									"infoEmpty": "",
									"zeroRecords": "Data tidak ditemukan",
									"lengthMenu": "&nbsp;&nbsp;_MENU_ data",
									"search": "Cari",
									"processing": "Memproses",
									"paginate": {
										"next": ">",
										"previous": "<",
										"last": "»",
										"first": "«"
									}
								},
								"sDom": '<"toolbar">frtip'
						});

				$(".toolbar").css({
					"display" : "inline-block"
				});

				$("div.toolbar").html($("#divNotification").html());
				$("#divDebt").hide();

				//$("div.toolbar").find("select").val($("#ddlBranch").val());
				
				var counterKeyItem = 0;
				$(document).on("keydown", function (evt) {
					if(counterKeyItem == 0) {
						counterKeyItem = 1;
						var index = table.cell({ focused: true }).index();
						if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0 && $("#FormData").css("display") == "none" && $("#delete-confirm").css("display") == "none") {
							$("#grid-data_wrapper").find("input[type='search']").focus();
						}
					}
					setTimeout(function() { counterKeyItem = 0; } , 1000);
				});
			});
		</script>
	</body>
</html>
