<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$PurchaseID = mysql_real_escape_string($_GET['ID']);
		$SupplierID = "";
		$Remarks = "";
		$TransactionDate = "";
		$IsEdit = 0;
		$rowCount = 0;
		$Data = "";
		if($PurchaseID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						TP.PurchaseID,
						DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') AS TransactionDate,
						TP.Remarks,
						SupplierID
					FROM
						transaction_purchase TP
					WHERE
						TP.PurchaseID = $PurchaseID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$PurchaseID = $row['PurchaseID'];
			$SupplierID = $row['SupplierID'];
			$Remarks = $row['Remarks'];
			$TransactionDate = $row['TransactionDate'];
			
			$sql = "SELECT
						PD.PurchaseDetailsID,
						PD.ItemID,
						CONCAT(MI.ItemCode, ' - ', MI.ItemName) ItemName,
						PD.Quantity,
						PD.Price,
						PD.Remarks
					FROM
						transaction_purchasedetails PD
						JOIN master_item MI
							ON MI.ItemID = PD.ItemID
					WHERE
						PD.PurchaseID = $PurchaseID";
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
					array_push($Data, "'".$row['PurchaseDetailsID']."', '".$row['ItemID']."', '".$row['ItemName']."', '".$row['Quantity']."', '".$row['Price']."', '".$row['Remarks']."'");
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Pembelian</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tanggal :
									<input id="hdnPurchaseID" name="hdnPurchaseID" type="hidden" <?php echo 'value="'.$PurchaseID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Supplier :
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlSupplier" id="ddlSupplier" class="form-control-custom" placeholder="Pilih Supplier" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT 
															SupplierID, 
															SupplierName 
														FROM 
															master_supplier 
														WHERE
															CASE
																WHEN '".$_SESSION['UserLogin']."' = 'Admin'
																THEN 1
																WHEN '".$_SESSION['UserLogin']."' = CreatedBy
																THEN 1
																ELSE 0
															END = 1
														ORDER BY 
															SupplierName";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													if($SupplierID == $row['SupplierID']) echo "<option selected value='".$row['SupplierID']."' >".$row['SupplierName']."</option>";
													else echo "<option value='".$row['SupplierID']."' >".$row['SupplierName']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Barang :
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlItem" id="ddlItem" class="form-control-custom" placeholder="Pilih Barang" >
											<option value="" brandid="" selected> </option>
											<?php
												$sql = "SELECT 
															MI.ItemID,
															MI.ItemName,
															MI.ItemCode,
															MI.Price
														FROM 
															master_item MI
														WHERE
															CASE
																WHEN '".$_SESSION['UserLogin']."' = 'Admin'
																THEN 1
																WHEN '".$_SESSION['UserLogin']."' = MI.CreatedBy
																THEN 1
																ELSE 0
															END = 1
														ORDER BY
															MI.ItemName";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['ItemID']."' price='".$row['Price']."' >".$row['ItemCode']." - ".$row['ItemName']."</option>";
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
										<thead style="background-color: black;color:white;height:25px;width:886px;display:block;">
											<td align="center" style="width:30px;">No</td>
											<td align="center" style="width:300px;">Nama Barang</td>
											<td align="center" style="width:75px;">QTY</td>
											<td align="center" style="width:135px;">Harga</td>
											<td align="center" style="width:170px;">Total</td>
											<td align="center" style="width:150px;">Keterangan</td>
											<td style="width: 26px"></td>
										</thead>
										<tbody style="display:block;max-height:232px;height:100%;overflow-y:auto;">
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota' style="width:30px;vertical-align:middle;"></td>
												<td style="width:300px;">
													<input type="text" id="txtItemName" name="txtItemName" class="form-control-custom txtItemName" placeholder="Nama Barang" readonly />
													<input type="hidden" id="hdnItemID" name="hdnItemID" value="0" class="hdnItemID" />
													<input type="hidden" id="hdnPurchaseDetailsID" class="hdnPurchaseDetailsID" name="hdnPurchaseDetailsID" value="0" />
												</td>
												<td style="width:75px;">
													<input type="text" row="" value=1 id="txtQuantity" style="text-align:right;" name="txtQuantity" onkeypress="return isNumberKey(event)" onchange="Calculate();" class="form-control-custom txtQuantity" placeholder="QTY" />
												</td>
												<td style="width:125px;">
													<input type="text" id="txtPrice" value="0.00" name="txtPrice" style="text-align:right;" class="form-control-custom txtPrice" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga" />
												</td>
												<td  style="width:170px;">
													<input type="text" id="txtTotal" name="txtTotal" class="form-control-custom txtTotal" style="text-align:right;" value="0.00" placeholder="Jumlah" readonly />
												</td>
												<td style="width:150px;">
													<input type="text" id="txtRemarksDetail" name="txtRemarksDetail" class="form-control-custom txtRemarksDetail" placeholder="Keterangan" />
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
							<div class="row">
								<div class="col-md-2">
									Grand Total :
								</div>
								<div class="col-md-3">
									<input type="text" id="txtGrandTotal" style="text-align:right;" value="0.00" name="txtGrandTotal" class="form-control-custom" readonly />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">
									Keterangan :
								</div>
								<div class="col-md-3">
									<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" placeholder="Keterangan"><?php echo $Remarks; ?></textarea>
								</div>
							</div>
						</form>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-default" id="btnAdd" style="display:none;" ><i class="fa fa-save "></i> Add</button>&nbsp;&nbsp;
								<button class="btn btn-default" id="btnSave"  onclick="SubmitValidate();" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function BindItem() {
				var i = 1;
				var CurrentItemID = $("#ddlItem").val();
				var CurrentPrice = $("#ddlItem option:selected").attr("price");
				var CurrentItemName = $("#ddlItem option:selected").text();
				var rows = $("#recordnew").val();
				var AddFlag = 1;
				//QTY + 1 if selected item already exists
				for(i=1;i<=rows;i++) {
					if($("#hdnItemID" + i).val() == CurrentItemID) {
						$("#txtQuantity" + i).val((parseFloat($("#txtQuantity" + i).val()) + 1));
						AddFlag = 0;
					}
				}
				if(AddFlag == 1) {
					$("#btnAdd").click();
					$("#hdnItemID" + i).val(CurrentItemID);
					$("#txtItemName" + i).val(CurrentItemName);
					$("#txtPrice" + i).val(returnRupiah(CurrentPrice.toString()));
					$("#txtQuantity" + i).val(1);
					$("#txtTotal" + i).val(CurrentPrice);
				}
				Calculate();
			}
			
			function DeleteRow(row) {
				var count = $("#datainput tbody tr").length - 1;
				$("#num" + row).remove();
				$("#recordnew").val(count-1);
				RegenerateRowNumber();
				Calculate();
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
				$(".hdnItemID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnItemID" + i);
						$(this).attr("name", "hdnItemID" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnPurchaseDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnPurchaseDetailsID" + i);
						$(this).attr("name", "hdnPurchaseDetailsID" + i);
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
				$(".txtPrice").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtPrice" + i);
						$(this).attr("name", "txtPrice" + i);
					}
					i++;
				});
				i = 0;
				$(".txtTotal").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtTotal" + i);
						$(this).attr("name", "txtTotal" + i);
					}
					i++;
				});
				i = 0;
				$(".txtItemName").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtItemName" + i);
						$(this).attr("name", "txtItemName" + i);
					}
					i++;
				});
				i = 0;
				$(".txtRemarksDetail").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtRemarksDetail" + i);
						$(this).attr("name", "txtRemarksDetail" + i);
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
			}
			
			function Calculate() {
				var Total = 0;
				GrandTotal = 0;
				var row = 0;
				var qty = 1;
				var price = 0;
				var disc = 0;
				var isPercentage = 0;
				var i = 0;
				$(".txtQuantity").each(function() {
					if(i != 0) {
						qty = $(this).val();
						row = $(this).attr("row");
						price = $("#txtPrice" + row).val().replace(/\,/g, "");
						if(qty == "") {
							$(this).val(1);
							qty = 1;
						}
						else if(price == "") {
							$("#txtPrice" + row).val("0.00");
							price = 0;
						}
						/*if(isPercentage == true) {
							price = price - ((price * disc)/ 100);
						}
						else {
							price = price - disc;
						}*/
						GrandTotal += parseFloat(qty) * parseFloat(price);
						Total = parseFloat(qty) * parseFloat(price);
						$("#txtTotal" + row).val(returnRupiah(Total.toFixed(2).toString()));
					}
					i++;
				});
				//GrandTotal += parseFloat(deliveryCost);
				$("#txtGrandTotal").val(returnRupiah(GrandTotal.toFixed(2).toString()));
			}
			
			$(document).ready(function () {
				$("#ddlItem").combobox({
					select: function( event, ui ) {
						BindItem();	
						setTimeout(function() {
							$("#ddlItem").next().find("input").val("");
							$("#ddlItem").val("");
						}, 0);					
					}
				});
				$("#ddlItem").next().find("input").click(function() {
					$(this).val("");
				});
				
				$("#ddlSupplier").combobox();
				$("#ddlSupplier").next().find("input").click(function() {
					$(this).val("");
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
					//$("#txtQuantity" + count).addClass("txtQuantity");
					$("#txtRemarksDetail" + count).removeAttr("required");
					$("#recordnew").val(count);
					$("#datainput tbody").animate({
						scrollTop: (25 * count)
					}, "slow");
				});
				$("#btnDelete").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					$('#datainput tr:last').remove();
					$("#recordnew").val(count-1);
					$("#btnAdd").attr("disabled", false)
					Calculate();
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
						$("#hdnPurchaseDetailsID" + count).val(d[0].replace("'", ""));
						$("#hdnItemID" + count).val(d[1].replace("'", ""));
						$("#txtItemName" + count).val(d[2].replace("'", ""));
						$("#txtQuantity" + count).val(d[3].replace("'", ""));
						$("#txtPrice" + count).val(returnRupiah(d[4].replace("'", "")));
						$("#txtRemarksDetail" + count).val(d[5].replace("'", ""));
						$("#record").val(count);
						$("#recordnew").val(count);
					}
					Calculate();
				}
			});
			
			function SubmitValidate() {
				if($("#recordnew").val() > 0) {
					var PassValidate = 1;
					var FirstFocus = 0;
					var SupplierID = $("#ddlSupplier").val();
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
					
					if(SupplierID == 0) {
						$("#ddlSupplier").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						PassValidate = 0;
						if(FirstFocus == 0) $("#ddlSupplier").next().find("input").focus();
						FirstFocus = 1;
					}
					
					if(PassValidate == 0) {
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						return false;
					}
					else SubmitForm("./Transaction/Purchase/Insert.php");
				}
			}
		</script>
	</body>
</html>
