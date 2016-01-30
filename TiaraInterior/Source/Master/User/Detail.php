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
		$IsEdit = 0;
		$MenuID = "";
		$EditMenuID = "";
		$DeleteMenuID = "";
		
		if($UserID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
					UserID,
					UserName,
					UserLogin					
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
			$UserLogin = $row['UserLogin'];
			
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
						<h2><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data User</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-5">
									Nama:<br />
									<input id="hdnUserID" name="hdnUserID" type="hidden" <?php echo 'value="'.$UserID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="txtUserName" name="txtUserName" type="text" class="form-control" placeholder="Nama " required   <?php echo 'value="'.$UserName.'"'; ?> />
								</div>
								<div class="col-md-5">
									Password:<br />
									<input id="txtPassword" name="txtPassword" type="password" class="form-control" placeholder="Password" />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-5">
									Username:<br />
									<input id="txtUserLogin" name="txtUserLogin" type="text" class="form-control" placeholder="Username" required <?php echo 'value="'.$UserLogin.'"'; ?> />
								</div>
								<div class="col-md-5">
									Konfirmasi Password:<br />
									<input id="txtConfirmPassword" name="txtConfirmPassword" type="password" class="form-control" placeholder="Konfirmasi Password"   />
								</div>
							</div>
							<div class="row">
								<div class="col-md-5">
									<br /><input type="checkbox" id="chkActive" name="chkActive" style="vertical-align: sub;" /> Aktif
								</div>
							</div>
							<br />
							<br />
							<div class="row">
								<div class="col-md-10">
									<div class="panel panel-default">
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
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitValidate(this.form);" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
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
				$(".form-control").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
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
				SubmitForm("./Master/User/Insert.php");
			}
		</script>
	</body>
</html>
