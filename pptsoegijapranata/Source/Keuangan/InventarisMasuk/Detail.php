<?php
	if(isset($_GET['id'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$Id = $_GET['id'];
		$Tanggal = "";
		$AsistenID = "";
		$Data = "";
		$rowcount = 0;
		$IsEdit = 0;
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			if($Id !=0) {
				$IsEdit = 1;
				//$Content = "Place the content here";
				$sql = "SELECT
						TI.TransaksiID,
						DATE_FORMAT(TI.Tanggal, '%d-%m-%Y')
					FROM
						transaksi_inventarismasuk TI
					WHERE
						TI.TransaksiID = $Id";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Tanggal = $row[1];

				$sql = "SELECT 
						DetailID,
						AlatID,
						Jumlah
					FROM
						transaksi_rinciinventarismasuk
					WHERE
						TransaksiID = '$Id'";
				if(!$result = mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}
				$rowcount = mysql_num_rows($result);
				if($rowcount > 0) {
					//$DetailID = array();
					$Data = array();
					while($row = mysql_fetch_row($result)) {
						//array_push($DetailID, $row[0]);
						array_push($Data, "'".$row[0]."', '".$row[1]."', '".$row[2]."'");
					}
					//$DetailID = implode(",", $DetailID);
					$Data = implode("|", $Data);
				}
				else {
					//$DetailID = "";
					$Data = "";
				}
			}
		}
	}
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
			.btn-group {
				width: 100%;
				display: block;
			}
			.col-md-2 {
				display: -webkit-box;
				-webkit-box-align: center;
				height: 34px;
			}
			.col-md-1 {
				display: -webkit-box;
				-webkit-box-align: center;
				height: 34px;
				width: 1%;
			}
			.col-md-6 {
				display: -webkit-box;
				-webkit-box-align: center;
			}
			.ui-autocomplete {
				font-family: Open Sans, sans-serif; 
				font-size: 14px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Inventaris</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data Inventaris</strong>  
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-2">Id :</div>
							<div class="col-md-6">
								<input id="txtId" name="txtId" type="text" class="form-control" placeholder="Id" readonly="readonly" <?php echo 'value="'.$Id.'"'; ?> />
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2">Tanggal :</div>
							<div class="col-md-6">
								<input id="txtTanggal" name="txtTanggal" type="text" class="form-control DatePickerFromNow" placeholder="Tanggal" required <?php echo 'value="'.$Tanggal.'"'; ?> />
							</div>
						</div>
						<br />
						<br />
						<div class="row">
							<div class="col-md-12">
								<form id="PostForm" method="POST" action="" >
									<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowcount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnTanggal" name="hdnTanggal" type="hidden" <?php echo 'value="'.$Tanggal.'"'; ?> />
									<div class="table-responsive table-bordered">
										<table class="table" id="datainput">
											<thead>
												<td>No</td>
												<td>Alat</td>
												<td>Satuan</td>
												<td>Jumlah</td>
											</thead>
											<tbody>
												<tr id='' align='center' style='display:none;'>
													<td id='nota' name='nota'></td>
													<td>
														<input type="hidden" id="hdnDetailID" name="hdnDetailID" value="0" />
														<select id="ddlAlat" name="ddlAlat" class="form-control alat" onchange="BindSatuan(this.getAttribute('row'))" >
															<option value=0 tipe="pilih">-Pilih Alat-</option>
															<?php
																$sql = "SELECT
																		AlatID,
																		NamaAlat,
																		CASE
																			WHEN Satuan = 1
																			THEN 'Pcs'
																			ELSE 'Pax'
																		END AS Satuan
																	FROM
																		master_alat";
																if(!$result = mysql_query($sql, $dbh)) {
																	echo mysql_error();
																	return 0;
																}
																while($row = mysql_fetch_row($result)) {
																	echo "<option value='".$row[0]."' satuan='".$row[2]."' >".$row[1]."</option>";
																}
															?>
														</select>
													</td>
													<td>
														<input type="text" id="txtSatuan" name="txtSatuan" class="form-control txtSatuan" readonly="readonly" />
													</td>
													<td>
														<input type="text" id="txtJumlah" name="txtJumlah" onkeypress="return isNumberKey(event)" class="form-control txtJumlah" value=1 readonly="readonly" />
													</td>
												</tr>
												<tr id='num1' align='center'>
													<td id='nota1' name='nota1'  style='width:20px;'>1</td>
													<td>
														<input type="hidden" id="hdnDetailID1" name="hdnDetailID1" value="0" />
														<select id="ddlAlat1" name="ddlAlat1" row=1 class="form-control alat" onchange="BindSatuan(this.getAttribute('row'))" >
															<option value=0 tipe="pilih">-Pilih Alat-</option>
															<?php
																$sql = "SELECT
																		AlatID,
																		NamaAlat,
																		CASE
																			WHEN Satuan = 1
																			THEN 'Pcs'
																			ELSE 'Pax'
																		END AS Satuan
																	FROM
																		master_alat";
																if(!$result = mysql_query($sql, $dbh)) {
																	echo mysql_error();
																	return 0;
																}
																while($row = mysql_fetch_row($result)) {
																	echo "<option value='".$row[0]."' satuan='".$row[2]."' >".$row[1]."</option>";
																}
															?>
														</select>
													</td>
													<td>
														<input type="text" id="txtSatuan1" name="txtSatuan1" class="form-control txtSatuan" readonly="readonly" />
													</td>
													<td>
														<input type="text" id="txtJumlah1" name="txtJumlah1" onkeypress="return isNumberKey(event)" class="form-control txtJumlah" value=1 readonly="readonly" />
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<br />
									<input type="hidden" id="record" name="record" value=1 />
									<input type="hidden" id="recordnew" name="recordnew" value=1 />
								</form>
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-6">
											<button class="btn btn-primary" id="btnAdd"><i class="fa fa-plus "></i> Tambah</button>&nbsp;&nbsp;
											<button class="btn btn-danger" id="btnDelete"><i class="fa fa-close"></i> Hapus</button>
										</div>
										<div class="col-md-3">
										</div>
										<div class="col-md-3" style="float:right;">
											<button class="btn btn-default" id="btnSave" onclick="SubmitValidate();" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
											<!--<button class="btn btn-default" id="btnPrint" ><i class="fa fa-print"></i> Print</button>-->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>			
			$(document).ready(function () {
				$("#btnAdd").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					count++;
					var $clone = $("#datainput tbody tr:first").clone();
					$clone.find("#nota").text(count);
					$clone.find("#nota").attr("id", "nota" + count);
					$clone.find("#nota").attr("name", "nota" + count);
					$clone.removeAttr("style");
					$clone.attr({
						id: "num" + count,
						name: "num" + count

					});
					$clone.find("input, select").each(function(){
						//var temp = $(this).attr("id") + (count - 1);
						$(this).attr({
							id: $(this).attr("id") + count,
							name: $(this).attr("name") + count,
							row: count
						});				
						//$(this).val($("#" + temp).val());
					});
					$("#datainput tbody").append($clone);
					$("#ddlAlat" + count).addClass("alat");
					$("#ddlAlat" + count).attr("required", "");
					$("#txtJumlah" + count).attr("required", "");
					$("#recordnew").val(count);
					//BindJobdesk();
				});
				$("#btnDelete").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					if(count > 1) {
						$('#datainput tr:last').remove();
						$("#recordnew").val(count-1);
					}
				});
				var IsEdit = $("#hdnIsEdit").val();
				if(IsEdit == 1) {
					if(parseInt($("#hdnRow").val()) > 0) {
						//alert("Test");
						var data = $("#hdnData").val();
						var item = data.split("|");
						var e = item[0].split("', '");
						var row = item.length;
						var count = 0;
						$('#datainput tbody:last > tr:not(:first)').remove();
						for(i=0; i<row; i++) {
							$("#btnAdd").click();
							count++;
							//set values
							var d = item[i].split("', '");
							$("#nota").text(count);
							$("#hdnDetailID" + count).val(d[0].replace("'", ""));
							$("#ddlAlat" + count).val(d[1].replace("'", ""));
							$("#txtJumlah" + count).val(d[2].replace("'", ""));
							
							$("#txtSatuan" + count).val($("#ddlAlat" + count + " option:selected").attr("satuan"));
							$("#txtJumlah" + count).removeAttr("readonly");
							$("#record").val(count);
							$("#recordnew").val(count);
						}
					}
				}				
			});
			function SubmitValidate() {
				$("#hdnTanggal").val($("#txtTanggal").val());
				//$("#hdnAsistenID").val($("#ddlAsisten").val());
				SubmitForm("./Keuangan/InventarisMasuk/Insert.php");
			}
			function BindSatuan(row) {
				$("#txtSatuan" + row).val($("#ddlAlat" + row + " option:selected").attr("satuan"));
				$("#txtJumlah" + row).removeAttr("readonly");
			}
		</script>
	</body>
</html>
