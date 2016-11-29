<?php
	if(isset($_POST['TableID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$TableID = mysql_real_escape_string($_POST['TableID']);
		$TableNumber = mysql_real_escape_string($_POST['TableNumber']);
		$TransactionDate = date('d')."-".date('m')."-".date('Y');
		$IsEdit = 0;
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Pemesanan</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nomor Meja :
								</div>
								<div class="col-md-4 labelColumn">
									<?php echo $TableNumber ?>
								</div>
								<div class="col-md-2 labelColumn">
									Tanggal :
								</div>
								<div class="col-md-4">
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnTableID" name="hdnTableID" type="hidden" <?php echo 'value="'.$TableID.'"'; ?> />
									<input id="hdnSaleID" name="hdnSaleID" type="hidden" value=0 />
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Makanan :
								</div>
								<div class="col-md-4">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlFood" id="ddlFood" class="form-control-custom" placeholder="Pilih Makanan" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT MenuListID, MenuName, Price FROM master_menulist WHERE MenuListCategoryID = 1 ORDER BY MenuName";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['MenuListID']."' price=".$row['Price']." >".$row['MenuName']."</option>";
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-2 labelColumn">
									Minuman :
								</div>
								<div class="col-md-4">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlDrink" id="ddlDrink" class="form-control-custom" placeholder="Pilih Minuman" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT MenuListID, MenuName, Price FROM master_menulist WHERE MenuListCategoryID = 2 ORDER BY MenuName";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['MenuListID']."' price=".$row['Price']." >".$row['MenuName']."</option>";
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
										<thead style="background-color: black;color:white;height:35px;width:1000px;display:block;">
											<td align="center" style="width:42px;border-right:solid 1px white;border-left:solid 1px black;">No</td>
											<td align="center" style="width:294px;border-right:solid 1px white;">Nama Menu</td>
											<td align="center" style="width:64px;border-right:solid 1px white;">QTY</td>
											<td align="center" style="width:154px;border-right:solid 1px white;">Harga Jual</td>
											<td align="center" style="width:199px;border-right:solid 1px white;">Diskon</td>
											<td align="center" style="width:174px;">Total</td>
											<td style="width: 34px"></td>
										</thead>
										<tbody style="display:block;max-height:245px;height:100%;overflow-y:auto;">
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota' style="width:42px;vertical-align:middle;border: solid 1px black;"></td>
												<td style="width:294px;border-right: solid 1px black;border-bottom:solid 1px black;">
													<input type="text" id="txtMenuName" name="txtMenuName" class="form-control-custom txtMenuName" style="border:none;background-color:white;" readonly ></span>
													<input type="hidden" id="hdnMenuListID" name="hdnMenuListID" value="0" class="hdnMenuListID" />
													<input type="hidden" id="hdnSaleDetailsID" class="hdnSaleDetailsID" name="hdnSaleDetailsID" value="0" />
												</td>
												<td style="width:64px;border-right: solid 1px black;border-bottom:solid 1px black;">
													<input type="text" row="" value=1 id="txtQuantity" autocomplete=off style="text-align:right;border:none;" onClick="this.select();" name="txtQuantity" onchange="Calculate();" onkeypress="return isNumberKey(event)"  class="form-control-custom txtQuantity" placeholder="QTY"/>
												</td>
												<td style="width:154px;border-right: solid 1px black;border-bottom:solid 1px black;">
													<input type="text" id="txtPrice" value="0.00" autocomplete=off name="txtPrice" style="text-align:right;border:none;" class="form-control-custom txtPrice" onClick="this.select();" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga"/>
												</td>
												<td style="width:199px;border-right: solid 1px black;border-bottom:solid 1px black;">
													<input type="text" id="txtDiscount" autocomplete=off style="display:inline-block;width: 120px;text-align:right;border:none;" value="0" name="txtDiscount" onClick="this.select();" style="text-align:right;" class="form-control-custom txtDiscount" onchange="ValidateDiscount(this.getAttribute('row'))" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" placeholder="Diskon" /> 
													<select id="ddlDiscount" name="ddlDiscount" class="ddlDiscount" onchange="Calculate();">
														<option value="true">%</option>
														<option value="false">Rp</option>
													</select>
												</td>
												<td  style="width:174px;border-right: solid 1px black;border-bottom:solid 1px black;">
													<input type="text" id="txtTotal" name="txtTotal" class="form-control-custom txtTotal" style="text-align:right;border:none;background-color:white;" value="0.00" placeholder="Jumlah" readonly />
												</td>
												<td style="vertical-align:middle;border-right: solid 1px black;border-bottom:solid 1px black;">
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
									Diskon :
								</div>
								<div class="col-md-3">
									<input type="text" id="txtDiscountTotal" value="0" onClick="this.select();" style="text-align:right;" name="txtDiscountTotal" class="form-control-custom" onchange="ValidateDiscountTotal();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" /> &nbsp;
								</div>
								<div class="col-md-1">
									<select id="ddlDiscountTotal" name="ddlDiscountTotal" onchange="Calculate();" >
										<option value="true">%</option>
										<option value="false">Rp</option>
									</select>
								</div>
								<div class="col-md-2">
									Grand Total :
								</div>
								<div class="col-md-3">
									<input type="text" id="txtGrandTotal" style="text-align:right;" value="0.00" name="txtGrandTotal" class="form-control-custom" readonly />
								</div>
							</div>
						</form>
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-default" id="btnAdd" style="display:none;" ><i class="fa fa-save "></i> Add</button>&nbsp;&nbsp;								
								<button class="btn btn-default" id="btnSave"  onclick="SubmitValidate();" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
								<button type="button" class="btn btn-default" value="Kembali" onclick='Reload();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function BindFood() {
				var i = 1;
				var CurrentFoodID = $("#ddlFood").val();
				var CurrentPrice = $("#ddlFood option:selected").attr("price");
				var CurrentFoodName = $("#ddlFood option:selected").text();
				var AddFlag = 1;
				var rows = $("#recordnew").val();
				//QTY + 1 if selected item already exists
				for(i=1;i<=rows;i++) {
					if($("#hdnMenuListID" + i).val() == CurrentFoodID) {
						$("#txtQuantity" + i).val((parseInt($("#txtQuantity" + i).val()) + 1));
						AddFlag = 0;
					}
				}
				//if(AddFlag == 1) {
					$("#btnAdd").click();
					rows = $("#recordnew").val();
					$("#hdnMenuListID" + rows).val(CurrentFoodID);
					$("#txtMenuName" + rows).val(CurrentFoodName);
					$("#txtPrice" + rows).val(returnRupiah(CurrentPrice.toString()));
					$("#txtQuantity" + rows).val(1);
					$("#txtTotal" + rows).val(CurrentPrice);
				//}
				Calculate();
			}
			
			function BindDrink() {
				var i = 1;
				var CurrentDrinkID = $("#ddlDrink").val();
				var CurrentPrice = $("#ddlDrink option:selected").attr("price");
				var CurrentDrinkName = $("#ddlDrink option:selected").text();
				var AddFlag = 1;
				var rows = $("#recordnew").val();
				//QTY + 1 if selected item already exists
				for(i=1;i<=rows;i++) {
					if($("#hdnMenuListID" + i).val() == CurrentDrinkID) {
						$("#txtQuantity" + i).val((parseInt($("#txtQuantity" + i).val()) + 1));
						AddFlag = 0;
					}
				}
				if(AddFlag == 1) {
					$("#btnAdd").click();
					rows = $("#recordnew").val();
					$("#hdnMenuListID" + rows).val(CurrentDrinkID);
					$("#txtMenuName" + rows).val(CurrentDrinkName);
					$("#txtPrice" + rows).val(returnRupiah(CurrentPrice.toString()));
					$("#txtQuantity" + rows).val(1);
					$("#txtTotal" + rows).val(CurrentPrice);
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
				$(".hdnMenuListID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnMenuListID" + i);
						$(this).attr("name", "hdnMenuListID" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnSaleDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnSaleDetailsID" + i);
						$(this).attr("name", "hdnSaleDetailsID" + i);
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
				$(".txtMenuName").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtMenuName" + i);
						$(this).attr("name", "txtMenuName" + i);
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
				$(".ddlDiscount").each(function() {
					if(i != 0) {
						$(this).attr("id", "ddlDiscount" + i);
						$(this).attr("name", "ddlDiscount" + i);
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
						disc = $("#txtDiscount" + row).val().replace(/\,/g, "");
						isPercentage = $("#ddlDiscount" + row).val();
						if(qty == "") {
							$(this).val(1);
							qty = 1;
						}
						else if(price == "") {
							$("#txtPrice" + row).val("0.00");
							price = 0;
						}
						if(isPercentage == "true") {
							price = price - ((price * disc)/ 100);
						}
						else {
							price = price - disc;
						}
						GrandTotal += parseFloat(qty) * parseFloat(price);
						Total = parseFloat(qty) * parseFloat(price);
						$("#txtTotal" + row).val(returnRupiah(Total.toFixed(2).toString()));
					}
					i++;
				});
				var discTotal = $("#txtDiscountTotal").val().replace(/\,/g, "");
				if($("#ddlDiscountTotal").val() == "true") {
					GrandTotal = GrandTotal - ((GrandTotal * discTotal)/ 100);
				}
				else {
					GrandTotal = GrandTotal - discTotal;
				}
				$("#txtGrandTotal").val(returnRupiah(GrandTotal.toFixed(2).toString()));
			}
			
			function ValidateDiscount(row) {
				var IsPercentage = $("#ddlDiscount" + row).val();
				var Discount = $("#txtDiscount" + row);
				if(IsPercentage == "true") {
					Discount.val(minmax(Discount.val().replace(/\,/g, "").replace(/\.00/g, ""), 0, 100));
				}
				else {
					convertRupiah("txtDiscount" + row, Discount.val());
				}
				Calculate();
			}
			
			function ValidateDiscountTotal() {
				var IsPercentage = $("#ddlDiscountTotal").val();
				var Discount = $("#txtDiscountTotal");
				if(IsPercentage == "true") {
					Discount.val(minmax(Discount.val().replace(/\,/g, "").replace(/\.00/g, ""), 0, 100));
				}
				else {
					convertRupiah("txtDiscountTotal", Discount.val());
				}
				Calculate();
			}
			
			$(document).ready(function () {
				$("#ddlFood").combobox({
					select: function(event, ui) {
						BindFood();
						setTimeout(function() {
							$("#ddlFood").next().find("input").val("");
							$("#ddlFood").val("");
						}, 0);
					}
				});
				
				$("#ddlDrink").combobox({
					select: function(event, ui) {
						BindDrink();
						setTimeout(function() {
							$("#ddlDrink").next().find("input").val("");
							$("#ddlDrink").val("");
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
					Calculate();
				});
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
						$.ajax({
							url: "./Transaction/Order/Insert.php",
							type: "POST",
							data: $("#PostForm").serialize(),
							dataType: "json",
							success: function(data) {
								if(data.FailedFlag == '0') {
									$.notify(data.Message, "success");
									Reload();
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
