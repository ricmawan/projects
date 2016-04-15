<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$OutgoingID = mysql_real_escape_string($_GET['ID']);
		$SalesID = "";
		$CustomerID = "";
		$TransactionDate = "";
		$OutgoingNumber = "";
		$Remarks = "";
		$IsEdit = 0;
		$rowCount = 0;
		$DeliveryCost = 0.00;
		$Data = "";
		if($OutgoingID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
					OT.OutgoingID,
					OT.OutgoingNumber,
					OT.SalesID,
					OT.CustomerID,
					OT.Remarks,
					OT.DeliveryCost,
					DATE_FORMAT(OT.TransactionDate, '%d-%m-%Y') AS TransactionDate
				FROM
					transaction_outgoing OT
				WHERE
					OT.OutgoingID = $OutgoingID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$OutgoingID = $row['OutgoingID'];
			$OutgoingNumber = $row['OutgoingNumber'];
			$SalesID = $row['SalesID'];
			$CustomerID = $row['CustomerID'];
			$Remarks = $row['Remarks'];
			$DeliveryCost = $row['DeliveryCost'];
			$TransactionDate = $row['TransactionDate'];
			
			$sql = "SELECT
						OTD.OutgoingDetailsID,
						OTD.TypeID,
						OTD.Quantity,
						OTD.BuyPrice,
						OTD.SalePrice,
						OTD.BatchNumber,
						OTD.Discount,
						CONCAT(MB.BrandName, ' ', MT.TypeName, ' - ', OTD.BatchNumber) AS TypeName,
						OTD.IsPercentage,
						OTD.Remarks,
						OTD.Quantity + (IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(BO.Quantity, 0) + IFNULL(SO.Quantity, 0)) Stock
					FROM
						transaction_outgoingdetails OTD
						JOIN master_type MT
							ON MT.TypeID = OTD.TypeID
						JOIN master_brand MB
							ON MB.BrandID = MT.BrandID
						LEFT JOIN
						(
							SELECT
								TypeID,
								TRIM(BatchNumber) BatchNumber,
								SUM(SA.Quantity) Quantity,
								BuyPrice,
								SalePrice
							FROM
							(
								SELECT
									TypeID,
									TRIM(BatchNumber) BatchNumber,
									SUM(Quantity) Quantity,
									CASE
										WHEN IsPercentage = 1
										THEN (BuyPrice - ((BuyPrice * Discount) / 100))
										ELSE (BuyPrice - Discount)
									END AS BuyPrice,
									SalePrice,
									CreatedDate
								FROM
									transaction_firststockdetails
								GROUP BY
									TypeID,
									BatchNumber,
									BuyPrice,
									SalePrice,
									CreatedDate,
									Discount
								UNION
								SELECT
									TypeID,
									TRIM(BatchNumber) BatchNumber,
									SUM(Quantity) Quantity,
									CASE
										WHEN IsPercentage = 1
										THEN (BuyPrice - ((BuyPrice * Discount) / 100))
										ELSE (BuyPrice - Discount)
									END AS BuyPrice,
									SalePrice,
									CreatedDate
								FROM
									transaction_incomingdetails
								GROUP BY
									TypeID,
									BatchNumber,
									BuyPrice,
									SalePrice,
									CreatedDate,
									Discount
								ORDER BY
									CreatedDate DESC
							)SA
							GROUP BY
								TypeID,
								BatchNumber
						)FS
							ON FS.TypeID = MT.TypeID
							AND FS.BatchNumber = OTD.BatchNumber
						LEFT JOIN
						(
							SELECT
								OTD.TypeID,
								TRIM(OTD.BatchNumber) BatchNumber,
								SUM(OTD.Quantity) Quantity
							FROM
								transaction_outgoingdetails OTD
								JOIN transaction_outgoing OT
									ON OT.OutgoingID = OTD.OutgoingID
							WHERE
								OT.IsCancelled = 0
							GROUP BY
								OTD.TypeID,
								OTD.BatchNumber
						)TOD
							ON TOD.TypeID = MT.TypeID
							AND TOD.BatchNumber = FS.BatchNumber
						LEFT JOIN
						(
							SELECT
								TypeID,
								TRIM(BatchNumber) BatchNumber,
								SUM(Quantity) Quantity
							FROM
								transaction_buyreturndetails
							GROUP BY
								TypeID,
								BatchNumber
						)BR
							ON BR.TypeID = MT.TypeID
							AND BR.BatchNumber = FS.BatchNumber
						LEFT JOIN
						(
							SELECT
								TypeID,
								TRIM(BatchNumber) BatchNumber,
								SUM(Quantity) Quantity
							FROM
								transaction_salereturndetails
							GROUP BY
								TypeID,
								BatchNumber
						)SR
							ON SR.TypeID = MT.TypeID
							AND SR.BatchNumber = FS.BatchNumber
						LEFT JOIN
						(
							SELECT
								BOD.TypeID,
								TRIM(BOD.BatchNumber) BatchNumber,
								SUM(BOD.Quantity) Quantity
							FROM
								transaction_booking BO
								JOIN transaction_bookingdetails BOD
									ON BO.BookingID = BOD.BookingID
							WHERE
								BO.BookingStatusID = 1
							GROUP BY
								BOD.TypeID,
								BOD.BatchNumber
						)BO
							ON BO.TypeID = MT.TypeID
							AND BO.BatchNumber = FS.BatchNumber
						LEFT JOIN
						(
							SELECT
								SOD.TypeID,
								TRIM(SOD.BatchNumber) BatchNumber,
								SUM(
										CASE
											WHEN SOD.FromQty > SOD.ToQty
											THEN -(SOD.FromQty - SOD.ToQty)
											ELSE (SOD.ToQty - SOD.FromQty)
										END
									) Quantity
							FROM
								transaction_stockopname SO
								JOIN transaction_stockopnamedetails SOD
									ON SO.StockOpnameID = SOD.StockOpnameID
							GROUP BY
								SOD.TypeID,
								SOD.BatchNumber
						)SO
							ON SO.TypeID = MT.TypeID
							AND SO.BatchNumber = FS.BatchNumber
					WHERE
						OTD.OutgoingID = $OutgoingID";
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
					array_push($Data, "'".$row['OutgoingDetailsID']."', '".$row['TypeID']."', '".$row['TypeName']."', '".$row['BatchNumber']."', '".$row['Quantity']."', '".$row['BuyPrice']."', '".$row['SalePrice']."', '".$row['Discount']."', '".$row['Remarks']."', '".$row['IsPercentage']."', '".$row['Stock']."'");
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Barang Keluar</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-1 labelColumn">
									No Nota :
								</div>
								<div class="col-md-3">
									<input type="text" id="txtOutgoingNumber" name="txtOutgoingNumber" class="form-control-custom" readonly <?php echo 'value="'.$OutgoingNumber.'"'; ?> />
								</div>
								<!--<div class="col-md-1 labelColumn">
									Sales:
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlSales" id="ddlSales" class="form-control-custom" placeholder="Pilih Sales" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT SalesID, SalesName FROM master_sales";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													if($SalesID == $row['SalesID']) echo "<option selected value='".$row['SalesID']."' >".$row['SalesName']."</option>";
													else echo "<option value='".$row['SalesID']."' >".$row['SalesName']."</option>";
												}
											?>
										</select>
									</div>
								</div>-->
							</div>
							<br />
							<div class="row">
								<div class="col-md-1 labelColumn">
									Tanggal :
									<input id="hdnOutgoingID" name="hdnOutgoingID" type="hidden" <?php echo 'value="'.$OutgoingID.'"'; ?> />
									<input id="hdnSalesID" name="hdnSalesID" type="hidden" <?php echo 'value="'.$SalesID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" onchange="GetInvoiceNumber(this.value);" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
								<div class="col-md-1 labelColumn">
									Pelanggan:
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlCustomer" id="ddlCustomer" class="form-control-custom" placeholder="Pilih Pelanggan" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT CustomerID, CustomerName, Address, SalesID FROM master_customer";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													if($CustomerID == $row['CustomerID']) echo "<option selected value='".$row['CustomerID']."' salesid='".$row['SalesID']."' >".$row['CustomerName']." - ".$row['Address']."</option>";
													else echo "<option value='".$row['CustomerID']."' salesid='".$row['SalesID']."' >".$row['CustomerName']." - ".$row['Address']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-1 labelColumn">
									Merek :
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlBrand" id="ddlBrand" class="form-control-custom" placeholder="Pilih Merek" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT BrandID, BrandName FROM master_brand";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['BrandID']."' >".$row['BrandName']."</option>";
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-1 labelColumn">
									Tipe :
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlType" id="ddlType" class="form-control-custom" placeholder="Pilih Tipe" >
											<option value="" brandid="" selected> </option>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									
									<table class="table" style="width:auto;" id="datainput">
										<thead style="background-color: black;color:white;height:25px;width:1020px;display:block;">
											<td align="center" style="width:30px;">No</td>
											<td align="center" style="width:230px;">Nama Barang</td>
											<td align="center" style="width:75px;">QTY</td>
											<td align="center" style="width:135px;">Harga Jual</td>
											<td align="center" style="width:155px;">Diskon</td>
											<td align="center" style="width:170px;">Total</td>
											<td align="center" style="width:200px;">Keterangan</td>
											<td style="width: 26px"></td>
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
													<input type="text" row="" value=1 id="txtQuantity" style="text-align:right;" name="txtQuantity" onkeypress="return isNumberKey(event)" onchange="ValidateQty(this.getAttribute('row'));" class="form-control-custom txtQuantity" placeholder="QTY"/>
												</td>
												<td style="width:135px;">
													<input type="text" id="txtSalePrice" value="0.00" name="txtSalePrice" style="text-align:right;" class="form-control-custom txtSalePrice" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga Jual"/>
												</td>
												<td style="width:155px;">
													<input type="text" id="txtDiscount" style="display:inline-block;width: 90px;text-align:right;" value="0" name="txtDiscount" style="text-align:right;" class="form-control-custom txtDiscount" onchange="ValidateDiscount(this.getAttribute('row'))" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" placeholder="Diskon" /> &nbsp; <input type="checkbox" name="chkIsPercentage" id="chkIsPercentage" style="margin-top:2px;vertical-align:sub;" onchange="ValidateDiscount(this.getAttribute('row'))" value=1 checked class="chkIsPercentage" /> (%)
												</td>
												<td  style="width:170px;">
													<input type="text" id="txtTotal" name="txtTotal" class="form-control-custom txtTotal" style="text-align:right;" value="0.00" placeholder="Jumlah" readonly />
												</td>
												<td  style="width:200px;">
													<input type="text" id="txtRemarksDetail" name="txtRemarksDetail" class="form-control-custom txtRemarksDetail" placeholder="Keterangan" maxlength=50 />
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
									Ongkos Kirim :
								</div>
								<div class="col-md-3">
									<input type="text" id="txtDeliveryCost" style="text-align:right;" <?php echo 'value="'.number_format($DeliveryCost,2,".",",").'"'; ?> name="txtDeliveryCost" class="form-control-custom" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
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
									<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" placeholder="Catatan"><?php echo $Remarks; ?></textarea>
								</div>
							</div>
							<br />
						</form>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-default" id="btnAdd" style="display:none;" ><i class="fa fa-save "></i> Add</button>&nbsp;&nbsp;								
								<button class="btn btn-default" id="btnPrintInvoice"  onclick="PrintInvoice();" ><i class="fa fa-print "></i> Cetak Nota</button>&nbsp;&nbsp;
								<button class="btn btn-default" id="btnPrintShipment"  onclick="PrintShipment();" ><i class="fa fa-print "></i> Cetak Surat Jalan</button>&nbsp;&nbsp;
								<button class="btn btn-default" id="btnSave"  onclick="SubmitValidate();" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function BindSales() {
				$("#hdnSalesID").val($("#ddlCustomer option:selected").attr("salesid"));
			}
			
			function BindType() {
				$("#ddlType option").each(function() {
					$(this).remove();
				});
				$("#ddlType").append('<option value="" brandid="" selected> </option>');
				$("#ddlType").val("");
				$("#ddlType").next().find("input").val("");
				$.ajax({
					url: "./Transaction/Outgoing/GetAvailableType.php",
					type: "POST",
					data: { BrandID : $("#ddlBrand").val() },
					dataType: "json",
					success: function(data) {
						$.each(data, function(key, value) {
							$("#ddlType").append("<option value='" + value.TypeID + "' buyprice='" + value.BuyPrice + "' saleprice='" + value.SalePrice + "' stock='" + value.Stock + "' batchnumber='" + value.BatchNumber + "' brandid='" + value.BrandID + "' >" + value.BrandName + " " + value.TypeName + " - " + value.BatchNumber + "</option>");
						});
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
			
			function BindTypeList() {
				var i = 1;
				var CurrentTypeID = $("#ddlType").val();
				var CurrentBuyPrice = $("#ddlType option:selected").attr("buyprice");
				var CurrentSalePrice = $("#ddlType option:selected").attr("saleprice");
				var CurrentBatchNumber = $("#ddlType option:selected").attr("batchnumber");
				var CurrentStock = $("#ddlType option:selected").attr("stock");
				var CurrentTypeName = $("#ddlType option:selected").text();
				var rows = $("#recordnew").val();
				var AddFlag = 1;
				//QTY + 1 if selected item already exists
				for(i=1;i<=rows;i++) {
					if($("#hdnTypeID" + i).val() == CurrentTypeID && $("#hdnBatchNumber" + i).val() == CurrentBatchNumber) {
						if((parseInt($("#txtQuantity" + i).val()) + 1) > CurrentStock) {
							$.notify("Sisa stok yang ada : " +CurrentStock, "error");
							$("#txtQuantity" + i).val(CurrentStock);
						}
						else {
							$("#txtQuantity" + i).val((parseInt($("#txtQuantity" + i).val()) + 1));
						}
						AddFlag = 0;
					}
				}
				if(AddFlag == 1) {
					$("#btnAdd").click();
					$("#hdnTypeID" + i).val(CurrentTypeID);
					$("#txtTypeName" + i).val(CurrentTypeName);
					$("#hdnBuyPrice" + i).val(returnRupiah(CurrentBuyPrice.toString()));
					$("#hdnBatchNumber" + i).val(CurrentBatchNumber.toString());
					$("#hdnStock" + i).val(CurrentStock.toString());
					$("#txtSalePrice" + i).val(returnRupiah(CurrentSalePrice.toString()));
					$("#txtQuantity" + i).val(1);
					$("#txtTotal" + i).val(CurrentSalePrice);
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
				$(".hdnTypeID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnTypeID" + i);
						$(this).attr("name", "hdnTypeID" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnStock").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnStock" + i);
						$(this).attr("name", "hdnStock" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnOutgoingDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnOutgoingDetailsID" + i);
						$(this).attr("name", "hdnOutgoingDetailsID" + i);
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
				$(".hdnBatchNumber").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnBatchNumber" + i);
						$(this).attr("name", "hdnBatchNumber" + i);
						$(this).attr("row", i);
					}
					i++;
				});
				i = 0;
				$(".txtSalePrice").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtSalePrice" + i);
						$(this).attr("name", "txtSalePrice" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnBuyPrice").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnBuyPrice" + i);
						$(this).attr("name", "hdnBuyPrice" + i);
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
				$(".txtTypeName").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtTypeName" + i);
						$(this).attr("name", "txtTypeName" + i);
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
				$(".txtDiscount").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtDiscount" + i);
						$(this).attr("name", "txtDiscount" + i);
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
				$(".chkIsPercentage").each(function() {
					if(i != 0) {
						$(this).attr("id", "chkIsPercentage" + i);
						$(this).attr("name", "chkIsPercentage" + i);
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
				var deliveryCost = 0;
				$(".txtQuantity").each(function() {
					if(i != 0) {
						qty = $(this).val();
						row = $(this).attr("row");
						price = $("#txtSalePrice" + row).val().replace(/\,/g, "");
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
						$("#txtTotal" + row).val(returnRupiah(Total.toString()));
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
				$("#txtGrandTotal").val(returnRupiah(GrandTotal.toString()));
			}
			
			function ValidateDiscount(row) {
				var IsPercentage = $("#chkIsPercentage" + row).prop('checked');
				var Discount = $("#txtDiscount" + row);
				if(IsPercentage == true) {
					Discount.val(minmax(Discount.val().replace(/\,/g, "").replace(/\.00/g, ""), 0, 100));
				}
				else {
					convertRupiah("txtDiscount" + row, Discount.val());
				}
				Calculate();
			}
			
			$(document).ready(function () {
				$("#ddlBrand").combobox({
					select: function( event, ui ) {
						BindType();						
					}
				});
				
				//$("#ddlSales").combobox();
				$("#ddlCustomer").combobox({
					select: function( event, ui ) {
						BindSales();						
					}
				});
				$("#ddlType").combobox({
					select: function(event, ui) {
						BindTypeList();
						setTimeout(function() {
							$("#ddlType").next().find("input").val("");
							$("#ddlType").val("");
						}, 0);
					}
				});
				$("#btnAdd").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					count++;
					if(count <= 10) {
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
					}
					else {
						$.notify("Jumlah barang melebihi maksimal!", "error");
					}
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
						$("#hdnOutgoingDetailsID" + count).val(d[0].replace("'", ""));
						$("#hdnTypeID" + count).val(d[1].replace("'", ""));
						$("#txtTypeName" + count).val(d[2].replace("'", ""));
						$("#hdnBatchNumber" + count).val(d[3].replace("'", ""));
						$("#txtQuantity" + count).val(d[4].replace("'", ""));
						$("#hdnBuyPrice" + count).val(d[5].replace("'", ""));
						$("#txtSalePrice" + count).val(returnRupiah(d[6].replace("'", "")));
						$("#txtRemarksDetail" + count).val(d[8].replace("'", ""));
						$("#hdnStock" + count).val(d[10].replace("'", ""));
						
						if(d[9].replace("'", "") == true) {
							$("#txtDiscount" + count).val(d[7].replace("'", ""));
							$("#chkIsPercentage" + count).attr("checked", true);
							$("#chkIsPercentage" + count).prop("checked", true);
						}
						else {
							$("#txtDiscount" + count).val(returnRupiah(d[7].replace("'", "")));
							$("#chkIsPercentage" + count).attr("checked", false);
							$("#chkIsPercentage" + count).prop("checked", false);
						}
						$("#record").val(count);
						$("#recordnew").val(count);
					}
					Calculate();
				}
			});
			
			function PrintInvoice() {
				if($("#hdnOutgoingID").val() == 0) {
					$.notify("Tekan Simpan terlebih dahulu!", "error");
					return false;
				}
				else {
					$("#loading").show();
					$.ajax({
						url: "./Transaction/Outgoing/PrintInvoice.php",
						type: "POST",
						data: $("#PostForm").serialize(),
						dataType: "json",
						success: function(data) {
							$("html, body").animate({
								scrollTop: 0
							}, "slow");
							$("#loading").hide();
						},
						error: function(data) {
							$("#loading").hide();
							$.notify("Koneksi gagal", "error");
					
						}
					});
				}
			}
			
			function PrintShipment() {
				if($("#hdnOutgoingID").val() == 0) {
					$.notify("Tekan Simpan terlebih dahulu!", "error");
					return false;
				}
				else {
					var ID = $("#hdnId").val();
					$("#loading").show();
					$.ajax({
						url: "./Transaction/Outgoing/PrintShipment.php",
						type: "POST",
						data: $("#PostForm").serialize(),
						dataType: "json",
						success: function(data) {
							$("html, body").animate({
								scrollTop: 0
							}, "slow");
							$("#loading").hide();
						},
						error: function(data) {
							$("#loading").hide();
							$.notify("Koneksi gagal", "error");
						}
					});
				}
			}
			
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
					
					if($("#ddlCustomer").val() == "") {
						PassValidate = 0;
						$("#ddlCustomer").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#ddlCustomer").next().find("input").focus();
						FirstFocus = 1;
					}
					if(PassValidate == 0) {
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						return false;
					}
					else {
						$.ajax({
							url: "./Transaction/Outgoing/Insert.php",
							type: "POST",
							data: $("#PostForm").serialize(),
							dataType: "json",
							success: function(data) {
								if(data.FailedFlag == '0') {
									$.notify(data.Message, "success");
									$("#hdnOutgoingID").val(data.ID);
									$("#hdnIsEdit").val(1);
								}
								else {
									$("#loading").hide();
									$.notify(data.Message, "error");					
								}
							},
							error: function(data) {
								$("#loading").hide();
								$.notify("Terjadi kesalahan sistem!", "error");
							}
						});
					}
				}
			}
			
			function ValidateQty(row) {
				var currentQty = $("#txtQuantity" + row).val();
				var currentStock = $("#hdnStock" +  row).val();
				if(parseInt(currentQty) > parseInt(currentStock)) {
					$.notify("Sisa stok yang ada : " + currentStock, "error");
					$("#txtQuantity" + row).val(currentStock);
				}
				Calculate();
			}
			function GetInvoiceNumber(SelectedDate)
			{
				$.ajax({
					url: "./Transaction/Outgoing/GetInvoiceNumber.php",
					type: "POST",
					data: { SelectedDate : SelectedDate, InvoiceNumberType : "TJ"},
					dataType: "json",
					success: function(data) {
						if(data.FailedFlag == '0') {
							$("#txtOutgoingNumber").val(data.InvoiceNumber);
						}
						else {
							$("#loading").hide();
							$.notify(data.Message, "error");					
						}
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
		</script>
	</body>
</html>
