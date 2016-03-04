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
						TF.TransaksiID,
						DATE_FORMAT(TF.Tanggal, '%d-%m-%Y')
					FROM
						transaksi_fee TF
					WHERE
						TF.TransaksiID = $Id";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Tanggal = $row[1];
				
				$sql = "SELECT 
						DetailID,
						Keterangan,
						Jumlah,
						Instfee
					FROM
						transaksi_rincifee
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
				<h2>Institusional Fee,SPV dan Lembur</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Institusional Fee,SPV dan Lembur</strong>  
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
												<td>Keterangan</td>
												<td>Jumlah</td>
												<td>Inst Fee</td>
												<td>Total</td>
												
												
											</thead>
											<tbody>
												<tr id='' align='center' style='display:none;'>
													<td id='nota' name='nota'></td>
													<td>
														<input type="hidden" id="hdnDetailID" name="hdnDetailID" value="0" />
														<input type="text" id="txtKeterangan" name="txtKeterangan" class="form-control" />
													</td>
													<td>
														<input type="text" id="txtJumlah" style="text-align:right;" name="txtJumlah" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" onchange="calculate()" class="form-control" value="0.00" /> 
														
													</td>
													<td>
														<input type="text" id="txtInst" style="text-align:right;" name="txtInst" onkeyup="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event)" onfocus="clearFormat(this.id, this.value)"  onchange="calculate()" class="form-control txtInst" value="0" /> 
													</td>
													<td>
														<input type="text" id="txtTotal" style="text-align:right;" name="txtTotal" readonly class="form-control txtTotal" value="0.00" />
													</td>
												</tr>
												<tr id='num1' align='center'>
													<td id='nota1' name='nota1'  style='width:20px;'>1</td>
													<td>
														<input type="hidden" id="hdnDetailID1" name="hdnDetailID1" value="0" />
														<input type="text" id="txtKeterangan1" name="txtKeterangan1" class="form-control" />
													</td>
													<td>
														<input type="text" id="txtJumlah1" style="text-align:right;" name="txtJumlah1" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" onchange="calculate()" class="form-control txtJumlah" value="0.00" />
														
													</td>
													<td>
														<input type="text" id="txtInst1" style="text-align:right;" name="txtInst1" onkeyup="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event)" onfocus="clearFormat(this.id, this.value)"  onchange="calculate()" class="form-control txtInst" value="0" />
													</td>
													<td>
														<input type="text" id="txtTotal1" style="text-align:right;" name="txtTotal1" readonly class="form-control txtTotal" value="0.00" />
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
												<input type="text" name="txtGrandTotal" style="text-align:right;" id="txtGrandTotal" class="form-control col-md-3" value="0.00" readonly />
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
					$("#txtKeterangan" + count).attr("required", "");
					$("#txtJumlah" + count).attr("required", "");
					$("#txtInst" + count).attr("required", "");
					$("#txtTotal" + count).attr("required", "");
					$("#txtJumlah" + count).addClass("txtJumlah");
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
							$("#txtKeterangan" + count).val(d[1].replace("'", ""));
							$("#txtJumlah" + count).val(returnRupiah(d[2].replace("'", "")));
							$("#txtInst" + count).val(d[3].replace("'", ""));
							//console.log(d[3]);
							
							$("#record").val(count);
							$("#recordnew").val(count);
						}
						calculate();
					}
				}				
			});
			function SubmitValidate() {
				$("#hdnTanggal").val($("#txtTanggal").val());
				SubmitForm("./Keuangan/FeeSpv/Insert.php");
			}	
			function calculate() {
				var total = 0;
				var i = 0;
				var totalTemp;
				var Inst;
				$(".txtJumlah").each(function() {
					i++;
					if($(this).val() == "") $(this).val("0");
					if($("#txtInst" + i).val() == "") $("#txtInst" + i).val(0);
					if($("#txtInst" + i).val() > 100) $("#txtInst" + i).val(100);
					Inst = parseInt($("#txtInst" + i).val());
					totalTemp = (parseInt($(this).val().replace(/\,/g, "")) * Inst)/100;
					$("#txtTotal" + i).val(returnRupiah(totalTemp.toString()));
					total += (parseInt($(this).val().replace(/\,/g, "")) * Inst)/100;
				});
				$("#txtGrandTotal").val(returnRupiah(total.toString()));
			}
		</script>
	</body>
</html>
