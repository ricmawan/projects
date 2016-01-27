<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$OutgoingTransactionID = mysql_real_escape_string($_GET['ID']);
		$ProjectID = "";
		$TransactionDate = "";
		$IsEdit = 0;
		$rowCount = 0;
		$Data = "";
		if($OutgoingTransactionID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						OT.OutgoingTransactionID,
						OT.ProjectID,
						DATE_FORMAT(OT.TransactionDate, '%d-%m-%Y') AS TransactionDate
					FROM
						transaction_outgoingtransaction OT
					WHERE
						OT.OutgoingTransactionID = $OutgoingTransactionID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$OutgoingTransactionID = $row['OutgoingTransactionID'];
			$ProjectID = $row['ProjectID'];
			$TransactionDate = $row['TransactionDate'];
			
			$sql = "SELECT
						OTD.OutgoingTransactionDetailsID,
						OTD.ItemID,
						OTD.Quantity,
						OTD.Price,
						CONCAT(MC.CategoryName, ' ', I.ItemName) AS ItemName,
						OTD.Remarks,
						OTD.Name
					FROM
						transaction_outgoingtransactiondetails OTD
						JOIN master_item I
							ON I.ItemID = OTD.ItemID
						JOIN master_category MC
							ON MC.CategoryID = I.CategoryID
					WHERE
						OTD.OutgoingTransactionID = $OutgoingTransactionID";
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
					array_push($Data, "'".$row['OutgoingTransactionDetailsID']."', '".$row['ItemID']."', '".$row['Quantity']."', '".$row['Price']."', '".$row['ItemName']."', '".$row['Name']."', '".$row['Remarks']."'");
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
						<h2><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Barang Keluar</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-5">
									Tanggal:<br />
									<input id="hdnOutgoingTransactionID" name="hdnOutgoingTransactionID" type="hidden" <?php echo 'value="'.$OutgoingTransactionID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
									<input id="txtTransactionDate" style="height:34px;" name="txtTransactionDate" type="text" class="form-control DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
								<div class="col-md-5">
									Proyek:<br />
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlProject" id="ddlProject" class="form-control" placeholder="Pilih Proyek" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT ProjectID, ProjectName FROM master_project WHERE IsDone = 0";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													if($ProjectID == $row['ProjectID']) echo "<option selected value='".$row['ProjectID']."' >".$row['ProjectName']."</option>";
													else echo "<option value='".$row['ProjectID']."' >".$row['ProjectName']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-5">
									Kategori Barang:<br />
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlCategory" id="ddlCategory" class="form-control" placeholder="Pilih Kategori Barang" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT CategoryID, CategoryName FROM master_category";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['CategoryID']."' >".$row['CategoryName']."</option>";
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-5">
									Barang:<br />
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlItem" id="ddlItem" class="form-control" placeholder="Pilih Barang" >
											<option value="" categoryid="" selected> </option>
										</select>
										<select name="ddlHiddenItem" id="ddlHiddenItem" style="display:none;" class="form-control" placeholder="Pilih Barang" >
											<option value="" categoryid="" selected> </option>
											<?php
												$sql = "SELECT 
															MI.ItemID, 
															MI.ItemName, 
															MC.CategoryID, 
															MI.Price,  
															MC.CategoryName
														FROM 
															master_item MI 
															JOIN master_category MC
																ON MC.CategoryID = MI.CategoryID";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['ItemID']."' price='".$row['Price']."' categoryid='".$row['CategoryID']."' >".$row['CategoryName']." ".$row['ItemName']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div style="max-height: 320px !important; height:100%; overflow-y: auto;">
								<div class="col-md-12">					
									<table class="table" id="datainput">
										<thead>
											<td>No</td>
											<td>Nama</td>
											<td>Nama Barang</td>
											<td>QTY</td>
											<td>Harga</td>
											<td>Total</td>
											<td>Keterangan</td>
										</thead>
										<tbody>
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota'></td>
												<td>
													<input type="text" id="txtName" name="txtName" class="form-control txtName" placeholder="Nama"/>
												</td>
												<td>
													<input type="text" id="txtItemName" name="txtItemName" class="form-control txtItemName" placeholder="Nama Barang" readonly />
													<input type="hidden" id="hdnItemID" name="hdnItemID" value="0" class="hdnItemID" />
													<input type="hidden" id="hdnOutgoingTransactionDetailsID" class="hdnOutgoingTransactionDetailsID" name="hdnOutgoingTransactionDetailsID" value="0" />
												</td>
												<td>
													<input type="text" row="" style="width: 50px;" value=1 id="txtQuantity" name="txtQuantity" onkeypress="return isNumberKey(event)" onchange="Calculate();" class="form-control txtQuantity" placeholder="QTY"/>
												</td>
												<td>
													<input type="text" id="txtPrice" value="0.00" name="txtPrice" style="text-align:right;" class="form-control txtPrice" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga"/>
												</td>
												<td>
													<input type="text" id="txtTotal" name="txtTotal" class="form-control txtTotal" onchange="calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="text-align:right;" value="0.00" placeholder="Jumlah" readonly />
												</td>
												<td>
													<input type="text" id="txtRemarks" name="txtRemarks" class="form-control txtRemarks" placeholder="Keterangan"/>
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
			function BindItem() {
				$("#ddlItem option").each(function() {
					$(this).remove();
				});
				$("#ddlItem").append('<option value="" categoryid="" selected> </option>');
				$("#ddlItem").val("");
				$("#ddlItem").next().find("input").val("");
				$("#ddlHiddenItem option").each(function() {
					if($(this).attr("categoryid") == $("#ddlCategory").val() || $(this).attr("categoryid") == "") {
						$("#ddlItem").append($(this).clone());
					}
				});
			}
			
			function BindItemList() {
				var i = 1;
				var CurrentItemID = $("#ddlItem").val();
				var CurrentPrice = $("#ddlItem option:selected").attr("price");
				var CurrentItemName = $("#ddlItem option:selected").text();
				//var AddFlag = 1;
				/*for(i=1;i<=rows;i++) {
					if($("#hdnItemID" + i).val() == CurrentItemID) {
						$("#txtQuantity" + i).val((parseFloat($("#txtQuantity" + i).val()) + 1));
						AddFlag = 0;
					}
				}*/
				//if(AddFlag == 1) {
					$("#btnAdd").click();
					var i = $("#recordnew").val();
					$("#hdnItemID" + i).val(CurrentItemID);
					$("#txtItemName" + i).val(CurrentItemName);
					$("#txtPrice" + i).val(returnRupiah(CurrentPrice.toString()));
					$("#txtQuantity" + i).val(1);
					$("#txtTotal" + i).val(CurrentPrice);
				//}
				Calculate();
			}
			
			function DeleteRow(row) {
				var count = $("#datainput tbody tr").length - 1;
				$("#num" + row).remove();
				$("#recordnew").val((count-1));
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
				$(".hdnOutgoingTransactionDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnOutgoingTransactionDetailsID" + i);
						$(this).attr("name", "hdnOutgoingTransactionDetailsID" + i);
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
				$(".txtName").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtName" + i);
						$(this).attr("name", "txtName" + i);
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
				i = 0;
				$(".txtItemName").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtItemName" + i);
						$(this).attr("name", "txtItemName" + i);
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
						$("#txtTotal" + row).val(returnRupiah(Total.toString()));
					}
					i++;
				});
				$("#txtGrandTotal").val(returnRupiah(GrandTotal.toString()));
			}
			$(document).ready(function () {
				$("#ddlCategory").combobox({
					select: function( event, ui ) {
						BindItem();						
					}
				});
				
				$("#ddlProject").combobox();
				$("#ddlItem").combobox({
					select: function(event, ui) {
						BindItemList();
						setTimeout(function() {
							$("#ddlItem").next().find("input").val("");
							$("#ddlItem").val("");
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
					$("#txtName" + count).removeAttr("required");
					$("#txtRemarks" + count).removeAttr("required");
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
					var item = data.split("|");
					var row = item.length;
					var count = 0;
					$('#datainput tbody:last > tr:not(:first)').remove();
					for(var i=0; i<row; i++) {
						$("#btnAdd").click();
						count++;
						//set values
						var d = item[i].split("', '");
						$("#nota").text(count);
						$("#hdnOutgoingTransactionDetailsID" + count).val(d[0].replace("'", ""));
						$("#hdnItemID" + count).val(d[1].replace("'", ""));
						$("#txtQuantity" + count).val(d[2].replace("'", ""));
						$("#txtPrice" + count).val(returnRupiah(d[3].replace("'", "")));
						$("#txtItemName" + count).val(d[4].replace("'", ""));
						$("#txtName" + count).val(d[5].replace("'", ""));
						$("#txtRemarks" + count).val(d[6].replace("'", ""));
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
					
					if($("#ddlProject").val() == "") {
						PassValidate = 0;
						$("#ddlProject").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#ddlProject").next().find("input").focus();
						FirstFocus = 1;
					}
					if(PassValidate == 0) {
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						return false;
					}
					else SubmitForm("./Transaction/OutgoingTransaction/Insert.php");
				}
			}
		</script>
	</body>
</html>
