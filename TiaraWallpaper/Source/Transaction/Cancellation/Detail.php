<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$CancellationID = mysql_real_escape_string($_GET['ID']);
		$OutgoingID = 0;
		$CustomerName = "";
		$TransactionDate = "";
		$OutgoingNumber = "";
		$Remarks = "";
		$IsEdit = 0;
		$rowCount = 0;
		$DeliveryCost = 0.00;
		$Data = "";
		$TransactionType = 0;
		if($CancellationID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						TC.CancellationID,
						TC.OutgoingID,
						TC.IncomingID,
						TC.SaleReturnID,
						TC.BuyReturnID,
						CASE
							WHEN TC.OutgoingID <> 0
							THEN 1
							WHEN TC.IncomingID <> 0
							THEN 2
							WHEN TC.SaleReturnID <> 0
							THEN 3
							ELSE 4
						END TransactionType
					FROM
						transaction_cancellation TC
					WHERE
						TC.CancellationID = $CancellationID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$CancellationID = $row['CancellationID'];
			if($row['OutgoingID'] != "0") $OutgoingID = $row['OutgoingID'];
			else if($row['IncomingID'] != "0") $OutgoingID = $row['IncomingID'];
			else if($row['SaleReturnID'] != "0") $OutgoingID = $row['SaleReturnID'];
			else $OutgoingID = $row['BuyReturnID'];
			$TransactionType = $row['TransactionType'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Pembatalan</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-1 labelColumn">
									No Nota :
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlInvoiceNumber" id="ddlInvoiceNumber" class="form-control-custom" placeholder="Pilih Nota" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT 
															OT.OutgoingID InvoiceID, 
															OT.OutgoingNumber InvoiceNumber,
															1 TransactionType
														FROM 
															transaction_outgoing OT 
														WHERE 
															OT.IsCancelled = 0 
														UNION ALL
														SELECT
															TI.IncomingID,
															TI.IncomingNumber,
															2 TransactionType
														FROM
															transaction_incoming TI
														WHERE
															TI.IsCancelled = 0
														UNION ALL
														SELECT
															SR.SaleReturnID,
															SR.SaleReturnNumber,
															3 TransactionType
														FROM
															transaction_salereturn SR
														WHERE
															SR.IsCancelled = 0
														UNION ALL
														SELECT
															BR.BuyReturnID,
															BR.BuyReturnNumber,
															4 TransactionType
														FROM
															transaction_buyreturn BR
														WHERE
															BR.IsCancelled = 0
														ORDER BY
															InvoiceNumber
															";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['InvoiceID']."' transactiontype=".$row['TransactionType']." >".$row['InvoiceNumber']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-1 labelColumn">
									Tanggal :
									<input id="hdnOutgoingID" name="hdnOutgoingID" type="hidden" <?php echo 'value="'.$OutgoingID.'"'; ?> />
									<input id="hdnCancellationID" name="hdnCancellationID" type="hidden" <?php echo 'value="'.$CancellationID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
									<input id="hdnTransactionType" name="hdnTransactionType" type="hidden" <?php echo 'value="'.$TransactionType.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtTransactionDate" disabled name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" />
								</div>
								<div class="col-md-1 labelColumn" id="dvCustomer" >
									Pelanggan:
								</div>
								<div class="col-md-3">
									<input id="txtCustomerName" readonly name="txtCustomerName" type="text" class="form-control-custom" placeholder="Pelanggan" />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									
									<table class="table" style="width:auto;" id="datainput">
										<thead style="background-color: black;color:white;height:25px;width:1080px;display:block;">
											<td align="center" style="width:30px;">No</td>
											<td align="center" style="width:230px;">Nama Barang</td>
											<td align="center" style="width:75px;">QTY</td>
											<td align="center" style="width:135px;" class="tdSalePrice" >Harga Jual</td>
											<td align="center" style="width:135px;" class="tdBuyPrice" >Harga Beli</td>
											<td align="center" style="width:155px;">Diskon</td>
											<td align="center" style="width:170px;">Total</td>
											<td align="center" style="width:150px;" class="tdRemarks" >Keterangan</td>
										</thead>
										<tbody style="display:block;max-height:172px;height:100%;overflow-y:auto;">
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota' style="width:30px;vertical-align:middle;"></td>
												<td style="width:230px;">
													<input type="text" id="txtTypeName" name="txtTypeName" class="form-control-custom txtTypeName" placeholder="Nama Barang" readonly />
													<input type="hidden" id="hdnTypeID" name="hdnTypeID" value="0" class="hdnTypeID" />
													<input type="hidden" id="hdnOutgoingDetailsID" class="hdnOutgoingDetailsID" name="hdnOutgoingDetailsID" value="0" />
													<input type="hidden" id="hdnBuyPrice" name="hdnBuyPrice" class="hdnBuyPrice" value=0 />
													<input type="hidden" id="hdnBatchNumber" name="hdnBatchNumber" class="hdnBatchNumber" value="" />
													<input type="hidden" id="hdnStock" name="hdnStock" class="hdnStock" value="" />
												</td>
												<td style="width:75px;">
													<input type="text" row="" value=1 id="txtQuantity" style="text-align:right;" name="txtQuantity" class="form-control-custom txtQuantity" placeholder="QTY" readonly />
												</td>
												<td style="width:135px;" class="tdSalePrice">
													<input type="text" id="txtSalePrice" value="0.00" name="txtSalePrice" style="text-align:right;" class="form-control-custom" placeholder="Harga Jual" readonly />
												</td>
												<td style="width:135px;" class="tdBuyPrice">
													<input type="text" id="txtBuyPrice" value="0.00" name="txtBuyPrice" style="text-align:right;" class="form-control-custom" placeholder="Harga Beli" readonly />
												</td>
												<td style="width:155px;">
													<input type="text" id="txtDiscount" style="display:inline-block;width: 90px;text-align:right;" value="0" name="txtDiscount" style="text-align:right;" class="form-control-custom" placeholder="Diskon"  readonly /> &nbsp; <input type="checkbox" name="chkIsPercentage" id="chkIsPercentage" style="margin-top:2px;vertical-align:sub;" value=1 checked class="chkIsPercentage" disabled /> (%)
												</td>
												<td  style="width:170px;">
													<input type="text" id="txtTotal" name="txtTotal" class="form-control-custom" style="text-align:right;" value="0.00" placeholder="Jumlah" readonly />
												</td>
												<td  style="width:200px;" class="tdRemarks">
													<input type="text" id="txtRemarksDetail" name="txtRemarksDetail" class="form-control-custom" placeholder="Keterangan" readonly />
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<input type="hidden" id="record" name="record" value=0 />
							<input type="hidden" id="recordnew" name="recordnew" value=0 />
							<br />
							<div class="row" id="rwDeliveryCost" >
								<div class="col-md-2">
									Ongkos Kirim :
								</div>
								<div class="col-md-3">
									<input type="text" id="txtDeliveryCost" style="text-align:right;" <?php echo 'value="'.number_format($DeliveryCost,2,".",",").'"'; ?> name="txtDeliveryCost" class="form-control-custom"  readonly />
								</div>
							</div>
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
									Catatan :
								</div>
								<div class="col-md-4">
									<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" placeholder="Catatan" ><?php echo $Remarks; ?></textarea>
								</div>
							</div>
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
			function Calculate() {
				var Total = 0;
				GrandTotal = 0;
				var row = 0;
				var qty = 1;
				var price = 0;
				var disc = 0;
				var isPercentage = 0;
				var i = 0;
				var deliveryCost = 0;
				$(".txtQuantity").each(function() {
					if(i != 0) {
						qty = $(this).val();
						row = $(this).attr("row");
						if($("#hdnTransactionType").val() == 1 || $("#hdnTransactionType").val() == 3 ) price = $("#txtSalePrice" + row).val().replace(/\,/g, "");
						else if($("#hdnTransactionType").val() == 2 || $("#hdnTransactionType").val() == 4 ) price = $("#txtBuyPrice" + row).val().replace(/\,/g, "");
						disc = $("#txtDiscount" + row).val().replace(/\,/g, "");
						isPercentage = $("#chkIsPercentage" + row).prop('checked');
						if(qty == "") {
							$(this).val(1);
							qty = 1;
						}
						else if(price == "") {
							$("#txtSalePrice" + row).val("0.00");
							price = 0;
						}
						if(isPercentage == true) {
							price = price - ((price * disc)/ 100);
						}
						else {
							price = price - disc;
						}
						GrandTotal += parseFloat(qty) * parseFloat(price);
						Total = parseFloat(qty) * parseFloat(price);
						$("#txtTotal" + row).val(returnRupiah(Total.toFixed(4).toString()));
					}
					i++;
				});
				if ($("#txtDeliveryCost").val() == "") {
					$("#txtDeliveryCost").val(0);
					deliveryCost = 0;
				}
				else {
					deliveryCost = $("#txtDeliveryCost").val().replace(/\,/g, "");
				}
				GrandTotal += parseFloat(deliveryCost);
				$("#txtGrandTotal").val(returnRupiah(GrandTotal.toFixed(2).toString()));
			}
			
			function LoadOutgoingDetails(ID) {
				var currentOutgoingID = 0;
				if(ID == "0") currentOutgoingID = $("#ddlInvoiceNumber").val();
				else currentOutgoingID = ID;
				$("#dvCustomer").html("Pelanggan:");
				$("#hdnOutgoingID").val(currentOutgoingID);
				$(".tdBuyPrice").hide();
				$(".tdSalePrice").show();
				$(".tdRemarks").show();
				$("#rwDeliveryCost").show();
				$.ajax({
					url: "./Transaction/Cancellation/GetOutgoingDetails.php",
					type: "POST",
					data: { OutgoingID : currentOutgoingID },
					dataType: "json",
					success: function(data) {
						var count = 0;
						$('#datainput tbody:last > tr:not(:first)').remove();
						$("#txtTransactionDate").val(data[0].TransactionDate);
						$("#txtDeliveryCost").val(returnRupiah(data[0].DeliveryCost));
						$("#txtCustomerName").val(data[0].CustomerName);
						for(var i = 0; i < data.length; i++) {
							$("#btnAdd").click();
							count++;
							//set values
							$("#nota").text(count);
							$("#hdnOutgoingDetailsID" + count).val(data[i].OutgoingDetailsID);
							$("#hdnTypeID" + count).val(data[i].TypeID);
							$("#txtTypeName" + count).val(data[i].TypeName);
							$("#hdnBatchNumber" + count).val(data[i].BatchNumber);
							$("#txtQuantity" + count).val(data[i].Quantity);
							$("#hdnBuyPrice" + count).val(data[i].BuyPrice);
							$("#txtSalePrice" + count).val(returnRupiah(data[i].SalePrice));
							$("#txtRemarksDetail" + count).val(data[i].Remarks);
							
							if(data[i].IsPercentage == true) {
								$("#txtDiscount" + count).val(data[i].Discount);
								$("#chkIsPercentage" + count).attr("checked", true);
								$("#chkIsPercentage" + count).prop("checked", true);
							}
							else {
								$("#txtDiscount" + count).val(returnRupiah(data[i].Discount));
								$("#chkIsPercentage" + count).attr("checked", false);
								$("#chkIsPercentage" + count).prop("checked", false);
							}
							$("#record").val(count);
							$("#recordnew").val(count);
						}
						Calculate();
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
			
			function LoadIncomingDetails(ID) {
				var currentIncomingID = 0;
				if(ID == "0") currentIncomingID = $("#ddlInvoiceNumber").val();
				else currentIncomingID = ID;
				$("#dvCustomer").html("Supplier:");
				$("#hdnOutgoingID").val(currentIncomingID);
				$(".tdBuyPrice").show();
				$(".tdSalePrice").show();
				$(".tdRemarks").hide();
				$("#rwDeliveryCost").show();
				$.ajax({
					url: "./Transaction/Cancellation/GetIncomingDetails.php",
					type: "POST",
					data: { IncomingID : currentIncomingID },
					dataType: "json",
					success: function(data) {
						var count = 0;
						$('#datainput tbody:last > tr:not(:first)').remove();
						$("#txtTransactionDate").val(data[0].TransactionDate);
						$("#txtDeliveryCost").val(returnRupiah(data[0].DeliveryCost));
						$("#txtCustomerName").val(data[0].SupplierName);
						for(var i = 0; i < data.length; i++) {
							$("#btnAdd").click();
							count++;
							//set values
							$("#nota").text(count);
							$("#hdnOutgoingDetailsID" + count).val(data[i].OutgoingDetailsID);
							$("#hdnTypeID" + count).val(data[i].TypeID);
							$("#txtTypeName" + count).val(data[i].TypeName);
							$("#hdnBatchNumber" + count).val(data[i].BatchNumber);
							$("#txtQuantity" + count).val(data[i].Quantity);
							$("#txtBuyPrice" + count).val(returnRupiah(data[i].BuyPrice));
							$("#txtSalePrice" + count).val(returnRupiah(data[i].SalePrice));
							//$("#txtRemarksDetail" + count).val(data[i].Remarks);
							
							if(data[i].IsPercentage == true) {
								$("#txtDiscount" + count).val(data[i].Discount);
								$("#chkIsPercentage" + count).attr("checked", true);
								$("#chkIsPercentage" + count).prop("checked", true);
							}
							else {
								$("#txtDiscount" + count).val(returnRupiah(data[i].Discount));
								$("#chkIsPercentage" + count).attr("checked", false);
								$("#chkIsPercentage" + count).prop("checked", false);
							}
							$("#record").val(count);
							$("#recordnew").val(count);
						}
						Calculate();
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
			
			function LoadSaleReturnDetails(ID) {
				var currentSaleReturnID = 0;
				if(ID == "0") currentSaleReturnID = $("#ddlInvoiceNumber").val();
				else currentSaleReturnID = ID;
				$("#dvCustomer").html("Pelanggan:");
				$("#hdnOutgoingID").val(currentSaleReturnID);
				$(".tdBuyPrice").hide();
				$(".tdSalePrice").show();
				$(".tdRemarks").hide();
				$("#rwDeliveryCost").hide();
				$.ajax({
					url: "./Transaction/Cancellation/GetSaleReturnDetails.php",
					type: "POST",
					data: { SaleReturnID : currentSaleReturnID },
					dataType: "json",
					success: function(data) {
						var count = 0;
						$('#datainput tbody:last > tr:not(:first)').remove();
						$("#txtTransactionDate").val(data[0].TransactionDate);
						$("#txtDeliveryCost").val(0.00);
						$("#txtCustomerName").val(data[0].CustomerName);
						for(var i = 0; i < data.length; i++) {
							$("#btnAdd").click();
							count++;
							//set values
							$("#nota").text(count);
							$("#hdnOutgoingDetailsID" + count).val(data[i].OutgoingDetailsID);
							$("#hdnTypeID" + count).val(data[i].TypeID);
							$("#txtTypeName" + count).val(data[i].TypeName);
							$("#hdnBatchNumber" + count).val(data[i].BatchNumber);
							$("#txtQuantity" + count).val(data[i].Quantity);
							//$("#txtBuyPrice" + count).val(returnRupiah(data[i].BuyPrice));
							$("#txtSalePrice" + count).val(returnRupiah(data[i].SalePrice));
							//$("#txtRemarksDetail" + count).val(data[i].Remarks);
							
							if(data[i].IsPercentage == true) {
								$("#txtDiscount" + count).val(data[i].Discount);
								$("#chkIsPercentage" + count).attr("checked", true);
								$("#chkIsPercentage" + count).prop("checked", true);
							}
							else {
								$("#txtDiscount" + count).val(returnRupiah(data[i].Discount));
								$("#chkIsPercentage" + count).attr("checked", false);
								$("#chkIsPercentage" + count).prop("checked", false);
							}
							$("#record").val(count);
							$("#recordnew").val(count);
						}
						Calculate();
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
			
			function LoadBuyReturnDetails(ID) {
				var currentBuyReturnID = 0;
				if(ID == "0") currentBuyReturnID = $("#ddlInvoiceNumber").val();
				else currentBuyReturnID = ID;
				$("#dvCustomer").html("Supplier:");
				$("#hdnOutgoingID").val(currentBuyReturnID);
				$(".tdBuyPrice").show();
				$(".tdSalePrice").hide();
				$(".tdRemarks").hide();
				$("#rwDeliveryCost").hide();
				$.ajax({
					url: "./Transaction/Cancellation/GetBuyReturnDetails.php",
					type: "POST",
					data: { BuyReturnID : currentBuyReturnID },
					dataType: "json",
					success: function(data) {
						var count = 0;
						$('#datainput tbody:last > tr:not(:first)').remove();
						$("#txtTransactionDate").val(data[0].TransactionDate);	
						$("#txtDeliveryCost").val(0.00);
						$("#txtCustomerName").val(data[0].SupplierName);
						for(var i = 0; i < data.length; i++) {
							$("#btnAdd").click();
							count++;
							//set values
							$("#nota").text(count);
							$("#hdnOutgoingDetailsID" + count).val(data[i].OutgoingDetailsID);
							$("#hdnTypeID" + count).val(data[i].TypeID);
							$("#txtTypeName" + count).val(data[i].TypeName);
							$("#hdnBatchNumber" + count).val(data[i].BatchNumber);
							$("#txtQuantity" + count).val(data[i].Quantity);
							$("#txtBuyPrice" + count).val(returnRupiah(data[i].BuyPrice));
							//$("#txtSalePrice" + count).val(returnRupiah(data[i].SalePrice));
							//$("#txtRemarksDetail" + count).val(data[i].Remarks);
							
							if(data[i].IsPercentage == true) {
								$("#txtDiscount" + count).val(data[i].Discount);
								$("#chkIsPercentage" + count).attr("checked", true);
								$("#chkIsPercentage" + count).prop("checked", true);
							}
							else {
								$("#txtDiscount" + count).val(returnRupiah(data[i].Discount));
								$("#chkIsPercentage" + count).attr("checked", false);
								$("#chkIsPercentage" + count).prop("checked", false);
							}
							$("#record").val(count);
							$("#recordnew").val(count);
						}
						Calculate();
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
			$(document).ready(function () {
				$("#ddlInvoiceNumber").combobox({
					select: function( event, ui ) {
						if(ui.item.attributes["transactiontype"].value == 1) LoadOutgoingDetails("0");
						else if(ui.item.attributes["transactiontype"].value == 2) LoadIncomingDetails("0");
						else if(ui.item.attributes["transactiontype"].value == 3) LoadSaleReturnDetails("0");
						else if(ui.item.attributes["transactiontype"].value == 4) LoadBuyReturnDetails("0");
						$("#hdnTransactionType").val(ui.item.attributes["transactiontype"].value);
					}
				});
				$("#ddlInvoiceNumber").next().find("input").click(function() {
					$(this).val("");
				});
				$("#btnAdd").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					count++;
					//if(count <= 10) {
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
						$("#txtRemarksDetail" + count).removeAttr("required");
						$("#recordnew").val(count);
						if($("#hdnIsEdit").val() == 0 ) {
							$("#datainput tbody").animate({
								scrollTop: (25 * count)
							}, "slow");
						}						
					//}
					//else {
						//$.notify("Jumlah barang melebihi maksimal!", "error");
					//}
				});
				
				if(parseInt($("#hdnIsEdit").val()) == 1) {
					if($("#hdnTransactionType").val() == "1") {
						LoadOutgoingDetails($("#hdnOutgoingID").val());
					}
					else if($("#hdnTransactionType").val() == "2") {
						LoadIncomingDetails($("#hdnOutgoingID").val());
					}
					else if($("#hdnTransactionType").val() == "3") {
						LoadSaleReturnDetails($("#hdnOutgoingID").val());
					}
					else if($("#hdnTransactionType").val() == "4") {
						LoadBuyReturnDetails($("#hdnOutgoingID").val());
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
					else {
						SubmitForm("./Transaction/Cancellation/Insert.php");
					}
				}
			}
		</script>
	</body>
</html>
