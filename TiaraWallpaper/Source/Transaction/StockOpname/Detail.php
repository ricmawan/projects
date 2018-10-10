<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$StockOpnameID = mysql_real_escape_string($_GET['ID']);
		$Remarks = "";
		$IsEdit = 0;
		$rowCount = 0;
		$Data = "";
		if($StockOpnameID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
					SO.StockOpnameID,
					SO.Remarks
				FROM
					transaction_stockopname SO
				WHERE
					SO.StockOpnameID = $StockOpnameID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$StockOpnameID = $row['StockOpnameID'];
			$Remarks = $row['Remarks'];
			
			$sql = "SELECT
						SOD.StockOpnameDetailsID,
						SOD.TypeID,
						SOD.FromQty,
						SOD.ToQty,
						SOD.BuyPrice,
						SOD.SalePrice,
						SOD.BatchNumber,
						CONCAT(MB.BrandName, ' ', I.TypeName, ' - ', SOD.BatchNumber) AS TypeName
					FROM
						transaction_stockopnamedetails SOD
						JOIN master_type I
							ON I.TypeID = SOD.TypeID
						JOIN master_brand MB
							ON MB.BrandID = I.BrandID
					WHERE
						SOD.StockOpnameID = $StockOpnameID";
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
					array_push($Data, "'".$row['StockOpnameDetailsID']."', '".$row['TypeID']."', '".$row['TypeName']."', '".$row['BatchNumber']."', '".$row['FromQty']."', '".$row['ToQty']."', '".$row['BuyPrice']."', '".$row['SalePrice']."'");
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Penyesuaian Stok</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" > 
							<div class="row">
								<div class="col-md-1 labelColumn">
									Merek :
									<input id="hdnStockOpnameID" name="hdnStockOpnameID" type="hidden" <?php echo 'value="'.$StockOpnameID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlBrand" id="ddlBrand" class="form-control-custom" placeholder="Pilih Merek" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT BrandID, BrandName FROM master_brand ORDER BY BrandName";
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
										<thead style="background-color: black;color:white;height:25px;width:885px;display:block;">
											<td align="center" style="width:30px;">No</td>
											<td align="center" style="width:230px;">Nama Barang</td>
											<td align="center" style="width:75px;">QTY</td>
											<td align="center" style="width:85px;">Penyesuian</td>
											<td align="center" style="width:135px;">Harga Beli</td>
											<td align="center" style="width:135px;">Harga Jual</td>
											<td align="center" style="width:170px;">Total</td>
											<td style="width: 26px"></td>
										</thead>
										<tbody style="display:block;max-height:172px;height:100%;overflow-y:auto;">
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota' style="width:30px;vertical-align:middle;"></td>
												<td style="width:230px;">
													<input type="text" id="txtTypeName" name="txtTypeName" class="form-control-custom txtTypeName" placeholder="Nama Barang" readonly />
													<input type="hidden" id="hdnTypeID" name="hdnTypeID" value="0" class="hdnTypeID" />
													<input type="hidden" id="hdnStockOpnameDetailsID" class="hdnStockOpnameDetailsID" name="hdnStockOpnameDetailsID" value="0" />
													<input type="hidden" id="hdnBatchNumber" name="hdnBatchNumber" class="hdnBatchNumber" value="" />
												</td>
												<td style="width:75px;">
													<input type="text" row="" value=1 id="txtQuantity" style="text-align:right;" name="txtQuantity" class="form-control-custom txtQuantity" placeholder="QTY" readonly />
												</td>
												<td style="width:85px;">
													<input type="text" row="" value=1 id="txtAdjustment" style="text-align:right;" name="txtAdjustment" onclick="this.select();" onkeypress="return isNumberKey(event)" onchange="Calculate();" class="form-control-custom txtAdjustment" placeholder="Penyesuaian" />
												</td>
												<td style="width:135px;">
													<input type="text" id="txtBuyPrice" value="0.00" name="txtBuyPrice" style="text-align:right;" onclick="this.select();" class="form-control-custom txtBuyPrice" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga Beli"/>
												</td>
												<td style="width:135px;">
													<input type="text" id="txtSalePrice" value="0.00" name="txtSalePrice" style="text-align:right;" onclick="this.select();" class="form-control-custom txtSalePrice" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga Jual"/>
												</td>
												<td  style="width:170px;">
													<input type="text" id="txtTotal" name="txtTotal" class="form-control-custom txtTotal" style="text-align:right;" value="0.00" placeholder="Jumlah" readonly />
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
								<button class="btn btn-default" id="btnSave"  onclick="SubmitValidate();" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
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
					$("#txtBuyPrice" + i).val(returnRupiah(CurrentBuyPrice.toString()));
					$("#hdnBatchNumber" + i).val(CurrentBatchNumber.toString());
					$("#hdnStock" + i).val(CurrentStock.toString());
					$("#txtSalePrice" + i).val(returnRupiah(CurrentSalePrice.toString()));
					$("#txtQuantity" + i).val(CurrentStock);
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
				$(".hdnStockOpnameDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnStockOpnameDetailsID" + i);
						$(this).attr("name", "hdnStockOpnameDetailsID" + i);
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
				$(".txtBuyPrice").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtBuyPrice" + i);
						$(this).attr("name", "txtBuyPrice" + i);
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
				var adjustment = 1;
				var buyprice = 0;
				var saleprice = 0;
				var disc = 0;
				var isPercentage = 0;
				var i = 0;
				var deliveryCost = 0;
				$(".txtAdjustment").each(function() {
					if(i != 0) {
						adjustment = $(this).val();
						row = $(this).attr("row");
						qty = $("#txtQuantity" + row).val();
						saleprice = $("#txtSalePrice" + row).val().replace(/\,/g, "");
						buyprice = $("#txtBuyPrice" + row).val().replace(/\,/g, "");
						if(adjustment == "") {
							$(this).val(1);
							adjustment = 1;
						}
						if(adjustment == "") {
							$(this).val(1);
							qty = 1;
						}
						if(saleprice == "") {
							$("#txtSalePrice" + row).val("0.00");
							saleprice = 0;
						}
						if(buyprice == "") {
							$("#txtBuyPrice" + row).val("0.00");
							buyprice = 0;
						}
						
						if(parseFloat(qty) > parseFloat(adjustment)) {
							GrandTotal += (parseFloat(qty) - parseFloat(adjustment)) * parseFloat(saleprice);
							Total = (parseFloat(qty) - parseFloat(adjustment)) * parseFloat(saleprice);
						}
						else {
							GrandTotal += -(parseFloat(adjustment) - parseFloat(qty)) * parseFloat(buyprice); 
							Total = -(parseFloat(adjustment) - parseFloat(qty)) * parseFloat(buyprice);
						}
						//GrandTotal += parseFloat(qty) * parseFloat(price);
						//Total = parseFloat(qty) * parseFloat(price);
						$("#txtTotal" + row).val(returnRupiah(Total.toFixed(4).toString()));
					}
					i++;
				});
				$("#txtGrandTotal").val(returnRupiah(GrandTotal.toFixed(2).toString()));
			}
			
			$(document).ready(function () {
				$("#ddlBrand").combobox({
					select: function( event, ui ) {
						BindType();						
					}
				});
				$("#ddlBrand").next().find("input").click(function() {
					$(this).val("");
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
						$("#hdnStockOpnameDetailsID" + count).val(d[0].replace("'", ""));
						$("#hdnTypeID" + count).val(d[1].replace("'", ""));
						$("#txtTypeName" + count).val(d[2].replace("'", ""));
						$("#hdnBatchNumber" + count).val(d[3].replace("'", ""));
						$("#txtQuantity" + count).val(d[4].replace("'", ""));
						$("#txtAdjustment" + count).val(d[5].replace("'", ""));
						$("#txtBuyPrice" + count).val(returnRupiah(d[6].replace("'", "")));
						$("#txtSalePrice" + count).val(returnRupiah(d[7].replace("'", "")));
						
						$("#record").val(count);
						$("#recordnew").val(count);
					}
					Calculate();
				}
			});
			
			var counterInsert = 0;
			function SubmitValidate() {
				if(counterInsert == 0) {
					counterInsert = 1;
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
							SubmitForm("./Transaction/StockOpname/Insert.php");
						}
					}
				}
				setTimeout(function() {
					counterInsert = 0;
				}, 1000);
			}
		</script>
	</body>
</html>
