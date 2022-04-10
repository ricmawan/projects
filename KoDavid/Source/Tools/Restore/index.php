<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<script src="assets/js/jquery.form.js"></script>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Restore Dabase</h5>
					</div>
					<div class="panel-body">
						<form id="PostForm" method="POST" enctype="multipart/form-data" action="./Tools/RestoreDB/Restore.php" >
							<div id="output" style="display:none;"></div>
							<input type="radio" id="RestoreMethod1" style="display:none;" name="RestoreMethod" value=1 checked> <!--Pilih Dari Riwayat Backup:-->
							<div class="table-responsive">
								<table id="grid-data" class="table table-striped table-bordered table-hover" >
									<thead>				
										<tr>
											<th data-column-id="RowNumber" data-sortable="false" data-type="numeric" >No</th>
											<th data-column-id="BackupDate">Tanggal</th>
											<th data-column-id="FileName" data-identifier="true">Nama File</th>
										</tr>
									</thead>
								</table>
							</div>
							<br />
							<!--<div class="row">
								<div class="col-md-2">
									<input type="radio" id="RestoreMethod2" name="RestoreMethod" value=2> atau upload:
								</div>
								<div class="col-md-3">
									<input type="file" class="form-control" name="uploadfile" id="uploadfile" accept=".sql, application/sql" disabled />
								</div>
							</div>-->
						</form>
						<br />
						<button class="btn btn-primary" onclick="Restore();"><i class="fa fa-download"></i> Restore</button>&nbsp;
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
							url: "./Tools/RestoreDB/DataSource.php",
							selection: true,
							multiSelect: false,
							rowSelect: true,
							keepSelection: true
						});
				
				/*$("input:radio[name=RestoreMethod]").change(function() {
					if($("#RestoreMethod1").prop("checked")) {
						$("#uploadfile").attr("disabled", true);
						$("input:checkbox[name=select]").prop("disabled", false);
						$("input:checkbox[name=select]").attr("disabled", false);
					}
					else {
						$("#uploadfile").attr("disabled", false);
						$("input:checkbox[name=select]").prop("disabled", true);
						$("input:checkbox[name=select]").attr("disabled", true);
					}
				});
				
				var options = { 
					target: "#output",   // target element(s) to be updated with server response 
					success: afterSuccess,  // post-submit callback 
					resetForm: false// reset the form after successful submit 
				};
				$("#inputProgress").submit(function() { 
					$(this).ajaxSubmit(options);  			
					// always return false to prevent standard browser submit and page navigation 
					return false; 
				}); 
				function afterSuccess()
				{
					$("#loading").hide();
					var response = $("#output").html();
					var detail = response.split("|");
					if(detail[3] == "0") {
						$.notify(detail[1], "success");
					}
					else $.notify(detail[1], "warn");
				}*/
			});
			
			function Restore() {
				//console.log($("#grid-data").bootgrid("getSelectedRows")[0]);
				var ask=confirm("Apakah anda yakin ingin mengembalikan data dari file yang dipilih?");
				if(ask==true) {
					$("#loading").show();
					/*form = $("#PostForm");
					form.submit();*/
					$.ajax({
						url: "./Tools/RestoreDB/Restore.php",
						type: "POST",
						data: $("#PostForm").serialize(),
						dataType: "json",
						success: function(data) {
							if(data.FailedFlag == '0') {
								$("#loading").hide();
								$.notify(data.Message, "success");
							}
							else {
								$("#loading").hide();
								$.notify(data.Message, "error");					
							}
						},
						error: function(data) {
							$("#loading").hide();
							$.notify("Terjadi kesalahan sistem!", "error");
						}
					});
				}
			}
		</script>
	</body>
</html>
