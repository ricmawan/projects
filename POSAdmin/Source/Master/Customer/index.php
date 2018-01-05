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
							 <h5>Master Data Pelanggan</h5>
						</span>
						<span style="width:49%;display:inline-block;text-align:right;">
							<button id="btnAdd" class="btn btn-primary" onclick="openDialog(0, 0);"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
							<?php
								if($DeleteFlag == true) echo '<button id="btnDelete" class="btn btn-danger" onclick="DeleteData(\'./Master/Customer/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>';
								echo '<input id="hdnEditFlag" name="hdnEditFlag" type="hidden" value="'.$EditFlag.'" />';
								echo '<input id="hdnDeleteFlag" name="hdnDeleteFlag" type="hidden" value="'.$DeleteFlag.'" />';
							?>
						</span>
					</div>
					<div class="panel-body">
						<div class="table-responsive" style="overflow-x:hidden;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th><input id="select_all" name="select_all" type="checkbox" onclick="chkAll();" /></th>
										<th>No</th>
										<th>Kode Pelanggan</th>
										<th>Nama Pelanggan</th>
										<th>Telepon</th>
										<th>Alamat</th>
										<th>Kota</th>
										<th>Keterangan</th>
									</tr>
								</thead>
							</table>
						</div>
						<br />
					</div>
				</div>
			</div>
		</div>
		<div id="FormData" title="Tambah Pelanggan" style="display: none;">
			<form class="col-md-12" id="PostForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-3 labelColumn">
						Kode Pelanggan :
						<input id="hdnCustomerID" name="hdnCustomerID" type="hidden" value=0 />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
					</div>
					<div class="col-md-7">
						<input id="txtCustomerCode" name="txtCustomerCode" type="text" tabindex=5 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Pelanggan" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Nama Pelanggan :
					</div>
					<div class="col-md-7">
						<input id="txtCustomerName" name="txtCustomerName" type="text" tabindex=6 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Nama Pelanggan" required />
					</div>
				</div>				
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Telepon :
					</div>
					<div class="col-md-7">
						<input id="txtTelephone" name="txtTelephone" type="text" tabindex=7 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Telepon" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Alamat :
					</div>
					<div class="col-md-7">
						<input id="txtAddress" name="txtAddress" type="text" tabindex=8 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Alamat" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Kota :
					</div>
					<div class="col-md-7">
						<input id="txtCity" name="txtCity" type="text" tabindex=9 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kota" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Keterangan :
					</div>
					<div class="col-md-7">
						<textarea id="txtRemarks" name="txtRemarks" tabindex=10 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Keterangan"></textarea>
					</div>
				</div>
				<br />
			</form>
		</div>
		<script>
			var table;
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#FormData").attr("title", "Edit Pelanggan");
					$("#hdnCustomerID").val(Data[8]);
					$("#txtCustomerCode").val(Data[2].toString());
					$("#txtCustomerName").val(Data[3].toString());
					$("#txtTelephone").val(Data[4].toString());
					$("#txtAddress").val(Data[5].toString());
					$("#txtCity").val(Data[6].toString());
					$("#txtRemarks").val(Data[7].toString());
				}
				else $("#FormData").attr("title", "Tambah Pelanggan");
				var index = table.cell({ focused: true }).index();
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
						table.keys.disable();
						$("#divModal").show();
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0 && $("textarea:focus").length == 0 && $("#btnOK:focus").length == 0) { //right arrow
								$("#btnCancelAddCustomer").focus();
							}
							else if(e.keyCode == 37 && $("input:focus").length == 0 && $("textarea:focus").length == 0 && $("#btnOK:focus").length == 0) { //left arrow
								$("#btnSaveCustomer").focus();
							}
						});
					},
					show: {
						effect: "fade",
						duration: 500
					},
					hide: {
						effect: "fade",
						duration: 500
					},
					close: function() {
						$(this).dialog("destroy");
						$("#divModal").hide();
						table.keys.enable();
						if(typeof index !== 'undefined') table.cell(index).focus();
						$("#hdnCustomerID").val(0);
						$("#txtCustomerCode").val("");
						$("#txtCustomerName").val("");
						$("#txtTelephone").val("");
						$("#txtAddress").val("");
						$("#txtCity").val("");
						$("#txtRemarks").val("");
					},
					resizable: false,
					height: 400,
					width: 550,
					modal: false,
					buttons: [
					{
						text: "Simpan",
						id: "btnSaveCustomer",
						tabindex: 11,
						click: function() {
							var Validation = ValidateForm();
							if(Validation == 1) {
								saveConfirm(function(action) {
									if(action == "Tidak") {
										$("#txtCustomerCode").focus();
									}
									else {
										$.ajax({
											url: "./Master/Customer/Insert.php",
											type: "POST",
											data: $("#PostForm").serialize(),
											dataType: "json",
											success: function(data) {
												if(data.FailedFlag == '0') {
													$("#loading").hide();
													$("#FormData").dialog("destroy");
													$("#divModal").hide();
													$("#hdnCustomerID").val(0);
													$("#txtCustomerCode").val("");
													$("#txtCustomerName").val("");
													$("#txtTelephone").val("");
													$("#txtAddress").val("");
													$("#txtCity").val("");
													$("#txtRemarks").val("");
													var counter = 0;
													Lobibox.alert("success",
													{
														msg: data.Message,
														width: 320,
														delay: 2000,
														beforeClose: function() {
															if(counter == 0) {
																table.keys.enable();
																counter = 1;
															}
														},
														shown: function() {
															setTimeout(function() {
																table.ajax.reload(function() {
																	table.keys.enable();
																	if(typeof index !== 'undefined') table.cell(index).focus();
																	table.keys.disable();
																}, false);
															}, 0);
														}
													});
												}
												else {
													$("#loading").hide();
													var counter = 0;
													Lobibox.alert("warning",
													{
														msg: data.Message,
														width: 320,
														delay: false,
														beforeClose: function() {
															if(counter == 0) {
																$("#txtCustomerCode").focus();
																counter = 1;
															}
														}
													});
													return 0;
												}
											},
											error: function(jqXHR, textStatus, errorThrown) {
												$("#loading").hide();
												var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
												LogEvent(errorMessage, "/Master/Customer/index.php");
												Lobibox.alert("error",
												{
													msg: errorMessage,
													width: 320,
													shown: function() {
														setTimeout(function() {
															$(".lobibox-footer").find('button:nth-child(1)').focus();
														}, 0);
													}
												});
												return 0;
											}
										});
									}
								});
							}
						}
					},
					{
						text: "Batal",
						id: "btnCancelAddCustomer",
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
							table.keys.enable();
							if(typeof index !== 'undefined') table.cell(index).focus();
							$("#hdnCustomerID").val(0);
							$("#txtCustomerCode").val("");
							$("#txtCustomerName").val("");
							$("#txtTelephone").val("");
							$("#txtAddress").val("");
							$("#txtCity").val("");
							$("#txtRemarks").val("");
							return false;
						}
					}]
				}).dialog("open");
			}

			$(document).ready(function() {
				keyFunction();
				enterLikeTab();
				var counterCustomer = 0;
				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "330px",
								"rowId": "CustomerID",
								"scrollCollapse": true,
								"order": [2, "asc"],
								"columns": [
									{ "width": "20px", "orderable": false, className: "text-center" },
									{ "width": "25px", "orderable": false },
									null,
									null,
									null,
									null,
									null,
									null
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Master/Customer/DataSource.php",
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
					var index = table.cell({ focused: true }).index();
					if(key == 32) { //space
						var checkbox = $(".focus").find("input:checkbox");
						if(checkbox.prop("checked") == true) {
							checkbox.prop("checked", false);
							checkbox.attr("checked", false);
						}
						else {
							checkbox.prop("checked", true);
							checkbox.attr("checked", true);
						}
					}
					else if(counterCustomer == 0) {
						counterCustomer = 1;
						var data = datatable.row( cell.index().row ).data();
						if(key == 13) { //enter
							if(($(".ui-dialog").css("display") == "none" || $("#delete-confirm").css("display") == "none") && $("#hdnEditFlag").val() == "1") {
								openDialog(data, 1);
							}
						}
						else if(key == 46 && $("#hdnDeleteFlag").val() == "1") { //delete
							var DeleteID = new Array();
							$("input:checkbox[name=select]:checked").each(function() {
								if($(this).val() != 'all') DeleteID.push($(this).val());
							});
							if(DeleteID.length == 0) {
								table.keys.disable();
								var deletedData = new Array();
								deletedData.push(data[8] + "^" + data[3]);
								SingleDelete("./Master/Customer/Delete.php", deletedData, function(action) {
									if(action == "Cancel") table.keys.enable();
									else {
										table.ajax.reload(function() {
											table.keys.enable();
											if(typeof index !== 'undefined') {
												try {
													table.cell(index).focus();
												}
												catch (err) {
													$("#grid-data").DataTable().cell( ':eq(0)' ).focus();
												}
											}
										}, false);
									}
								});
							}
							else {
								table.keys.disable();
								DeleteData("./Master/Customer/Delete.php", function(action) {
									if(action == "Cancel") table.keys.enable();
									else {
										$("#select_all").prop("checked", false);
										table.ajax.reload(function() {
											table.keys.enable();
											if(typeof index !== 'undefined') {
												try {
													table.cell(index).focus();
												}
												catch (err) {
													$("#grid-data").DataTable().cell( ':eq(0)' ).focus();
												}
											}
										}, false);
									}
								});
							}
						}
						setTimeout(function() { counterCustomer = 0; } , 1000);
					}
				});
				
				table.on('page', function() {
					$("#select_all").prop("checked", false);
				});
				
				$(document).on("keydown", function (evt) {
					var index = table.cell({ focused: true }).index();
					if (evt.keyCode == 46 && $("#hdnDeleteFlag").val() == "1" && typeof index == 'undefined') { //delete button
						evt.preventDefault();
						table.keys.disable();
						DeleteData("./Master/Customer/Delete.php", function(action) {
							if(action == "Cancel") table.keys.enable();
							else {
								$("#select_all").prop("checked", false);
								table.ajax.reload(function() {
									table.keys.enable();
									if(typeof index !== 'undefined') {
										try {
											table.cell(index).focus();
										}
										catch (err) {
											$("#grid-data").DataTable().cell( ':eq(0)' ).focus();
										}
									}
								}, false);
							}
						});
					}
				});
			});
		</script>
	</body>
</html>
