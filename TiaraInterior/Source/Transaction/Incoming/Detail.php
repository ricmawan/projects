<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$IncomingID = mysql_real_escape_string($_GET['ID']);
		$SupplierID = "";
		$TransactionDate = "";
		$IsEdit = 0;
		$rowCount = 0;
		$Data = "";
		if($IncomingID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
					IT.IncomingID,
					IT.SupplierID,
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
			$SupplierID = $row['SupplierID'];
			$TransactionDate = $row['TransactionDate'];
			
			$sql = "SELECT
						ITD.IncomingDetailsID,
						ITD.TypeID,
						ITD.Quantity,
						ITD.Price,
						CONCAT(MC.BrandName, ' ', I.TypeName) AS TypeName
					FROM
						transaction_incomingdetails ITD
						JOIN master_type I
							ON I.TypeID = ITD.TypeID
						JOIN master_brand MB
							ON MC.BrandID = I.BrandID
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
					array_push($Data, "'".$row['IncomingDetailsID']."', '".$row['TypeID']."', '".$row['Quantity']."', '".$row['Price']."', '".$row['TypeName']."'");
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
			.ui-autocomplete {
				font-family: Open Sans, sans-serif; 
				font-size: 14px;
			}
			.caret {
				display: inline-block;
				width: 0;
				height: 0;
				margin-left: 2px;
				vertical-align: middle;
				border-top: 4px solid;
				border-right: 4px solid transparent;
				border-left: 4px solid transparent;
				right: 10px;
				top: 50%;
				position: absolute;
			}
			.table > thead > tr > th,
			.table > tbody > tr > th,
			.table > tfoot > tr > th,
			.table > thead > tr > td,
			.table > tbody > tr > td,
			.table > tfoot > tr > td {
				padding: 2px 8px 2px 8px;
				line-height: 1.42857143;
				vertical-align: top;
				border-top: 1px solid #ddd;
			}			
			.form-control {
				height: 24px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Barang Masuk</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-5">
									Tanggal:<br />
									<input id="hdnIncomingID" name="hdnIncomingID" type="hidden" <?php echo 'value="'.$IncomingID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
									<input id="txtTransactionDate" style="height:34px;" name="txtTransactionDate" type="text" class="form-control DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
								<div class="col-md-5">
									Supplier:<br />
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlSupplier" id="ddlSupplier" class="form-control" placeholder="Pilih Supplier" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT SupplierID, SupplierName FROM master_supplier";
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
								<div class="col-md-5">
									Merk:<br />
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlBrand" id="ddlBrand" class="form-control" placeholder="Pilih Merek" >
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
								<div class="col-md-5">
									Tipe:<br />
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlType" id="ddlType" class="form-control" placeholder="Pilih Tipe" >
											<option value="" brandid="" selected> </option>
										</select>
										<select name="ddlHiddenType" id="ddlHiddenType" style="display:none;" class="form-control" placeholder="Pilih Barang" >
											<option value="" brandid="" selected> </option>
											<?php
												$sql = "SELECT 
															MI.TypeID, 
															MI.TypeName, 
															MB.BrandID, 
															MI.BuyPrice,
															MI.SalePrice,
															MB.BrandName
														FROM 
															master_type MI 
															JOIN master_brand MB
																ON MB.BrandID = MI.BrandID";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['TypeID']."' buyprice='".$row['buyPrice']."' saleprice='".$row['SalePrice']."' brandid='".$row['BrandID']."' >".$row['BrandName']." ".$row['TypeName']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div style="max-height: 335px !important; height:100%; overflow-y: auto;">
								<div class="col-md-10">					
									<table class="table" id="datainput">
										<thead>
											<td>No</td>
											<td>Nama Barang</td>
											<td>Batch</td>
											<td>QTY</td>
											<td>Harga Beli</td>
											<td>Harga Jual</td>
											<td>Total</td>
										</thead>
										<tbody>
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota'></td>
												<td>
													<input type="text" id="txtTypeName" name="txtTypeName" class="form-control txtTypeName" placeholder="Nama Barang" readonly />
													<input type="hidden" id="hdnTypeID" name="hdnTypeID" value="0" class="hdnTypeID" />
													<input type="hidden" id="hdnIncomingDetailsID" class="hdnIncomingDetailsID" name="hdnIncomingDetailsID" value="0" />
												</td>
												<td>
													<input type="text" row="" id="txtBatchNumber" name="txtBatchNumber" onkeypress="return isNumberKey(event)" onchange="Calculate();" class="form-control txtBatchNumber" placeholder="Batch"/>
												</td>
												<td>
													<input type="text" row="" value=1 id="txtQuantity" style="width: 50px;" name="txtQuantity" onkeypress="return isNumberKey(event)" onchange="Calculate();" class="form-control txtQuantity" placeholder="QTY"/>
												</td>
												<td>
													<input type="text" id="txtBuyPrice" value="0.00" name="txtBuyPrice" style="text-align:right;" class="form-control txtBuyPrice" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga Beli"/>
												</td>
												<td>
													<input type="text" id="txtSalePrice" value="0.00" name="txtSalePrice" style="text-align:right;" class="form-control txtSalePrice" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga Jual"/>
												</td>
												<td>
													<input type="text" id="txtTotal" name="txtTotal" class="form-control txtTotal" onchange="calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="text-align:right;" value="0.00" placeholder="Jumlah" readonly />
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
						</form>
						<br />
						<div class="row">
							<div class="col-md-2" style="text-align:right;">
								No Nota :
							</div>
							<div class="col-md-2">
								<input type="text" id="txtIncomingNumber" name="txtIncomingNumber" class="form-control" readonly />
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2" style="text-align:right;">
								Catatan :
							</div>
							<div class="col-md-4">
								<textarea id="txtAddress" name="txtAddress" class="form-control" placeholder="Catatan"></textarea>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2" style="text-align:right;">
								Grand Total :
							</div>
							<div class="col-md-2">
								<input type="text" id="txtGrandTotal" style="text-align:right;" value="0.00" name="txtGrandTotal" class="form-control" readonly />
							</div>
						</div>
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
				$("#ddlHiddenType option").each(function() {
					if($(this).attr("brandid") == $("#ddlBrand").val() || $(this).attr("brandid") == "") {
						$("#ddlType").append($(this).clone());
					}
				});
			}
			
			function BindTypeList() {
				var i = 1;
				var CurrentTypeID = $("#ddlType").val();
				var CurrentBuyPrice = $("#ddlType option:selected").attr("buyprice");
				var CurrentSalePrice = $("#ddlType option:selected").attr("saleprice");
				var CurrentTypeName = $("#ddlType option:selected").text();
				var rows = $("#recordnew").val();
				var AddFlag = 1;
				//QTY + 1 if selected item already exists
				for(i=1;i<=rows;i++) {
					/*if($("#hdnTypeID" + i).val() == CurrentTypeID) {
						$("#txtQuantity" + i).val((parseFloat($("#txtQuantity" + i).val()) + 1));
						AddFlag = 0;
					}*/
				}
				if(AddFlag == 1) {
					$("#btnAdd").click();
					$("#hdnTypeID" + i).val(CurrentTypeID);
					$("#txtTypeName" + i).val(CurrentTypeName);
					$("#txtBuyPrice" + i).val(returnRupiah(CurrentBuyPrice.toString()));
					$("#txtSalePrice" + i).val(returnRupiah(CurrentSalePrice.toString()));
					$("#txtQuantity" + i).val(1);
					$("#txtTotal" + i).val(CurrentBuyPrice);
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
				$(".txtBatchNumber").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtBatchNumber" + i);
						$(this).attr("name", "txtBatchNumber" + i);
						$(this).attr("row", i);
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
				$(".txtSalePrice").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtSalePrice" + i);
						$(this).attr("name", "txtSalePrice" + i);
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
						price = $("#txtBuyPrice" + row).val().replace(/\,/g, "");
						if(qty == "") {
							$(this).val(1);
							qty = 1;
						}
						else if(price == "") {
							$("#txtBuyPrice" + row).val("0.00");
							price = 0;
						}
						GrandTotal += parseFloat(qty) * parseFloat(price);
						Total = parseFloat(qty) * parseFloat(price);
						$("#txtTotal" + row).val(returnRupiah(Total.toString()));
					}
					i++;
				});
				$("#txtGrandTotal").val(returnRupiah(GrandTotal.toString()));
			}
			$(document).ready(function () {
				$("#ddlBrand").combobox({
					select: function( event, ui ) {
						BindType();						
					}
				});
				
				$("#ddlSupplier").combobox();
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
					$("#recordnew").val(count);
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
						$("#hdnIncomingDetailsID" + count).val(d[0].replace("'", ""));
						$("#hdnTypeID" + count).val(d[1].replace("'", ""));
						$("#txtQuantity" + count).val(d[2].replace("'", ""));
						$("#txtBuyPrice" + count).val(returnRupiah(d[3].replace("'", "")));
						$("#txtSalePrice" + count).val(returnRupiah(d[3].replace("'", "")));
						$("#txtBatchNumber" + count).val(returnRupiah(d[3].replace("'", "")));
						$("#txtTypeName" + count).val(d[4].replace("'", ""));
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
					$(".form-control").each(function() {
						if($(this).hasAttr('required')) {
							if($(this).val() == "") {
								PassValidate = 0;
								$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $(this).focus();
								FirstFocus = 1;
							}
						}
					});
					
					/*if($("#ddlSupplier").val() == "") {
						PassValidate = 0;
						$("#ddlSupplier").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#ddlSupplier").next().find("input").focus();
						FirstFocus = 1;
					}*/
					if(PassValidate == 0) {
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						return false;
					}
					else SubmitForm("./Transaction/Incoming/Insert.php");
				}
			}
		</script>
	</body>
</html>
