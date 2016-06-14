<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$IncomingInventoryID = mysql_real_escape_string($_GET['ID']);
		$TransactionDate = "";
		$IsEdit = 0;
		$rowCount = 0;
		$Data = "";
		if($IncomingInventoryID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						II.IncomingInventoryID,
						DATE_FORMAT(II.TransactionDate, '%d-%m-%Y') AS TransactionDate
					FROM
						transaction_incominginventory II
					WHERE
						II.IncomingInventoryID = $IncomingInventoryID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$TransactionDate = $row['TransactionDate'];
			
			$sql = "SELECT
						IID.IncomingInventoryDetailsID,
						IID.InventoryID,
						IID.Quantity,
						IID.Price,
						IID.Remarks,
						MI.InventoryName
					FROM
						transaction_incominginventorydetails IID
						JOIN master_inventory MI
							ON MI.InventoryID = IID.InventoryID
					WHERE
						IID.IncomingInventoryID = $IncomingInventoryID";
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
					array_push($Data, "'".$row['IncomingInventoryDetailsID']."', '".$row['InventoryID']."', '".$row['Quantity']."', '".$row['Price']."', '".$row['Remarks']."', '".$row['InventoryName']."'");
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Pembelian Inventaris</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-1 labelColumn">
									Tanggal:
									<input id="hdnIncomingInventoryID" name="hdnIncomingInventoryID" type="hidden" <?php echo 'value="'.$IncomingInventoryID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
								<div class="col-md-1 labelColumn">
									Barang:
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlInventory" id="ddlInventory" class="form-control ddlInventory" placeholder="Pilih Barang" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT InventoryID, InventoryName FROM master_inventory";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['InventoryID']."' >".$row['InventoryName']."</option>";
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
										<thead style="background-color: black;color:white;height:25px;width:800px;display:block;">
											<td align="center" style="width:30px;">No</td>
											<td align="center" style="width:180px;">Inventaris</td>
											<td align="center" style="width:75px;">Qty</td>
											<td align="center" style="width:135px;">Harga</td>
											<td align="center" style="width:170px;">Total</td>
											<td align="center" style="width:180px;">Keterangan</td>
											<td style="width: 26px"></td>
										</thead>
										<tbody style="display:block;max-height:282px;height:100%;overflow-y:auto;">
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota' style="width:30px;vertical-align:middle;"></td>
												<td style="width:180px;">
													<input type="text" id="txtInventoryName" readonly name="txtInventoryName" class="txtInventoryName form-control-custom placeholder" />
													<input type="hidden" id="hdnInventoryID" name="hdnInventoryID" class="hdnInventoryID" />
													<input type="hidden" id="hdnIncomingInventoryDetailsID" class="hdnIncomingInventoryDetailsID" name="hdnIncomingInventoryDetailsID" value="0" />
												</td>
												<td style="width:75px;">
													<input type="text" row="" value=1 id="txtQuantity" style="text-align:right;" name="txtQuantity" onkeypress="return isNumberKey(event)" onchange="Calculate();" class="form-control-custom txtQuantity" placeholder="QTY" />
												</td>
												<td style="width:135px;">
													<input type="text" id="txtPrice" value="0.00" name="txtPrice" style="text-align:right;" class="form-control-custom txtPrice" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga" />
												</td>
												<td  style="width:170px;">
													<input type="text" id="txtTotal" name="txtTotal" class="form-control-custom txtTotal" style="text-align:right;" readonly value="0.00" placeholder="Jumlah" />
												</td>
												<td  style="width:180px;">
													<input type="text" id="txtRemarks" name="txtRemarks" class="form-control-custom txtRemarks" placeholder="Keterangan" />
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
								<div class="col-md-2">
									<input type="text" id="txtGrandTotal" style="text-align:right;" value="0.00" name="txtGrandTotal" class="form-control-custom" readonly />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<button class="btn btn-primary" id="btnAdd" style="display:none;" type="button"><i class="fa fa-plus"></i> Tambah</button>
									<button class="btn btn-default" id="btnSave" type="button" onclick="SubmitForm('./Transaction/IncomingInventory/Insert.php');" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function DeleteRow(row) {
				var count = $("#datainput tbody tr").length - 1;
				$("#num" + row).remove();
				$("#recordnew").val(count-1);
				RegenerateRowNumber();
				Calculate();
			}
			
			function FillInventoryID() {
				var AddFlag = 1;
				var CurrentInventoryID = $("#ddlInventory").val();
				var CurrentInventoryName = $("#ddlInventory option:selected").text();
				var rows = $("#recordnew").val();
				//QTY + 1 if selected item already exists
				for(var i=1;i<=rows;i++) {
					if($("#hdnInventoryID" + i).val() == CurrentInventoryID) {
						$("#txtQuantity" + i).val((parseInt($("#txtQuantity" + i).val()) + 1));
						AddFlag = 0;
					}
				}
				
				if(AddFlag == 1) {
					$("#btnAdd").click();
					var rows = $("#recordnew").val();
					$("#hdnInventoryID" + rows).val(CurrentInventoryID);
					$("#txtInventoryName" + rows).val(CurrentInventoryName);
				}
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
				$(".hdnIncomingInventoryDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnIncomingInventoryDetailsID" + i);
						$(this).attr("name", "hdnIncomingInventoryDetailsID" + i);
					}
					i++;
				});
				i = 0;
				$(".txtInventoryName").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtInventoryName" + i);
						$(this).attr("name", "txtInventoryName" + i);
						$(this).attr("row", i);
					}
					i++;
				});
				i = 0;
				$(".txtRemarks").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtRemarks" + i);
						$(this).attr("name", "txtRemarks" + i);
						$(this).attr("row", i);
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
				$(".hdnInventoryID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnInventoryID" + i);
						$(this).attr("name", "hdnInventoryID" + i);
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
				$(".txtQuantity").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtQuantity" + i);
						$(this).attr("name", "txtQuantity" + i);
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
				var GrandTotal = 0;
				var row = 0;
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
				$("#ddlInventory").combobox({
					select: function( event, ui ) {
						FillInventoryID();
						setTimeout(function() {
							$("#ddlInventory").next().find("input").val("");
							$("#ddlInventory").val("");
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
					$("#datainput tbody").animate({
						scrollTop: (25 * count)
					}, "slow");
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
						$("#hdnIncomingInventoryDetailsID" + count).val(d[0].replace("'", ""));
						$("#hdnInventoryID" + count).val(d[1].replace("'", ""));
						$("#txtQuantity" + count).val(d[2].replace("'", ""));
						$("#txtPrice" + count).val(returnRupiah(d[3].replace("'", "")));
						$("#txtRemarks" + count).val(d[4].replace("'", ""));
						$("#txtInventoryName" + count).val(d[5].replace("'", ""));
						$("#record").val(count);
						$("#recordnew").val(count);
					}
					Calculate();
				}
			});
			
		</script>
	</body>
</html>
