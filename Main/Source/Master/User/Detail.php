<?php
	if(isset($_GET['ID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$UserID = mysql_real_escape_string($_GET['ID']);
		$UserName = "";
		$UserLogin = "";
		$UserTypeID = 0;
		$IsEdit = 0;
		$MenuID = "";
		$EditMenuID = "";
		$DeleteMenuID = "";
		$IsActive = "";
		
		if($UserID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "CALL spSelUserDetails($UserID, '".$_SESSION['UserLogin']."')";
			if(!$result = mysqli_query($dbh, $sql)) {
				logEvent(mysqli_error($dbh), '/Master/User/Detail.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
				return 0;
			}
			while($row = mysqli_fetch_array($result)) {
				$MenuID .= $row['MenuID'].", ";
				$EditMenuID .= $row['EditFlag'].", ";
				$DeleteMenuID .= $row['DeleteFlag'].", ";
				$UserId = $row['UserID'];
				$UserName = $row['UserName'];
				$UserLogin = $row['UserLogin'];
				$IsActive = $row['IsActive'];
			}
			$MenuID = substr($MenuID, 0, -2);
			$EditMenuID = substr($EditMenuID, 0, -2);
			$DeleteMenuID = substr($DeleteMenuID, 0, -2);
			mysqli_free_result($result);
			mysqli_next_result($dbh);
		}
	}
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
							<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data User</h5>
						</span>
						<span style="width:49%;display:inline-block;text-align:right;">
							<button type="button" tabindex=6 class="btn btn-default" value="Simpan" onclick="SubmitValidate(this.form);" ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" tabindex=7 class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</span>
					</div>
					<div class="panel-body" style="overflow-y:auto;">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama :
									<input id="hdnUserID" name="hdnUserID" type="hidden" <?php echo 'value="'.$UserID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtUserName" name="txtUserName" type="text" tabindex=1 <?php echo 'value="'.$UserName.'"'; ?> class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Nama " required />
								</div>
								<div class="col-md-2 labelColumn">
									Username :
								</div>
								<div class="col-md-3">
									<input id="txtUserLogin" name="txtUserLogin" type="text" tabindex=2 <?php echo 'value="'.$UserLogin.'"'; ?> class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Username" required />
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
								<div class="col-md-2 labelColumn">
									Konfirmasi Password :
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
								<div class="col-md-2 labelColumn">
									Status :
								</div>
								<div class="col-md-3">
									<select id="ddlUserType" name="ddlUserType" tabindex=5 class="form-control-custom"  >
										<option value=1 >Aktif</option>
										<option value=0 >Tidak Aktif</option>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-10">
									<div class="panel panel-default" style="max-height: 380px;min-height: 380px;" >
										<div class="panel-heading">
											<h5>Pilih Hak Akses Menu</h5>
										</div>
										<div class="panel-body" style="min-height: 320px !important;max-height:320px!important;overflow-y:auto;">
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
															while($row = mysqli_fetch_array($result)) {
																if($PrevGroupMenuID == $row['GroupMenuID']) {
																	$RowNumber++;
																	echo "
																		<tr>
																			<td>$RowNumber.</td>
																			<td>".$row['MenuName']."</td>
																			<td style='text-align:center;'><input class='g".$row['GroupMenuID']."' id='".$row['MenuID']."' name='permission' type='checkbox' value='2' /></td>
																			<td style='text-align:center;'><input class='ge".$row['GroupMenuID']."' id='e".$row['MenuID']."' name='edit' type='checkbox' value='true' /></td>
																			<td style='text-align:center;'><input class='gd".$row['GroupMenuID']."' id='d".$row['MenuID']."' name='delete' type='checkbox' value='true' /></td>
																		</tr>";
																}
																else if($PrevGroupMenuID <> $row['GroupMenuID']) {
																	$RowNumber = 1;
																	//Group menu
																	echo "
																		<tr>
																			<td></td>
																			<td ><b><u><i>".$row['GroupMenuName']."</i></u></b></td>
																			<td style='text-align:center;'><input id='g".$row['GroupMenuID']."' name='grouppermission' type='checkbox' /></td>
																			<td style='text-align:center;'><input id='ge".$row['GroupMenuID']."' name='groupedit' type='checkbox' value='true' /></td>
																			<td style='text-align:center;'><input id='gd".$row['GroupMenuID']."' name='groupdelete' type='checkbox' value='true' /></td>
																		</tr>";
																	
																	echo "
																		<tr>
																			<td>$RowNumber.</td>
																			<td>".$row['MenuName']."</td>
																			<td style='text-align:center;'><input class='g".$row['GroupMenuID']."' id='".$row['MenuID']."' name='permission' type='checkbox' value='2' /></td>
																			<td style='text-align:center;'><input class='ge".$row['GroupMenuID']."' id='e".$row['MenuID']."' name='edit' type='checkbox' value='true' /></td>
																			<td style='text-align:center;'><input class='gd".$row['GroupMenuID']."' id='d".$row['MenuID']."' name='delete' type='checkbox' value='true' /></td>
																		</tr>";
																}
																$PrevGroupMenuID = $row['GroupMenuID'];
															}
															mysqli_free_result($result);
															mysqli_next_result($dbh);
														?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
							<input id="hdnMenuID" name="hdnMenuID" type="hidden" <?php echo 'value="'.$MenuID.'"'; ?> />
							<input id="hdnEditMenuID" name="hdnEditMenuID" type="hidden" <?php echo 'value="'.$EditMenuID.'"'; ?> />
							<input id="hdnDeleteMenuID" name="hdnDeleteMenuID" type="hidden" <?php echo 'value="'.$DeleteMenuID.'"'; ?> />
							<input id="hdnIsActive" name="hdnIsActive" type="hidden" <?php echo 'value="'.$IsActive.'"'; ?> />
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				enterLikeTab();
				$("#txtUserName").focus();
				var MenuID = $("#hdnMenuID").val().split(", ");
				var EditMenuID = $("#hdnEditMenuID").val().split(", ");
				var DeleteMenuID = $("#hdnDeleteMenuID").val().split(", ");
				var IsActive = $("#hdnIsActive").val();
				if(IsActive == true) {
					$("#chkActive").attr("checked", true);
					$("#chkActive").prop("checked", true);
				}
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
					}
					else {
						$("#e" + $(this).attr("id")).attr("disabled", true);
						$("#d" + $(this).attr("id")).attr("disabled", true);
					}
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
				
				$("input:checkbox[name=permission]").each(function() {
					if($(this).prop('checked')) {
						$("#ge" + $(this).attr("id")).removeAttr("disabled");
						$("#gd" + $(this).attr("id")).removeAttr("disabled");
					}
					else {
						$("#ge" + $(this).attr("id")).attr("disabled", true);
						$("#gd" + $(this).attr("id")).attr("disabled", true);
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
			});
			
			function SubmitValidate(form) {
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
				
				if(PassValidate == 0) return false;
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
				//console.log(MenuID);
				$("#hdnDeleteMenuID").val(DeleteMenuID);
				$("#hdnEditMenuID").val(EditMenuID);
				$("#hdnMenuID").val(MenuID);
				SubmitForm("./Master/User/Insert.php");
			}
		</script>
	</body>
</html>
