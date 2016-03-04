<?php
	if(isset($_GET['id'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$Id = $_GET['id'];
		$Nama = "";
		$Username = "";
		$Jabatan = "";
		$IsEdit = 0;
		$idmenu = "";
		$idedit = "";
		$iddelete = "";
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			if($Id !=0) {
				$IsEdit = 1;
				//$Content = "Place the content here";
				$sql = "SELECT
							*
						FROM
							master_user
						WHERE
							UserID = $Id";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Nama = $row[1];
				$Username = $row[2];
				$Jabatan = $row[4];
				
				$sql = "SELECT * FROM master_role WHERE UserID = '$Id'";
				if(!$result = mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}
				while($row = mysql_fetch_row($result)) {
					$idmenu .= $row[2].", ";
					$idedit .= $row[3].", ";
					$iddelete .= $row[4].", ";
				}
				$idmenu = substr($idmenu, 0, -2);
				$idedit = substr($idedit, 0, -2);
				$iddelete = substr($iddelete, 0, -2);
			}
		}
	}
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Master Data User</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data User</strong>  
					</div>
					<div class="panel-body">
						<form class="col-md-6" id="PostForm" method="POST" action="" >
							Nama:<br />
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnJabatan" name="hdnJabatan" type="hidden" <?php echo 'value="'.$Jabatan.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtNama" name="txtNama" type="text" class="form-control" placeholder="Nama " required   <?php echo 'value="'.$Nama.'"'; ?> />
							<br />
							Username:<br />
							<input id="txtUsername" name="txtUsername" type="text" class="form-control" placeholder="Username"   required <?php echo 'value="'.$Username.'"'; ?> />
							<br />
							Jabatan:<br />
							<select class="form-control" name="ddlJabatan" id="ddlJabatan" required>
								<option value="" selected>-- Pilih Jabatan --</option>
								<option value="Customer Service">Customer Service</option>
								<option value="Konsultan">Konsultan</option>
								<option value="Admin">Admin</option>
							</select>
							<br />
							Password:<br />
							<input id="txtPassword" name="txtPassword" type="password" class="form-control" placeholder="Password"   />
							<br />
							Konfirmasi Password:<br />
							<input id="txtKonfirmasiPassword" name="txtKonfirmasiPassword" type="password" class="form-control" placeholder="Konfirmasi Password"   />
							<br />
							 <!--   Basic Table  -->
							<div class="panel panel-default">
								<div class="panel-heading">
									Pilih Hak Akses Menu
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
												<tr>
													<td></td>
													<td colspan="4"><u><i>Master Data</i></u></td>
												</tr>
												<tr>
													<td>1.</td>
													<td>User</td>
													<td style="text-align:center;"><input id="1" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e1" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d1" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>2.</td>
													<td>Parameter</td>
													<td style="text-align:center;"><input id="25" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e25" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d25" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>3.</td>
													<td>Alat</td>
													<td style="text-align:center;"><input id="2" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e2" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d2" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>4.</td>
													<td>Asisten</td>
													<td style="text-align:center;"><input id="3" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e3" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d3" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>5.</td>
													<td>Jabatan</td>
													<td style="text-align:center;"><input id="4" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e4" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d4" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>6.</td>
													<td>Jobdesk Asisten</td>
													<td style="text-align:center;"><input id="5" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e5" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d5" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>7.</td>
													<td>Konsultan</td>
													<td style="text-align:center;"><input id="6" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e6" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d6" name="delete" type='checkbox' value='true' /></td>
												</tr>
													<tr>
													<td>8.</td>
													<td>Supervisor</td>
													<td style="text-align:center;"><input id="27" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e27" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d27" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>9.</td>
													<td>Layanan</td>
													<td style="text-align:center;"><input id="7" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e7" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d7" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>10.</td>
													<td>Terapis</td>
													<td style="text-align:center;"><input id="8" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e8" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d8" name="delete" type='checkbox' value='true' /></td>
												</tr>
												
												
												<tr>
													<td></td>
													<td colspan="4"><u><i>Data Klien</i></u></td>
												</tr>
												<tr>
													<td>1.</td>
													<td>Perusahaan/Industri</td>
													<td style="text-align:center;"><input id="15" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e15" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d15" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>2.</td>
													<td>Anak & Remaja</td>
													<td style="text-align:center;"><input id="12" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e12" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d12" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>3.</td>
													<td>Dewasa</td>
													<td style="text-align:center;"><input id="13" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e13" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d13" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>4.</td>
													<td>Pendidikan</td>
													<td style="text-align:center;"><input id="14" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e14" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d14" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td></td>
													<td colspan="4"><u><i>Customer Service</i></u></td>
												</tr>
												<tr>
													<td>1.</td>
													<td>Customer Service</td>
													<td style="text-align:center;"><input id="9" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e9" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d9" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>2.</td>
													<td>Absen Peserta</td>
													<td style="text-align:center;"><input id="20" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e20" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d20" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td></td>
													<td colspan="4"><u><i>Operasional</i></u></td>
												</tr>
												<tr>
													<td>1.</td>
													<td>Honorium Asisten</td>
													<td style="text-align:center;"><input id="10" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e10" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d10" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>2.</td>
													<td>Inventaris Masuk</td>
													<td style="text-align:center;"><input id="11" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e11" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d11" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>3.</td>
													<td>Inventaris Keluar</td>
													<td style="text-align:center;"><input id="23" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e23" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d23" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>4.</td>
													<td>Kas Keluar</td>
													<td style="text-align:center;"><input id="21" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e21" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d21" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>5.</td>
													<td>Piket Konsultan</td>
													<td style="text-align:center;"><input id="22" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e22" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d22" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>6.</td>
													<td>Fee SPV</td>
													<td style="text-align:center;"><input id="26" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e26" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d26" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td></td>
													<td colspan="4"><u><i>Laporan</i></u></td>
												</tr>
												<tr>
													<td>1.</td>
													<td>Honorium Konsultan</td>
													<td style="text-align:center;"><input id="18" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e18" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d18" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>2.</td>
													<td>Honorium Asisten</td>
													<td style="text-align:center;"><input id="16" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e16" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d16" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>3.</td>
													<td>Psikotes</td>
													<td style="text-align:center;"><input id="19" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e19" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d19" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>4.</td>
													<td>Pendapatan</td>
													<td style="text-align:center;"><input id="17" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e17" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d17" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>5.</td>
													<td>Kas Keluar</td>
													<td style="text-align:center;"><input id="24" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e24" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d24" name="delete" type='checkbox' value='true' /></td>
												</tr>
												<tr>
													<td>6.</td>
													<td>Fee SPV</td>
													<td style="text-align:center;"><input id="28" name="permission" type='checkbox' value='2' /></td>
													<td style="text-align:center;"><input id="e28" name="edit" type='checkbox' value='true' /></td>
													<td style="text-align:center;"><input id="d28" name="delete" type='checkbox' value='true' /></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<input type="hidden" name="hdnMenuID" id="hdnMenuID" <?php echo 'value="'.$idmenu.'"'; ?> />
							<input type="hidden" name="hdnEditMenuID" id="hdnEditMenuID" <?php echo 'value="'.$idedit.'"'; ?> />
							<input type="hidden" name="hdnDeleteMenuID" id="hdnDeleteMenuID" <?php echo 'value="'.$iddelete.'"'; ?> />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitValidate(this.form);" ><i class="fa fa-save"></i> Simpan</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				$("#ddlJabatan").val($("#hdnJabatan").val());
				var idmenuselected = $("#hdnMenuID").val().split(", ");
				var ideditselected = $("#hdnEditMenuID").val().split(", ");
				var iddeleteselected = $("#hdnDeleteMenuID").val().split(", ");
				
				for (var i = 0; i < idmenuselected.length; i++) {
					var val = idmenuselected[i];
					var edit = ideditselected[i];
					var hapus = iddeleteselected[i];
					if(edit == true) {
						$("#e"+val).attr("checked", true);
						$("#e"+val).prop("checked", true);
					}
					if(hapus == true) {
						$("#d"+val).attr("checked", true);
						$("#d"+val).prop("checked", true);
					}
					$("#"+val).attr("checked", true);
					$("#"+val).prop("checked", true);
				}
				
				$("input:checkbox[name=permission]").each(function() {
					if($(this).prop('checked')) {
						$("#e"+$(this).attr("id")).removeAttr("disabled");
						$("#d"+$(this).attr("id")).removeAttr("disabled");
					}
					else {
						$("#e"+$(this).attr("id")).attr("disabled", true);
						$("#d"+$(this).attr("id")).attr("disabled", true);
					}
				});
				$("input:checkbox[name=permission]").click(function() {
					var i = parseInt($(this).attr('id'));
					if($(this).prop('checked')) {
						$("#e"+i).removeAttr("disabled");
						$("#d"+i).removeAttr("disabled");
					}
					else {
						$("#e"+i).attr("disabled", true);
						$("#d"+i).attr("disabled", true);
						$("#d"+i).attr("checked", false);
						$("#d"+i).prop("checked", false);
						$("#e"+i).attr("checked", false);
						$("#e"+i).prop("checked", false);
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
							$(this).notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
				if(isedit == 0) {
					if($("#txtPassword").val() == '') {
						$("#txtPassword").notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtPassword").focus();
						PassValidate = 0;
					}
					if($("#txtKonfirmasiPassword").val() == '') {
						$("#txtKonfirmasiPassword").notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtKonfirmasiPassword").focus();
						PassValidate = 0;
					}
					if($("#txtKonfirmasiPassword").val() != $("#txtPassword").val()) {
						$("#txtKonfirmasiPassword").notify("Konfirmasi Password tidak cocok!", { position:"right", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtKonfirmasiPassword").focus();
						PassValidate = 0;
					}
				}
				else {
					if($("#txtPassword").val() != '') {
						if($("#txtKonfirmasiPassword").val() == '') {
							$("#txtKonfirmasiPassword").notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $("#txtKonfirmasiPassword").focus();
							PassValidate = 0;
						}
						if($("#txtKonfirmasiPassword").val() != $("#txtPassword").val()) {
							$("#txtKonfirmasiPassword").notify("Konfirmasi Password tidak cocok!", { position:"right", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $("#txtKonfirmasiPassword").focus();
							PassValidate = 0;
						}
					}
				}
				
				if(PassValidate == 0) return false;
				var idmenu = new Array();
				var idedit = new Array();
				var iddelete = new Array();
				$("input:checkbox[name=permission]:checked").each(function() {
					var getID = $(this).attr('id');
					if($("#e"+getID).prop('checked')) {
						idedit.push(1);
					}
					else {
						idedit.push(0);
					}
					if($("#d"+getID).prop('checked')) {
						iddelete.push(1);
					}
					else {
						iddelete.push(0);
					}
					idmenu.push($(this).attr('id'));
				});
				//console.log(idmenu);
				$("#hdnDeleteMenuID").val(iddelete);
				$("#hdnEditMenuID").val(idedit);
				$("#hdnMenuID").val(idmenu);
				SubmitForm("./Master/User/Insert.php");
			}
		</script>
	</body>
</html>