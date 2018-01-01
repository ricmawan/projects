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
						 <span style="width:50%;display:inline-block;">
							 <h5>Master Data User</h5>
						</span>
						<span style="width:49%;display:inline-block;text-align:right;">
							<button id="btnAdd" class="btn btn-primary menu" link="./Master/User/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
							<?php 
								if($DeleteFlag == true) echo '<button id="btnDelete" class="btn btn-danger" onclick="DeleteData(\'./Master/User/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>';
								echo '<input type="hidden" name="hdnEditFlag" id="hdnEditFlag" value="'.$EditFlag.'" />';
							?>
						</span>
					</div>
					<div class="panel-body">
						<div class="table-responsive" style="overflow-x:hidden;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th><input type="checkbox" id="select_all" name="select_all" onclick="chkAll();" /></th>
										<th>No</th>
										<th>Nama</th>
										<th>Username</th>
										<th>Status</th>
									</tr>
								</thead>
							</table>
						</div>
						<br />
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				keyFunction();
				var table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "330px",
								"rowId": "UserID",
								"scrollCollapse": true,
								"order": [2, "asc"],
								"columns": [
									{ "width": "20px", "orderable": false, className: "text-center" },
									{ "width": "25px", "orderable": false },
									null,
									null,
									null
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Master/User/DataSource.php",
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
								}
							});
				
				table.on( 'key', function (e, datatable, key, cell, originalEvent) {
					var data = datatable.row( cell.index().row ).data();
					if(key == 13) {
						if(($(".ui-dialog").css("display") == "none" || $("#delete-confirm").css("display") == "none") && $("#hdnEditFlag").val() == "1") Redirect("./Master/User/Detail.php?ID=" + data[5]);
					}
					else if(key == 46) {
						var DeleteID = new Array();
						$("input:checkbox[name=select]:checked").each(function() {
							if($(this).val() != 'all') DeleteID.push($(this).val());
						});
						if(DeleteID.length == 0) {
							table.keys.disable();
							var deletedData = new Array();
							deletedData.push(data[5] + "^" + data[2]);
							SingleDelete("./Master/User/Delete.php", deletedData);
							table.keys.enable();
						}
						else {
							$("#btnDelete").click();
						}
					}
				});
				
			});
		</script>
	</body>
</html>
