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
						TA.TransaksiID,
						DATE_FORMAT(TA.Tanggal, '%d-%m-%Y'),
						TA.AsistenID
					FROM
						transaksi_asisten TA
					WHERE
						TA.TransaksiID = $Id";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Tanggal = $row[1];
				$AsistenID = $row[2];

				$sql = "SELECT 
						DetailID,
						JobdeskID,
						Jumlah,
						Harga
					FROM
						transaksi_rinciasisten
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
						array_push($Data, "'".$row[0]."', '".$row[1]."', '".$row[2]."', '".$row[3]."'");
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
				<h2>Honorium Asisten</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data Honorium Asisten</strong>  
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
						<div class="row">
							<div class="col-md-2">Asisten :</div>
							<div class="col-md-6">
								<select id="ddlAsisten" name="ddlAsisten" class="form-control" onchange="BindJobdesk()" required>
									<option value=0 selected>-Pilih Asisten-</option>
									<?php
										$sql = "SELECT * FROM master_asisten";
										if(!$result = mysql_query($sql, $dbh)) {
											echo mysql_error();
											return 0;
										}
										while($row = mysql_fetch_row($result)) {
											echo "<option value='".$row[0]."' tipe=".$row[2].">".$row[1]."</option>";
										}
									?>
								</select>
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
									<input id="hdnAsistenID" name="hdnAsistenID" type="hidden" <?php echo 'value="'.$AsistenID.'"'; ?> />
									<input id="hdnTanggal" name="hdnTanggal" type="hidden" <?php echo 'value="'.$Tanggal.'"'; ?> />
									<div class="table-responsive table-bordered">
										<table class="table" id="datainput">
											<thead>
												<td>No</td>
												<td>Jobdesk</td>
												<td>Satuan</td>
												<td>Jumlah</td>
												<td>Harga</td>
											</thead>
											<tbody>
												<tr id='' align='center' style='display:none;'>
													<td id='nota' name='nota'></td>
													<td>
														<input type="hidden" id="hdnDetailID" name="hdnDetailID" value="0" />
														<select id="ddlJobdesk" name="ddlJobdesk" class="form-control jobdesk" onchange="BindSatuan(this.getAttribute('row'))" >
															<option value=0 tipe="pilih">-Pilih Jobdesk-</option>
															<?php
																$sql = "SELECT
																		JobdeskID,
																		Keterangan,
																		tipe,
																		CASE
																			WHEN Satuan = 1
																			THEN 'Per Jaga'
																			ELSE 'Per Jam'
																		END AS Satuan,	
																		Harga
																	FROM
																		master_jobdesk";
																if(!$result = mysql_query($sql, $dbh)) {
																	echo mysql_error();
																	return 0;
																}
																while($row = mysql_fetch_row($result)) {
																	echo "<option style='display:none;' value='".$row[0]."' tipe=".$row[2]." satuan='".$row[3]."' harga=".$row[4].">".$row[1]."</option>";
																}
															?>
														</select>
													</td>
													<td>
														<input type="text" id="txtSatuan" name="txtSatuan" class="form-control txtSatuan" readonly="readonly" />
													</td>
													<td>
														<input type="text" id="txtJumlah" name="txtJumlah" onkeypress="return isNumberKey(event)" onchange="BindHarga(this.getAttribute('row'))" class="form-control txtJumlah" readonly value=1 />
													</td>
													<td>
														<input type="text" id="txtHarga" style="text-align:right;" onchange="calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" name="txtHarga" class="form-control txtHarga" value="0.00" />
													</td>
												</tr>
												<tr id='num1' align='center'>
													<td id='nota1' name='nota1'  style='width:20px;'>1</td>
													<td>
														<input type="hidden" id="hdnDetailID1" name="hdnDetailID1" value="0" />
														<select id="ddlJobdesk1" name="ddlJobdesk1" class="form-control jobdesk" required row=1 onchange="BindSatuan(this.getAttribute('row'))" >
															<option value=0  tipe="pilih">-Pilih Jobdesk-</option>
															<?php
																$sql = "SELECT
																		JobdeskID,
																		Keterangan,
																		tipe,
																		CASE
																			WHEN Satuan = 1
																			THEN 'Per Jaga'
																			ELSE 'Per Jam'
																		END AS Satuan,	
																		Harga
																	FROM
																		master_jobdesk";
																if(!$result = mysql_query($sql, $dbh)) {
																	echo mysql_error();
																	return 0;
																}
																while($row = mysql_fetch_row($result)) {
																	echo "<option style='display:none;' value='".$row[0]."' tipe=".$row[2]." satuan='".$row[3]."' harga=".$row[4].">".$row[1]."</option>";
																}
															?>
														</select>
													</td>
													<td>
														<input type="text" id="txtSatuan1" name="txtSatuan1" class="form-control txtSatuan" readonly="readonly" />
													</td>
													<td>
														<input type="text" id="txtJumlah1" name="txtJumlah1" onkeypress="return isNumberKey(event)" row=1 onchange="BindHarga(this.getAttribute('row'))" class="form-control txtJumlah" value=1 readonly required />
													</td>
													<td>
														<input type="text" id="txtHarga1" style="text-align:right;" onchange="calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" name="txtHarga1" class="form-control txtHarga" value="0.00" />
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<br />
									<div class="row">
										<div class="col-md-12">
											<div class="col-md-2">
												Grand Total :
											</div>
											<div class="col-md-3">
												<input type="text" name="txtTotal" style="text-align:right;" id="txtTotal" class="form-control col-md-3" value="0.00" readonly />
											</div>
										</div>
									</div>
									<br />
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
					$("#ddlJobdesk" + count).addClass("jobdesk");
					$("#ddlJobdesk" + count).attr("required", "");
					$("#txtJumlah" + count).attr("required", "");
					$("#txtHarga" + count).attr("required", "");
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
					$("#ddlAsisten").val($("#hdnAsistenID").val());
					BindJobdesk();
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
							$("#ddlJobdesk" + count).val(d[1].replace("'", ""));
							$("#txtJumlah" + count).val(d[2].replace("'", ""));
							$("#txtHarga" + count).val(returnRupiah(d[3].replace("'", "")));
							
							$("#txtSatuan" + count).val($("#ddlJobdesk" + count + " option:selected").attr("satuan"));
							//$("#txtHarga" + count).val(returnRupiah(($("#txtJumlah" + count).val() * $("#ddlJobdesk" + count + " option:selected").attr("harga")).toString()));
							$("#txtJumlah" + count).removeAttr("readonly");
							$("#record").val(count);
							$("#recordnew").val(count);
						}
						calculate();
					}
				}				
			});
			function SubmitValidate() {
				$("#hdnTanggal").val($("#txtTanggal").val());
				$("#hdnAsistenID").val($("#ddlAsisten").val());
				SubmitForm("./Keuangan/HonoriumAsisten/Insert.php");
			}
			function BindJobdesk() {
				var tipe = $("#ddlAsisten option:selected").attr("tipe");
				$(".jobdesk option").each(function() {
					if($(this).attr("tipe") == "pilih") $(this).attr("selected", "selected");
					if($(this).attr("tipe") == tipe || $(this).attr("tipe") == "pilih") {
						$(this).css({ "display" : "inline" });
					}
					else {
						$(this).css({ "display" : "none" });
					}
				});
				$(".txtSatuan").val("");
				$(".txtJumlah").val(1);
				$(".txtHarga").val("0.00");
				$(".txtTotal").val("0.00");
			}
			function BindSatuan(row) {
				var jumlah;
				var harga;
				if($("#ddlJobdesk" + row).val() == 0) {
					$("#txtJumlah" + row).attr("readonly", "readonly");
					$("#txtJumlah" + row).val(1);
					harga = 0;
					jumlah = 1;
				}
				else {
					$("#txtJumlah" + row).removeAttr("readonly");
					harga = $("#ddlJobdesk" + row + " option:selected").attr("harga");
					jumlah = $("#txtJumlah" + row).val();
				}
				$("#txtSatuan" + row).val($("#ddlJobdesk" + row + " option:selected").attr("satuan"));
				$("#txtHarga" + row).val(returnRupiah((jumlah * harga).toString()));
				calculate();
			}
			function BindHarga(row) {
				if($("#txtJumlah" + row).val() == "") $("#txtJumlah" + row).val(1);
				$("#txtHarga" + row).val(returnRupiah(($("#txtJumlah" + row).val() * $("#ddlJobdesk" + row + " option:selected").attr("harga")).toString()));
				calculate();
			}
			function calculate() {
				var total = 0;
				$(".txtHarga").each(function() {
					if($(this).val() == "") $(this).val("0.00");
					total += parseInt($(this).val().replace(/\,/g, ""));
				});
				$("#txtTotal").val(returnRupiah(total.toString()));
			}
		</script>
	</body>
</html>
