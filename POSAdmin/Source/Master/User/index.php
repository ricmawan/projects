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
							<button id="btnAdd" class="btn btn-primary" onclick="openDialog(0, 0);"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
							<?php 
								if($DeleteFlag == true) echo '<button id="btnDelete" class="btn btn-danger" onclick="fnDeleteData();" ><i class="fa fa-close"></i> Hapus</button>';
								echo '<input id="hdnEditFlag" name="hdnEditFlag" type="hidden" value="'.$EditFlag.'" />';
								echo '<input id="hdnDeleteFlag" name="hdnDeleteFlag" type="hidden" value="'.$DeleteFlag.'" />';
								echo '<input id="hdnSessionUserID" name="hdnSessionUserID" type="hidden" value="'.$_SESSION['UserID'].'" />';
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
										<th>Tipe User</th>
										<th>Status</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="FormData" title="Tambah Item" style="display: none;">
			<form class="col-md-12" id="PostForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-2 labelColumn">
						Nama :
						<input id="hdnUserID" name="hdnUserID" type="hidden" value=0 />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
					</div>
					<div class="col-md-3">
						<input id="txtUserName" name="txtUserName" type="text" tabindex=1 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Nama " required />
					</div>
					<div class="col-md-3 labelColumn">
						Username :
					</div>
					<div class="col-md-3">
						<input id="txtUserLogin" name="txtUserLogin" type="text" tabindex=2 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Username" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-2 labelColumn">
						Password :
					</div>
					<div class="col-md-3">
						<input id="txtPassword" name="txtPassword" type="password" tabindex=3 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Password" />
					</div>
					<div class="col-md-3 labelColumn">
						Konfirmasi Password:
					</div>
					<div class="col-md-3">
						<input id="txtConfirmPassword" name="txtConfirmPassword" type="password" tabindex=4 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Konfirmasi Password" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-2 labelColumn">
						Tipe User :
					</div>
					<div class="col-md-3">
						<select id="ddlUserType" name="ddlUserType" tabindex=5 class="form-control-custom"  >
							<?php
								$sql = "CALL spSelDDLUserType('".$_SESSION['UserLogin']."')";
								if (! $result = mysqli_query($dbh, $sql)) {
									logEvent(mysqli_error($dbh), '/Master/User/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
									return 0;
								}
								while($row = mysqli_fetch_array($result)) {
									echo "<option value='".$row['UserTypeID']."' >".$row['UserTypeName']."</option>";
								}
								
								mysqli_free_result($result);
								mysqli_next_result($dbh);
							?>
						</select>
					</div>
					<div class="col-md-3 labelColumn">
						Status :
					</div>
					<div class="col-md-3">
						<select id="ddlStatus" name="ddlStatus" tabindex=6 class="form-control-custom"  >
							<option value=1 >Aktif</option>
							<option value=0 >Tidak Aktif</option>
						</select>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default" style="max-height: 340px;min-height: 340px;" >
							<div class="panel-heading" style="padding: 5px 15px;" >
								<h5>Pilih Hak Akses Menu</h5>
							</div>
							<div class="panel-body" style="min-height: 280px !important;max-height:280px!important;overflow-y:auto;">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>No</th>
												<th>Nama Menu</th>
												<th>Lihat</th>
												<th>Ubah</th>
												<th>Hapus</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sql = "CALL spSelMenu('".$_SESSION['UserLogin']."')";
							
												if (!$result = mysqli_query($dbh, $sql)) {
													logEvent(mysqli_error($dbh), '/Home.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
													return 0;
												}
												
												$PrevGroupMenuID = 0;
												$RowNumber = 1;
												$tabindex = 7;
												while($row = mysqli_fetch_array($result)) {
													if($PrevGroupMenuID == $row['GroupMenuID']) {
														$RowNumber++;
														echo "
															<tr>
																<td>$RowNumber.</td>
																<td>".$row['MenuName']."</td>
																<td style='text-align:center;'><input class='g".$row['GroupMenuID']."' id='".$row['MenuID']."' name='permission' type='checkbox' value='2' tabindex=".$tabindex." /></td>
																<td style='text-align:center;'><input class='ge".$row['GroupMenuID']."' id='e".$row['MenuID']."' name='edit' type='checkbox' value='true' tabindex=".($tabindex + 1)." /></td>
																<td style='text-align:center;'><input class='gd".$row['GroupMenuID']."' id='d".$row['MenuID']."' name='delete' type='checkbox' value='true' tabindex=".($tabindex + 2)." /></td>
															</tr>";
															
														$tabindex += 3;
													}
													else if($PrevGroupMenuID <> $row['GroupMenuID']) {
														$RowNumber = 1;
														//Group menu
														echo "
															<tr>
																<td></td>
																<td ><b><u><i>".$row['GroupMenuName']."</i></u></b></td>
																<td style='text-align:center;'><input id='g".$row['GroupMenuID']."' name='grouppermission' type='checkbox' tabindex=".$tabindex." /></td>
																<td style='text-align:center;'><input id='ge".$row['GroupMenuID']."' name='groupedit' type='checkbox' value='true' tabindex=".($tabindex + 1)." /></td>
																<td style='text-align:center;'><input id='gd".$row['GroupMenuID']."' name='groupdelete' type='checkbox' value='true' tabindex=".($tabindex + 2)." /></td>
															</tr>";
														$tabindex += 3;
														
														echo "
															<tr>
																<td>$RowNumber.</td>
																<td>".$row['MenuName']."</td>
																<td style='text-align:center;'><input class='g".$row['GroupMenuID']."' id='".$row['MenuID']."' name='permission' type='checkbox' value='2' tabindex=".$tabindex." /></td>
																<td style='text-align:center;'><input class='ge".$row['GroupMenuID']."' id='e".$row['MenuID']."' name='edit' type='checkbox' value='true' tabindex=".($tabindex + 1)." /></td>
																<td style='text-align:center;'><input class='gd".$row['GroupMenuID']."' id='d".$row['MenuID']."' name='delete' type='checkbox' value='true' tabindex=".($tabindex + 2)." /></td>
															</tr>";
															
														$tabindex += 3;
													}
													$PrevGroupMenuID = $row['GroupMenuID'];
												}
												mysqli_free_result($result);
												mysqli_next_result($dbh);
												echo '<input id="hdnTabIndex" name="hdnTabIndex" type="hidden" value="'.$tabindex.'" />';
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input id="hdnMenuID" name="hdnMenuID" type="hidden" />
				<input id="hdnEditMenuID" name="hdnEditMenuID" type="hidden" />
				<input id="hdnDeleteMenuID" name="hdnDeleteMenuID" type="hidden" />
			</form>
		</div>
		<script>
			var table;
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#FormData").attr("title", "Edit User");
					$("#hdnUserID").val(Data[6].toString());
					$("#txtUserName").val(Data[2]);
					$("#txtUserLogin").val(Data[3]);
					$("#ddlStatus").val(Data[10]);
					$("#ddlUserType").val(Data[11]);
					$("#hdnMenuID").val(Data[7].toString());
					$("#hdnEditMenuID").val(Data[8].toString());
					$("#hdnDeleteMenuID").val(Data[9].toString());
					
					var MenuID = $("#hdnMenuID").val().split(", ");
					var EditMenuID = $("#hdnEditMenuID").val().split(", ");
					var DeleteMenuID = $("#hdnDeleteMenuID").val().split(", ");
					
					for (var i = 0; i < MenuID.length; i++) {
						var MenuIDSelected = MenuID[i];
						var EditMenuIDSelected = EditMenuID[i];
						var DeleteMenuIDSelected = DeleteMenuID[i];
						if(EditMenuIDSelected == true) {
							$("#e" + MenuIDSelected).attr("checked", true);
							$("#e" + MenuIDSelected).prop("checked", true);
						}
						if(DeleteMenuIDSelected == true) {
							$("#d" + MenuIDSelected).attr("checked", true);
							$("#d" + MenuIDSelected).prop("checked", true);
						}
						$("#" + MenuIDSelected).attr("checked", true);
						$("#" + MenuIDSelected).prop("checked", true);
					}
					
					$("input:checkbox[name=permission]").each(function() {
						if($(this).prop('checked')) {
							$("#e" + $(this).attr("id")).removeAttr("disabled");
							$("#d" + $(this).attr("id")).removeAttr("disabled");
							$("#ge" + $(this).attr("id")).removeAttr("disabled");
							$("#gd" + $(this).attr("id")).removeAttr("disabled");
						}
						else {
							$("#e" + $(this).attr("id")).attr("disabled", true);
							$("#d" + $(this).attr("id")).attr("disabled", true);
							$("#ge" + $(this).attr("id")).attr("disabled", true);
							$("#gd" + $(this).attr("id")).attr("disabled", true);
						}
					});
				}
				else $("#FormData").attr("title", "Tambah User");
				var index = table.cell({ focused: true }).index();
				var btnIndex = $("#hdnTabIndex").val();
				//console.log(index);
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
						table.keys.disable();
						$("#divModal").show();
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0 && $("select:focus").length == 0) { //right arrow
								$("#btnCancelAddUser").focus();
							}
							else if(e.keyCode == 37 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0 && $("select:focus").length == 0) { //left arrow
								$("#btnSaveUser").focus();
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
						resetForm();
					},
					resizable: false,
					height: 600,
					width: 900,
					modal: false,
					buttons: [
					{
						text: "Simpan",
						id: "btnSaveUser",
						tabindex: btnIndex,
						click: function() {
							var PassValidate = ValidateForm();
							if(PassValidate == 1) {
								saveConfirm(function(action) {
									if(action == "Ya") {
										$.ajax({
											url: "./Master/User/Insert.php",
											type: "POST",
											data: $("#PostForm").serialize(),
											dataType: "json",
											success: function(data) {
												if(data.FailedFlag == '0') {
													$("#loading").hide();
													$("#FormData").dialog("destroy");
													$("#divModal").hide();
													var hdnUserID  = $("#hdnUserID").val();
													resetForm();
													var counter = 0;
													Lobibox.alert("success",
													{
														msg: data.Message,
														width: 480,
														delay: 2000,
														beforeClose: function() {
															if(counter == 0) {
																if(hdnUserID == $("#hdnSessionUserID").val()) location.reload();
																else table.keys.enable();
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
														width: 480,
														delay: false,
														beforeClose: function() {
															if(counter == 0) {
																setTimeout(function() {
																	$("#txtUserName").focus();
																}, 0);
																counter = 1;
															}
														}
													});
													return 0;
												}
											},
											error: function(jqXHR, textStatus, errorThrown) {
												$("#loading").hide();
												var counter = 0;
												var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
												LogEvent(errorMessage, "/Master/User/index.php");
												Lobibox.alert("error",
												{
													msg: errorMessage,
													width: 480,
													beforeClose: function() {
														if(counter == 0) {
															setTimeout(function() {
																$("#txtUserName").focus();
															}, 0);
															counter = 1;
														}
													}
												});
												return 0;
											}
										});
									}
									else {
										$("#txtUserName").focus();
										return false;
									}
								});
							}
						}
					},
					{
						text: "Batal",
						id: "btnCancelAddUser",
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
							table.keys.enable();
							if(typeof index !== 'undefined') table.cell(index).focus();
							resetForm();
							return false;
						}
					}]
				}).dialog("open");
			}
			
			function resetForm() {
				$("#hdnUserID").val(0);
				$("#txtUserName").val("");
				$("#txtUserLogin").val("");
				$("#ddlStatus").val(1);
				$("#ddlUserType").val(1);
				$("#txtConfirmPassword").val("");
				$("#txtPassword").val("");
				$("#hdnMenuID").val("");
				$("#hdnEditMenuID").val("");
				$("#hdnDeleteMenuID").val("");
				
				$("input:checkbox[name=permission]").each(function() {
					$(this).attr("checked", false);
					$(this).prop("checked", false);
				});
				
				$("input:checkbox[name=edit]").each(function() {
					$(this).attr("checked", false);
					$(this).prop("checked", false);
				});
				
				$("input:checkbox[name=delete]").each(function() {
					$(this).attr("checked", false);
					$(this).prop("checked", false);
				});
				
				$("input:checkbox[name=grouppermission]").each(function() {
					$(this).attr("checked", false);
					$(this).prop("checked", false);
				});
				
				$("input:checkbox[name=groupedit]").each(function() {
					$(this).attr("checked", false);
					$(this).prop("checked", false);
				});
				
				$("input:checkbox[name=groupdelete]").each(function() {
					$(this).attr("checked", false);
					$(this).prop("checked", false);
				});
			}
			
			function fnDeleteData() {
				var index = table.cell({ focused: true }).index();
				table.keys.disable();
				DeleteData("./Master/User/Delete.php", function(action) {
					if(action == "success") {
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
							if(table.page.info().page == table.page.info().pages) {
								setTimeout(function() {
									table.page("previous").draw('page');
								}, 0);
							}
						}, false);
					}
					else {
						table.keys.enable();
						return false;
					}
				});
			}
			
			function ValidateForm() {
				var isedit = $("#hdnIsEdit").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				$(".form-control-custom").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
				if($("#ddlUserType").val() == "0") {
					$("#ddlUserType").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#ddlUserType").focus();
					PassValidate = 0;
					FirstFocus = 1;
				}
				if(isedit == 0) {
					if($("#txtPassword").val() == '') {
						$("#txtPassword").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtPassword").focus();
						PassValidate = 0;
						FirstFocus = 1;
					}
					if($("#txtConfirmPassword").val() == '') {
						$("#txtConfirmPassword").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtConfirmPassword").focus();
						PassValidate = 0;
						FirstFocus = 1;
					}
					if($("#txtConfirmPassword").val() != $("#txtPassword").val()) {
						$("#txtConfirmPassword").notify("Konfirmasi Password tidak cocok!", { position:"right", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtConfirmPassword").focus();
						PassValidate = 0;
						FirstFocus = 1;
					}
				}
				else {
					if($("#txtPassword").val() != '') {
						if($("#txtConfirmPassword").val() == '') {
							$("#txtConfirmPassword").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $("#txtConfirmPassword").focus();
							PassValidate = 0;
							FirstFocus = 1;
						}
						if($("#txtConfirmPassword").val() != $("#txtPassword").val()) {
							$("#txtConfirmPassword").notify("Konfirmasi Password tidak cocok!", { position:"right", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $("#txtConfirmPassword").focus();
							PassValidate = 0;
							FirstFocus = 1;
						}
					}
				}
				
				if(PassValidate == 1) {
					var MenuID = new Array();
					var EditMenuID = new Array();
					var DeleteMenuID = new Array();
					$("input:checkbox[name=permission]:checked").each(function() {
						var getID = $(this).attr('id');
						if($("#e" + getID).prop('checked')) {
							EditMenuID.push(1);
						}
						else {
							EditMenuID.push(0);
						}
						if($("#d" + getID).prop('checked')) {
							DeleteMenuID.push(1);
						}
						else {
							DeleteMenuID.push(0);
						}
						MenuID.push($(this).attr('id'));
					});
					$("#hdnDeleteMenuID").val(DeleteMenuID);
					$("#hdnEditMenuID").val(EditMenuID);
					$("#hdnMenuID").val(MenuID);
				}
				return PassValidate;
			}
			
			$(document).ready(function() {
				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Master/User/index.php");
					Lobibox.alert("error",
					{
						msg: "Terjadi kesalahan. Memuat ulang halaman.",
						width: 480,
						delay: 2000,
						beforeClose: function() {
							if(counterError == 0) {
								location.reload();
								counterError = 1;
							}
						}
					});
				};
				
				keyFunction();
				enterLikeTab();
				var counterUser = 0;
				table = $("#grid-data").DataTable({
							"keys": true,
							"scrollY": "330px",
							"rowId": "UserID",
							"scrollCollapse": true,
							"order": [2, "asc"],
							"columns": [
								{ "width": "20px", "orderable": false, className: "dt-head-center dt-body-center" },
								{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
								{ className: "dt-head-center" },
								{ className: "dt-head-center" },
								{ className: "dt-head-center" },
								{ className: "dt-head-center" }
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
					else if(counterUser == 0) {
						counterUser = 1;
						var data = datatable.row( cell.index().row ).data();
						if(key == 13) {
							if(($(".ui-dialog").css("display") == "none" || $("#delete-confirm").css("display") == "none") && $("#hdnEditFlag").val() == "1") {
								openDialog(data, 1);
							}
						}
						else if(key == 46  && $("#hdnDeleteFlag").val() == "1") {
							var DeleteID = new Array();
							$("input:checkbox[name=select]:checked").each(function() {
								if($(this).val() != 'all') DeleteID.push($(this).val());
							});
							if(DeleteID.length == 0) {
								table.keys.disable();
								var deletedData = new Array();
								deletedData.push(data[6] + "^" + data[2]);
								SingleDelete("./Master/User/Delete.php", deletedData, function(action) {
									if(action == "success") {
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
											if(table.page.info().page == table.page.info().pages) {
												setTimeout(function() {
													table.page("previous").draw('page');
												}, 0);
											}
										}, false);
									}
									else {
										table.keys.enable();
										return false;
									}
								});
							}
							else {
								fnDeleteData();
							}
						}
						setTimeout(function() { counterUser = 0; } , 1000);
					}
				});
				
				table.on('page', function() {
					$("#select_all").prop("checked", false);
				});
				
				var counterKeyItem = 0;
				$(document).on("keydown", function (evt) {
					if(counterKeyItem == 0) {
						counterKeyItem = 1;
						var index = table.cell({ focused: true }).index();
						if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0) {
							$("#grid-data_wrapper").find("input[type='search']").focus();
						}
						else if (evt.keyCode == 46 && $("#hdnDeleteFlag").val() == "1" && typeof index == 'undefined' && $("#FormData").css("display") == "none") { //delete button
							evt.preventDefault();
							fnDeleteData();
						}
					}
					setTimeout(function() { counterKeyItem = 0; } , 1000);
				});
				
				$("input:checkbox[name=permission]").click(function() {
					var i = parseInt($(this).attr('id'));
					if($(this).prop('checked')) {
						$("#e" + i).removeAttr("disabled");
						$("#d" + i).removeAttr("disabled");
					}
					else {
						$("#e" + i).attr("disabled", true);
						$("#d" + i).attr("disabled", true);
						$("#d" + i).attr("checked", false);
						$("#d" + i).prop("checked", false);
						$("#e" + i).attr("checked", false);
						$("#e" + i).prop("checked", false);
					}
				});
				
				$("input:checkbox[name=grouppermission]").click(function() {
					var i = parseInt($(this).attr('id').replace("g", ""));
					if($(this).prop('checked')) {
						$(".g" + i).attr("checked", true);
						$(".g" + i).prop("checked", true);
						$("#ge" + i).removeAttr("disabled");
						$("#gd" + i).removeAttr("disabled");
						$(".ge" + i).removeAttr("disabled");
						$(".gd" + i).removeAttr("disabled");
					}
					else {
						$("#ge" + i).attr("disabled", true);
						$("#gd" + i).attr("disabled", true);
						$("#ge" + i).attr("checked", false);
						$("#gd" + i).attr("checked", false);
						$(".ge" + i).attr("disabled", true);
						$(".gd" + i).attr("disabled", true);
						$(".g" + i).attr("checked", false);
						$(".g" + i).prop("checked", false);
						$(".gd" + i).attr("checked", false);
						$(".gd" + i).prop("checked", false);
						$(".ge" + i).attr("checked", false);
						$(".ge" + i).prop("checked", false);
					}
				});
				
				$("input:checkbox[name=groupedit]").click(function() {
					var i = parseInt($(this).attr('id').replace("ge", ""));
					if($(this).prop('checked')) {
						$("input:checkbox.ge" + i).each(function() {
							//console.log($(this).prop("disabled"));
							if($(this).prop("disabled") == false) {
								$(this).attr("checked", true);
								$(this).prop("checked", true);
								
							}
						});
					}
					else {
						$(".ge" + i).attr("checked", false);
						$(".ge" + i).prop("checked", false);
					}
				});
				
				$("input:checkbox[name=groupdelete]").click(function() {
					var i = parseInt($(this).attr('id').replace("gd", ""));
					if($(this).prop('checked')) {
						$("input:checkbox.gd" + i).each(function() {
							//console.log($(this).prop("disabled"));
							if($(this).prop("disabled") == false) {
								$(this).attr("checked", true);
								$(this).prop("checked", true);
								
							}
						});
					}
					else {
						$(".gd" + i).attr("checked", false);
						$(".gd" + i).prop("checked", false);
					}
				});
				
				$('#grid-data tbody').on('dblclick', 'tr', function () {
					var data = table.row(this).data();
					openDialog(data, 1);
				});
			});
		</script>
	</body>
</html>
