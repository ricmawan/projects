<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
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
			$sql = "SELECT
						UserID,
						UserTypeID,
						UserName,
						UserLogin,
						IsActive
					FROM
						master_user
					WHERE
						UserID = $UserID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$UserId = $row['UserID'];
			$UserName = $row['UserName'];
			$UserTypeID = $row['UserTypeID'];
			$UserLogin = $row['UserLogin'];
			$IsActive = $row['IsActive'];
			
			$sql = "SELECT
						RoleID,
						UserID,
						MenuID,
						EditFlag,
						DeleteFlag
					FROM
						master_role
					WHERE
						UserID = $UserID";
			if(!$result = mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}
			while($row = mysql_fetch_array($result)) {
				$MenuID .= $row['MenuID'].", ";
				$EditMenuID .= $row['EditFlag'].", ";
				$DeleteMenuID .= $row['DeleteFlag'].", ";
			}
			$MenuID = substr($MenuID, 0, -2);
			$EditMenuID = substr($EditMenuID, 0, -2);
			$DeleteMenuID = substr($DeleteMenuID, 0, -2);
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Dokter</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-1 labelColumn">
									Nama :
									<input id="hdnUserID" name="hdnUserID" type="hidden" <?php echo 'value="'.$UserID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtUserName" name="txtUserName" type="text" class="form-control-custom" placeholder="Nama " required   <?php echo 'value="'.$UserName.'"'; ?> />
								</div>
								<div class="col-md-2 labelColumn">
									Username :
								</div>
								<div class="col-md-3">
									<input id="txtUserLogin" name="txtUserLogin" type="text" class="form-control-custom" placeholder="Username" required <?php echo 'value="'.$UserLogin.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-1 labelColumn">
									Password:
								</div>
								<div class="col-md-3">
									<input id="txtPassword" name="txtPassword" type="password" class="form-control-custom" placeholder="Password" />
								</div>
								<div class="col-md-2 labelColumn">
									Konfirmasi Password:
								</div>
								<div class="col-md-3">
									<input id="txtConfirmPassword" name="txtConfirmPassword" type="password" class="form-control-custom" placeholder="Konfirmasi Password"   />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">
									<input type="checkbox" id="chkActive" name="chkActive" value=1 style="vertical-align: sub;" /> Aktif
								</div>
							</div>
							<br />
							<br />
							<div class="row">
								<div class="col-md-1 labelColumn">
									Tahun :
								</div>
								<div class="col-md-2">
									<select id="ddlYear" name="ddlYear" class="form-control-custom" style="width:auto;">
										<?php
											$EndYear = (int)date("Y");
											for($StartYear = 2016;$StartYear <= $EndYear;$StartYear++) {
												echo "<option value=$StartYear>$StartYear</option>";
											}
										?>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-6">
									<div class="panel panel-default" style="min-height: 450px;" >
										<div class="panel-heading">
											<h5>Komisi dan biaya alat</h5>
										</div>
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th>No</th>
														<th>Bulan</th>
														<th>Biaya Alat</th>
														<th>Presentase Komisi</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>1</td>
														<td>Januari</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee1" name="fee1" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision1" name="commision1" /></td>
													</tr>
													<tr>
														<td>2</td>
														<td>Februari</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee2" name="fee2" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision2" name="commision2" /></td>
													</tr>
													<tr>
														<td>3</td>
														<td>Maret</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee3" name="fee3" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision3" name="commision3" /></td>
													</tr>
													<tr>
														<td>4</td>
														<td>April</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee4" name="fee4" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision4" name="commision4" /></td>
													</tr>
													<tr>
														<td>5</td>
														<td>Mei</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee5" name="fee5" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)"  id="commision5" name="commision5" /></td>
													</tr>
													<tr>
														<td>6</td>
														<td>Juni</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee6" name="fee6" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision6" name="commision6" /></td>
													</tr>
													<tr>
														<td>7</td>
														<td>Juli</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee7" name="fee7" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision7" name="commision7" /></td>
													</tr>
													<tr>
														<td>8</td>
														<td>Agustus</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee8" name="fee8" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision8" name="commision8" /></td>
													</tr>
													<tr>
														<td>9</td>
														<td>September</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee9" name="fee9" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision9" name="commision9" /></td>
													</tr>
													<tr>
														<td>10</td>
														<td>Oktober</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee10" name="fee10" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision10" name="commision10" /></td>
													</tr>
													<tr>
														<td>11</td>
														<td>November</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee11" name="fee11" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision11" name="commision11" /></td>
													</tr>
													<tr>
														<td>12</td>
														<td>Desember</td>
														<td><input type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee12" name="fee12" /></td>
														<td><input type="text" class="form-control-custom" onchange="ValidateDiscount(this.id)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision12" name="commision12" /></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="row" style="display: none;">
								<div class="col-md-10">
									<div class="panel panel-default" style="min-height: 450px;" >
										<div class="panel-heading">
											<h5>Pilih Hak Akses Menu</h5>
										</div>
										<div class="panel-body">
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
															$sql = "SELECT 
																	GroupMenuID,
																	GroupMenuName,
																	Icon,
																	Url
																FROM
																	master_groupmenu";
															if (! $result=mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_array($result)) {
																$RowNumber = 1;
																$sql2 = "SELECT
																		MenuID,
																		GroupMenuID,
																		MenuName,
																		Url,
																		Icon
																	 FROM
																		master_menu
																	WHERE 
																		GroupMenuID = ".$row['GroupMenuID']."
																	ORDER BY
																		OrderNo";
																if (! $result2=mysql_query($sql2, $dbh)) {
																	echo mysql_error();
																	return 0;
																}
																$rowcount = mysql_num_rows($result2);
																if($rowcount > 0) {
																	echo "
																		<tr>
																			<td></td>
																			<td colspan='4'><b><u><i>".$row['GroupMenuName']."</i></u></b></td>
																		</tr>";
																	while($row2 = mysql_fetch_array($result2)) {
																		echo "
																			<tr>
																				<td>$RowNumber.</td>
																				<td>".$row2['MenuName']."</td>
																				<td style='text-align:center;'><input id='".$row2['MenuID']."' name='permission' type='checkbox' value='2' /></td>
																				<td style='text-align:center;'><input id='e".$row2['MenuID']."' name='edit' type='checkbox' value='true' /></td>
																				<td style='text-align:center;'><input id='d".$row2['MenuID']."' name='delete' type='checkbox' value='true' /></td>
																			</tr>";
																		$RowNumber++;
																	}
																}
															}
														?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
							<br />
							<input type="hidden" name="hdnMenuID" id="hdnMenuID" <?php echo 'value="'.$MenuID.'"'; ?> />
							<input type="hidden" name="hdnEditMenuID" id="hdnEditMenuID" <?php echo 'value="'.$EditMenuID.'"'; ?> />
							<input type="hidden" name="hdnDeleteMenuID" id="hdnDeleteMenuID" <?php echo 'value="'.$DeleteMenuID.'"'; ?> />
							<input type="hidden" name="hdnIsActive" id="hdnIsActive" <?php echo 'value="'.$IsActive.'"'; ?> />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitValidate(this.form);" ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				$("#5").attr("checked", true);
				$("#5").prop("checked", true);
				$("#9").attr("checked", true);
				$("#9").prop("checked", true);
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
				}
				if(isedit == 0) {
					if($("#txtPassword").val() == '') {
						$("#txtPassword").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtPassword").focus();
						PassValidate = 0;
					}
					if($("#txtConfirmPassword").val() == '') {
						$("#txtConfirmPassword").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtConfirmPassword").focus();
						PassValidate = 0;
					}
					if($("#txtConfirmPassword").val() != $("#txtPassword").val()) {
						$("#txtConfirmPassword").notify("Konfirmasi Password tidak cocok!", { position:"right", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtConfirmPassword").focus();
						PassValidate = 0;
					}
				}
				else {
					if($("#txtPassword").val() != '') {
						if($("#txtConfirmPassword").val() == '') {
							$("#txtConfirmPassword").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $("#txtConfirmPassword").focus();
							PassValidate = 0;
						}
						if($("#txtConfirmPassword").val() != $("#txtPassword").val()) {
							$("#txtConfirmPassword").notify("Konfirmasi Password tidak cocok!", { position:"right", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $("#txtConfirmPassword").focus();
							PassValidate = 0;
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
				SubmitForm("./Master/Doctor/Insert.php");
			}
		</script>
	</body>
</html>
