<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$IncomingID = mysql_real_escape_string($_GET['ID']);
		$IncomingNumber = "";
		$SupplierID = "";
		$Remarks = "";
		$TransactionDate = "";
		$IsEdit = 0;
		$rowCount = 0;
		$DeliveryCost = 0.00;
		$Data = "";
		if($IncomingID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						IT.IncomingID,
						DATE_FORMAT(IT.TransactionDate, '%d-%m-%Y') AS TransactionDate
					FROM
						transaction_incoming IT
					WHERE
						IT.IncomingID = $IncomingID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$IncomingID = $row['IncomingID'];
			$TransactionDate = $row['TransactionDate'];
			
			$sql = "SELECT
						ITD.IncomingDetailsID,
						ITD.MaterialID,
						ITD.Quantity,
						MM.MaterialName
					FROM
						transaction_incomingdetails ITD
						JOIN master_material MM
							ON MM.MaterialID = ITD.MaterialID
					WHERE
						ITD.IncomingID = $IncomingID";
			if(!$result = mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$rowCount = mysql_num_rows($result);
			if($rowCount > 0) {
				//$DetailID = array();
				$Data = array();
				while($row = mysql_fetch_array($result)) {
					//array_push($DetailID, $row[0]);
					array_push($Data, "'".$row['IncomingDetailsID']."', '".$row['MaterialID']."', '".$row['MaterialName']."', '".$row['SupplierName']."', '".$row['Quantity']."', '".$row['Remarks']."'");
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
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Barang Masuk</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-1 labelColumn">
									Tanggal :
									<input id="hdnIncomingID" name="hdnIncomingID" type="hidden" <?php echo 'value="'.$IncomingID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
								<div class="col-md-1 labelColumn" >
									Material:
								</div>
								<div class="col-md-3" >
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlMaterial" id="ddlMaterial" class="form-control-custom" placeholder="Pilih Material" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT MaterialID, MaterialName, SalePrice FROM master_material";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['MaterialID']."' saleprice=".$row['SalePrice']." >".$row['MaterialName']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<table class="table" style="width:auto;" id="datainput">
										<thead style="background-color: black;color:white;height:25px;width:720px;display:block;">
											<td align="center" style="width:30px;">No</td>
											<td align="center" style="width:200px;">Material</td>
											<td align="center" style="width:200px;">Supplier</td>
											<td align="center" style="width:90px;">Jumlah</td>
											<td align="center" style="width:175px;">Keterangan</td>
											<td style="width: 25px"></td>
										</thead>
										<tbody style="display:block;max-height:232px;height:100%;overflow-y:auto;">
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota' style="width:30px;vertical-align:middle;"></td>
												<td style="width:200px;">
													<input type="text" id="txtMaterialName" name="txtMaterialName" class="form-control-custom txtMaterialName" placeholder="Nama Barang" readonly />
													<input type="hidden" id="hdnMaterialID" name="hdnMaterialID" value="0" class="hdnMaterialID" />
													<input type="hidden" id="hdnIncomingDetailsID" class="hdnIncomingDetailsID" name="hdnIncomingDetailsID" value="0" />
												</td>
												<td style="width:200px;">
													<input type="text" row="" id="txtSupplierName" name="txtSupplierName" class="form-control-custom txtSupplierName" placeholder="Supplier" onclick="this.select();" />
												</td>
												<td style="width:90px;">
													<input type="text" row="" value=1 id="txtQuantity" style="text-align:right;" name="txtQuantity" onclick="this.select();" onkeypress="return isNumberKey(event)" class="form-control-custom txtQuantity" placeholder="QTY" />
												</td>
												<td style="width:175px;">
													<input type="text" row="" id="txtRemarks" name="txtRemarks" class="form-control-custom txtRemarks" placeholder="Keterangan" onclick="this.select();" />
												</td>
												<td style="vertical-align:middle;">
													<i class="fa fa-close btnDelete" style="cursor:pointer;" acronym title="Hapus Data" onclick="DeleteRow(this.getAttribute('row'))"></i>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<input type="hidden" id="record" name="record" value=0 />
							<input type="hidden" id="recordnew" name="recordnew" value=0 />
							<br />
						</form>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-default" id="btnAdd" style="display:none;" ><i class="fa fa-save "></i> Add</button>&nbsp;&nbsp;
								<button class="btn btn-default" id="btnSave"  onclick="SubmitValidate();" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function MaterialSelect() {
				var i = 1;
				var CurrentMaterialID = $("#ddlMaterial").val();
				var CurrentMaterialName = $("#ddlMaterial option:selected").text();
				var rows = $("#recordnew").val();
				var AddFlag = 1;
				//QTY + 1 if selected item already exists
				for(i=1;i<=rows;i++) {
					/*if($("#hdnMaterialID" + i).val() == CurrentMaterialID) {
						$("#txtQuantity" + i).val((parseFloat($("#txtQuantity" + i).val()) + 1));
						AddFlag = 0;
					}*/
				}
				if(AddFlag == 1) {
					$("#btnAdd").click();
					$("#hdnMaterialID" + i).val(CurrentMaterialID);
					$("#txtMaterialName" + i).val(CurrentMaterialName);
					$("#txtQuantity" + i).val(1);
				}
			}
			
			function DeleteRow(row) {
				var count = $("#datainput tbody tr").length - 1;
				$("#num" + row).remove();
				$("#recordnew").val(count-1);
				RegenerateRowNumber();
			}
			
			function RegenerateRowNumber() {
				var i = 0;
				$(".nota").each(function() {
					if(i != 0) {
						$(this).html(i);
						$(this).attr("id", "nota" + i);
						$(this).attr("name", "nota" + i);
					}
					i++;
				});
				i = 0;
				$(".num").each(function() {
					if(i != 0) {
						$(this).attr("id", "num" + i);
						$(this).attr("name", "num" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnMaterialID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnMaterialID" + i);
						$(this).attr("name", "hdnMaterialID" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnIncomingDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnIncomingDetailsID" + i);
						$(this).attr("name", "hdnIncomingDetailsID" + i);
					}
					i++;
				});
				i = 0;
				$(".txtQuantity").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtQuantity" + i);
						$(this).attr("name", "txtQuantity" + i);
						$(this).attr("row", i);
					}
					i++;
				});
				i = 0;
				$(".txtMaterialName").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtMaterialName" + i);
						$(this).attr("name", "txtMaterialName" + i);
					}
					i++;
				});
				i = 0;
				$(".btnDelete").each(function() {
					if(i != 0) {
						$(this).attr("row", i);
					}
					i++;
				});
				i = 0;
				$(".txtSupplierName").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtSupplierName" + i);
						$(this).attr("name", "txtSupplierName" + i);
					}
					i++;
				});
				i = 0;
				$(".txtRemarks").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtRemarks" + i);
						$(this).attr("name", "txtRemarks" + i);
					}
					i++;
				});
			}
			
			$(document).ready(function () {
				$("#ddlMaterial").combobox({
					select: function(event, ui) {
						MaterialSelect();
						setTimeout(function() {
							$("#ddlMaterial").next().find("input").val("");
							$("#ddlMaterial").val("");
						}, 0);
					}
				});
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
					$clone.find("input, select, i").each(function(){
						//var temp = $(this).attr("id") + (count - 1);
						$(this).attr({
							id: $(this).attr("id") + count,
							name: $(this).attr("name") + count,
							row: count,
							required: ""
						});				
						//$(this).val($("#" + temp).val());
					});
					$("#datainput tbody").append($clone);
					$("#txtRemarks" + count).removeAttr("required");
					$("#recordnew").val(count);
					if($("#hdnIsEdit").val() == 0 ) {
						$("#datainput tbody").animate({
							scrollTop: (25 * count)
						}, "slow");
					}
				});
				$("#btnDelete").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					$('#datainput tr:last').remove();
					$("#recordnew").val(count-1);
					$("#btnAdd").attr("disabled", false)
				});
				
				if(parseInt($("#hdnRow").val()) > 0) {
					var data = $("#hdnData").val();
					var type = data.split("|");
					var row = type.length;
					var count = 0;
					$('#datainput tbody:last > tr:not(:first)').remove();
					for(var i=0; i<row; i++) {
						$("#btnAdd").click();
						count++;
						//set values
						var d = type[i].split("', '");
						$("#nota").text(count);
						$("#hdnIncomingDetailsID" + count).val(d[0].replace("'", ""));
						$("#hdnMaterialID" + count).val(d[1].replace("'", ""));
						$("#txtMaterialName" + count).val(d[2].replace("'", ""));
						$("#txtSupplierName" + count).val(d[3].replace("'", ""));
						$("#txtQuantity" + count).val(d[4].replace("'", ""));
						$("#txtRemarks" + count).val(d[5].replace("'", ""));
						$("#record").val(count);
						$("#recordnew").val(count);
					}
				}
			});
			
			function SubmitValidate() {
				if($("#recordnew").val() > 0) {
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
					
					if(PassValidate == 0) {
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						return false;
					}
					else SubmitForm("./Transaction/IncomingMaterial/Insert.php");
				}
			}
		</script>
	</body>
</html>
