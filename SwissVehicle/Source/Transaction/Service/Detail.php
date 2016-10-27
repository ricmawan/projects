<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$ServiceID = mysql_real_escape_string($_GET['ID']);
		$MachineID = "";
		$Remarks = "";
		$TransactionDate = "";
		$IsSelfWorkshop = 1;
		$WorkshopName = "";
		$Kilometer = 0;
		$IsEdit = 0;
		$rowCount = 0;
		$Data = "";
		if($ServiceID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						TS.ServiceID,
						TS.MachineID,
						TS.IsSelfWorkshop,
						TS.WorkshopName,
						TS.Kilometer,
						DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') AS TransactionDate,
						TS.Remarks
					FROM
						transaction_service TS
					WHERE
						TS.ServiceID = $ServiceID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$ServiceID = $row['ServiceID'];
			$MachineID = $row['MachineID'];
			$IsSelfWorkshop = $row['IsSelfWorkshop'];
			$WorkshopName = $row['WorkshopName'];
			$Kilometer = $row['Kilometer'];
			$Remarks = $row['Remarks'];
			$TransactionDate = $row['TransactionDate'];
			
			$sql = "SELECT
						SD.ServiceDetailsID,
						SD.ItemID,
						IFNULL(CONCAT(MI.ItemCode, ' - ', MI.ItemName), SD.ItemName) ItemName,
						SD.Quantity,
						SD.Price,
						SD.Remarks,
						IFNULL(SD.Quantity, 0) + IFNULL(PD.Quantity, 0) - IFNULL(TSD.Quantity, 0) Stock,
						MI.IsSecond
					FROM
						transaction_servicedetails SD
						LEFT JOIN master_item MI
							ON MI.ItemID = SD.ItemID
						LEFT JOIN
						(
							SELECT
								PD.ItemID,
								PD.Price,
								SUM(PD.Quantity) Quantity
							FROM
								transaction_purchasedetails PD
							GROUP BY
								PD.ItemID,
								PD.Price
						)PD
							ON PD.ItemID = MI.ItemID
							AND PD.Price = SD.Price
						LEFT JOIN
						(
							SELECT
								SD.ItemID,
								SD.Price,
								SUM(SD.Quantity) Quantity
							FROM
								transaction_servicedetails SD
							GROUP BY
								SD.ItemID,
								SD.Price
						)TSD
							ON TSD.ItemID = MI.ItemID
							AND TSD.Price = SD.Price
					WHERE
						SD.ServiceID = $ServiceID";
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
					array_push($Data, "'".$row['ServiceDetailsID']."', '".$row['ItemID']."', '".$row['ItemName']."', '".$row['Quantity']."', '".$row['Price']."', '".$row['Remarks']."', '".$row['Stock']."', '".$row['IsSecond']."'");
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Servis</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tanggal :
									<input id="hdnServiceID" name="hdnServiceID" type="hidden" <?php echo 'value="'.$ServiceID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
									<input id="hdnIsSelfWorkshop" name="hdnIsSelfWorkshop" type="hidden" <?php echo 'value="'.$IsSelfWorkshop.'"'; ?> />
									<input id="hdnMachineID" name="hdnMachineID" type="hidden" <?php echo 'value="'.$MachineID.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Mobil/Mesin :
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlMachine" id="ddlMachine" class="form-control-custom" placeholder="Pilih Mobil/Mesin" >
											<option value="" brandid="" selected> </option>
											<?php
												$sql = "SELECT 
															MM.MachineID,
															MM.MachineCode,
															MM.MachineType
														FROM 
															master_machine MM
														ORDER BY
															MM.MachineCode";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													if($MachineID == $row['MachineID']) echo "<option selected value='".$row['MachineID']."' >".$row['MachineType']." - ".$row['MachineCode']."</option>";
													else echo "<option value='".$row['MachineID']."' >".$row['MachineType']." - ".$row['MachineCode']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row" >
								<div class="col-md-2 labelColumn">
									Kilometer :
								</div>
								<div class="col-md-3">
									<input id="txtKilometer" style="text-align:right;" name="txtKilometer" type="text" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertThousand(this.id, this.value)" class="form-control-custom" placeholder="KM" required <?php echo 'value="'.number_format($Kilometer,0,".",",").'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Bengkel :
								</div>
								<div class="col-md-3">
									<select id="ddlWorkshop" name="ddlWorkshop" class="form-control-custom">
										<option value=1 selected >Milik Sendiri</option>
										<option value=0 >Rekanan</option>
									</select>
								</div>
							</div>
							<br />
							<div class="row" id="WorkshopRow" style="display: none;" >
								<div class="col-md-2 labelColumn">
									Nama Bengkel :
								</div>
								<div class="col-md-3">
									<input id="txtWorkshopName" name="txtWorkshopName" type="text" class="form-control-custom" placeholder="Nama Bengkel" <?php echo 'value="'.$WorkshopName.'"'; ?> />
								</div>
								<br />
							</div>
							<div class="row" id="RowItem" >
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
															ROUND(PD.Price, 2) Price,
															(IFNULL(PD.Quantity, 0) - IFNULL(SD.Quantity, 0)) Stock,
															MI.IsSecond
														FROM 
															master_item MI
															LEFT JOIN
															(
																SELECT
																	PD.ItemID,
																	PD.Price,
																	SUM(PD.Quantity) Quantity
																FROM
																	transaction_purchasedetails PD
																GROUP BY
																	PD.ItemID,
																	PD.Price
															)PD
																ON PD.ItemID = MI.ItemID
															LEFT JOIN
															(
																SELECT
																	SD.ItemID,
																	SD.Price,
																	SUM(SD.Quantity) Quantity
																FROM
																	transaction_servicedetails SD
																GROUP BY
																	SD.ItemID,
																	SD.Price
															)SD
																ON SD.ItemID = MI.ItemID
																AND PD.Price = SD.Price
														WHERE
															(IFNULL(PD.Quantity, 0) - IFNULL(SD.Quantity, 0)) > 0
															 AND CASE
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
													echo "<option value='".$row['ItemID']."' itemcode='".$row['ItemCode']."' itemname='".$row['ItemName']."' issecond='".$row['IsSecond']."' price='".$row['Price']."' stock='".$row['Stock']."' >".$row['ItemCode']." - ".$row['ItemName']." (".number_format($row['Price'],2,".",",").")</option>";
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
										<tbody style="display:block;max-height:174px;height:100%;overflow-y:auto;">
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota' style="width:30px;vertical-align:middle;"></td>
												<td style="width:300px;">
													<input type="text" id="txtItemName" name="txtItemName" class="form-control-custom txtItemName" placeholder="Nama Barang" readonly />
													<input type="hidden" id="hdnItemID" name="hdnItemID" value="0" class="hdnItemID" />
													<input type="hidden" id="hdnIsSecond" name="hdnIsSecond" value="0" class="hdnIsSecond" />
													<input type="hidden" id="hdnServiceDetailsID" class="hdnServiceDetailsID" name="hdnServiceDetailsID" value="0" />
													<input type="hidden" id="hdnStock" name="hdnStock" class="hdnStock" value="" />
												</td>
												<td style="width:75px;">
													<input type="text" row="" value=1 id="txtQuantity" style="text-align:right;" name="txtQuantity" onkeypress="return isNumberKey(event)" onchange="ValidateQty(this.getAttribute('row'));" class="form-control-custom txtQuantity" placeholder="QTY" />
												</td>
												<td style="width:125px;">
													<input type="text" readonly id="txtPrice" value="0.00" name="txtPrice" style="text-align:right;" class="form-control-custom txtPrice" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga" />
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
								<button class="btn btn-default" id="btnAdd" style="display:none;" ><i class="fa fa-plus "></i> Tambah</button>&nbsp;&nbsp;
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
				var CurrentItemName = $("#ddlItem option:selected").attr("itemcode") + " - " + $("#ddlItem option:selected").attr("itemname");
				var CurrentStock = $("#ddlItem option:selected").attr("stock");
				var CurrentIsSecond = $("#ddlItem option:selected").attr("issecond");
				var rows = $("#recordnew").val();
				var AddFlag = 1;
				//QTY + 1 if selected item already exists
				for(i=1;i<=rows;i++) {
					if($("#hdnItemID" + i).val() == CurrentItemID && $("#txtPrice" + i).val().replace(/\,/g, "") == CurrentPrice ) {
						AddFlag = 0;
						var currentQty = parseFloat($("#txtQuantity" + i).val()) + 1;
						var currentStock = $("#hdnStock" +  i).val();
						if(parseInt(currentQty) > parseInt(currentStock)) {
							$.notify("Sisa stok yang ada : " + currentStock, "error");
							$("#txtQuantity" + i).val(currentStock);
						}
						else {
							$("#txtQuantity" + i).val((parseFloat($("#txtQuantity" + i).val()) + 1));
						}
					}
				}
				if(AddFlag == 1) {
					$("#btnAdd").click();
					$("#hdnStock" + i).val(CurrentStock.toString());
					$("#hdnItemID" + i).val(CurrentItemID);
					$("#hdnIsSecond" + i).val(CurrentIsSecond);
					$("#txtItemName" + i).val(CurrentItemName);
					$("#txtPrice" + i).val(returnRupiah(CurrentPrice.toString()));
					$("#txtQuantity" + i).val(1);
					$("#txtTotal" + i).val(CurrentPrice);
				}
				Calculate();
			}
			
			function DeleteRow(row) {
				var count = $("#datainput tbody tr").length - 1;
				count--;
				$("#num" + row).remove();
				$("#recordnew").val(count);
				if(count == 0) {
					$("#ddlWorkshop").attr("disabled", false);
				}
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
				$(".hdnIsSecond").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnIsSecond" + i);
						$(this).attr("name", "hdnIsSecond" + i);
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
				$(".hdnServiceDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnServiceDetailsID" + i);
						$(this).attr("name", "hdnServiceDetailsID" + i);
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
						GrandTotal += parseFloat(qty) * parseFloat(price);
						Total = parseFloat(qty) * parseFloat(price);
						$("#txtTotal" + row).val(returnRupiah(Total.toFixed(2).toString()));
					}
					i++;
				});
				$("#txtGrandTotal").val(returnRupiah(GrandTotal.toFixed(2).toString()));
			}
			
			$(document).ready(function () {
				$("#ddlWorkshop").change(function() {
					$("#hdnIsSelfWorkshop").val($("#ddlWorkshop").val());
					if($("#ddlWorkshop").val() == "1") {
						$("#RowItem").show();
						$("#btnAdd").hide();
						$(".txtItemName").attr("readonly", true);
						$(".txtPrice").attr("readonly", true);
						$("#WorkshopRow").hide();
						$("#txtWorkshopName").removeAttr("required");
					}
					else {
						$("#RowItem").hide();
						$("#btnAdd").show();
						$(".txtItemName").attr("readonly", false);
						$(".txtPrice").attr("readonly", false);
						$("#WorkshopRow").show();
						$("#txtWorkshopName").attr("required", "required");
					}
				});
				
				$("#ddlMachine").combobox();
				$("#ddlMachine").next().find("input").click(function() {
					$(this).val("");
				});
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
				
				$("#btnAdd").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					count++;
					if(count > 0) {
						$("#ddlWorkshop").attr("disabled", true);
					}
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

					if(parseInt($("#hdnRow").val()) < 1) {
						$("#datainput tbody").animate({
							scrollTop: (25 * count)
						}, "slow");
					}
				});
				
				if(parseInt($("#hdnRow").val()) > 0) {
					$("#ddlWorkshop").val($("#hdnIsSelfWorkshop").val());
					if($("#ddlWorkshop").val() == "1") {
						$("#RowItem").show();
						$("#btnAdd").hide();
						$(".txtItemName").attr("readonly", true);
						$("#WorkshopRow").hide();
						$("#txtWorkshopName").removeAttr("required");
					}
					else {
						$("#RowItem").hide();
						$("#btnAdd").show();
						$(".txtItemName").attr("readonly", false);
						$("#WorkshopRow").show();
						$("#txtWorkshopName").attr("required", "required");
					}
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
						$("#hdnServiceDetailsID" + count).val(d[0].replace("'", ""));
						$("#hdnItemID" + count).val(d[1].replace("'", ""));
						$("#txtItemName" + count).val(d[2].replace("'", ""));
						$("#txtQuantity" + count).val(d[3].replace("'", ""));
						$("#txtPrice" + count).val(returnRupiah(d[4].replace("'", "")));
						$("#txtRemarksDetail" + count).val(d[5].replace("'", ""));
						$("#hdnStock" + count).val(d[6].replace("'", ""));
						$("#hdnIsSecond" + count).val(d[7].replace("'", ""));
						$("#record").val(count);
						$("#recordnew").val(count);
					}
					Calculate();
				}
			});
			
			function ValidateQty(row) {
				if($("#ddlWorkshop").val() == "1") {
					var currentQty = $("#txtQuantity" + row).val();
					var currentStock = $("#hdnStock" +  row).val();
					if(parseInt(currentQty) > parseInt(currentStock)) {
						$.notify("Sisa stok yang ada : " + currentStock, "error");
						$("#txtQuantity" + row).val(currentStock);
					}
				}
				Calculate();
			}
			
			function SubmitValidate() {
				if($("#recordnew").val() > 0) {
					var PassValidate = 1;
					var FirstFocus = 0;
					var MachineID = $("#ddlMachine").val();
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
					
					if(MachineID == 0) {
						$("#ddlMachine").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						PassValidate = 0;
						if(FirstFocus == 0) $("#ddlMachine").next().find("input").focus();
						FirstFocus = 1;
					}
					
					if(PassValidate == 0) {
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						return false;
					}
					else SubmitForm("./Transaction/Service/Insert.php");
				}
			}
		</script>
	</body>
</html>
