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
		if($CancellationID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						TC.CancellationID,
						OT.OutgoingID,
						OT.OutgoingNumber,
						OT.Remarks,
						OT.DeliveryCost,
						MC.CustomerName,
						DATE_FORMAT(OT.TransactionDate, '%d-%m-%Y') AS TransactionDate
					FROM
						transaction_cancellation TC
						JOIN transaction_outgoing OT
							ON OT.OutgoingID = TC.OutgoingID
						JOIN master_customer MC
							ON MC.CustomerID = OT.CustomerID
					WHERE
						OT.OutgoingID = $OutgoingID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$OutgoingID = $row['OutgoingID'];
			$OutgoingNumber = $row['OutgoingNumber'];
			$Remarks = $row['Remarks'];
			$CustomerName = $row['CustomerName'];
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
						CONCAT(MB.BrandName, ' ', I.TypeName, ' - ', OTD.BatchNumber) AS TypeName,
						OTD.IsPercentage,
						OTD.Remarks
					FROM
						transaction_outgoingdetails OTD
						JOIN master_type I
							ON I.TypeID = OTD.TypeID
						JOIN master_brand MB
							ON MB.BrandID = I.BrandID
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
					array_push($Data, "'".$row['OutgoingDetailsID']."', '".$row['TypeID']."', '".$row['TypeName']."', '".$row['BatchNumber']."', '".$row['Quantity']."', '".$row['BuyPrice']."', '".$row['SalePrice']."', '".$row['Discount']."', '".$row['Remarks']."', '".$row['IsPercentage']."'");
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
										<select name="ddlOutgoing" id="ddlOutgoing" class="form-control-custom" placeholder="Pilih Nota" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT OutgoingID, OutgoingNumber FROM transaction_outgoing OT";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													if($OutgoingID == $row['OutgoingID']) echo "<option selected value='".$row['OutgoingID']."' >".$row['OutgoingNumber']."</option>";
													else echo "<option value='".$row['OutgoingID']."' >".$row['OutgoingNumber']."</option>";
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
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtTransactionDate" readonly name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" onchange="GetInvoiceNumber(this.value);" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
								<div class="col-md-1 labelColumn">
									Pelanggan:
								</div>
								<div class="col-md-3">
									<input id="txtCustomerName" readonly name="txtCustomerName" type="text" class="form-control-custom" placeholder="Pelanggan" required <?php echo 'value="'.$CustomerName.'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									
									<table class="table" style="width:auto;" id="datainput">
										<thead style="background-color: black;color:white;height:25px;width:1020px;display:block;">
											<td align="center" style="width:30px;">No</td>
											<td align="center" style="width:180px;">Nama Barang</td>
											<td align="center" style="width:75px;">QTY</td>
											<td align="center" style="width:135px;">Harga Jual</td>
											<td align="center" style="width:155px;">Diskon</td>
											<td align="center" style="width:170px;">Total</td>
											<td align="center" style="width:250px;">Keterangan</td>
										</thead>
										<tbody style="display:block;max-height:172px;height:100%;overflow-y:auto;">
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota' style="width:30px;vertical-align:middle;"></td>
												<td style="width:180px;">
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
												<td style="width:135px;">
													<input type="text" id="txtSalePrice" value="0.00" name="txtSalePrice" style="text-align:right;" class="form-control-custom" placeholder="Harga Jual" readonly />
												</td>
												<td style="width:155px;">
													<input type="text" id="txtDiscount" style="display:inline-block;width: 90px;text-align:right;" value="0" name="txtDiscount" style="text-align:right;" class="form-control-custom" placeholder="Diskon"  readonly /> &nbsp; <input type="checkbox" name="chkIsPercentage" id="chkIsPercentage" style="margin-top:2px;vertical-align:sub;" value=1 checked class="chkIsPercentage" readonly /> (%)
												</td>
												<td  style="width:170px;">
													<input type="text" id="txtTotal" name="txtTotal" class="form-control-custom" style="text-align:right;" value="0.00" placeholder="Jumlah" readonly />
												</td>
												<td  style="width:250px;">
													<input type="text" id="txtRemarksDetail" name="txtRemarksDetail" class="form-control-custom" style="width:235px;" placeholder="Keterangan" readonly />
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
									<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" placeholder="Catatan" readonly ><?php echo $Remarks; ?></textarea>
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
			
			function LoadOutgoingDetails() {
				
			}
			$(document).ready(function () {
				$("#ddlOutgoing").combobox({
					select: function( event, ui ) {
						LoadOutgoingDetails();						
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
					
					if($("#ddlSales").val() == "") {
						PassValidate = 0;
						$("#ddlSales").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#ddlSales").next().find("input").focus();
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
		</script>
	</body>
</html>
