<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$TypeID = mysql_real_escape_string($_GET['ID']);
		$TypeName = "";
		$BrandID = 0;
		$UnitID = "";
		$ReminderCount = 0;
		$BuyPrice = 0.00;
		$SalePrice = 0.00;
		$IsEdit = 0;
		
		if($TypeID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						TypeID,
						TypeName,
						BrandID,
						UnitID,
						ReminderCount,
						BuyPrice,
						SalePrice
					FROM
						master_type
					WHERE
						TypeID = $TypeID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$TypeId = $row['TypeID'];
			$TypeName = $row['TypeName'];
			$BrandID = $row['BrandID'];
			$UnitID = $row['UnitID'];
			$ReminderCount = $row['ReminderCount'];
			$BuyPrice = $row['BuyPrice'];
			$SalePrice = $row['SalePrice'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Barang</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tipe :
								</div>
								<div class="col-md-3">
									<input id="hdnTypeID" name="hdnTypeID" type="hidden" <?php echo 'value="'.$TypeID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnUnitID" name="hdnUnitID" type="hidden" <?php echo 'value="'.$UnitID.'"'; ?> />
									<input id="hdnBrandID" name="hdnBrandID" type="hidden" <?php echo 'value="'.$BrandID.'"'; ?> />
									<input id="txtTypeName" name="txtTypeName" type="text" class="form-control-custom" placeholder="Tipe " required <?php echo 'value="'.$TypeName.'"'; ?> />								
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
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
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Satuan :
								</div>
								<div class="col-md-3">
									<select name="ddlUnit" id="ddlUnit" class="form-control-custom" required >
										<option value="" selected>Pilih Satuan</option>
										<?php
											$sql = "SELECT UnitID, UnitName FROM master_unit";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												echo "<option value='".$row['UnitID']."' >".$row['UnitName']."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Harga Beli :
								</div>
								<div class="col-md-3">
									<input id="txtBuyPrice" name="txtBuyPrice" type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga Beli" style="text-align:right;" <?php echo 'value="'.number_format($BuyPrice,2,".",",").'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Harga Jual :
								</div>
								<div class="col-md-3">
									<input id="txtSalePrice" name="txtSalePrice" type="text" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Harga Jual" style="text-align:right;" <?php echo 'value="'.number_format($SalePrice,2,".",",").'"'; ?> />
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitValidate(this.form);" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				$("#ddlBrand").combobox();
				var IsEdit = $("#hdnIsEdit").val();
				if(IsEdit == 1) {
					$("#ddlBrand option[value='" + $("#hdnBrandID").val() + "']").attr('selected', 'selected');
					$("#ddlUnit option[value='" + $("#hdnUnitID").val() + "']").attr('selected', 'selected');
					$("#ddlBrand").combobox("destroy");
					$("#ddlBrand").combobox();
				}
			});
			function SubmitValidate(form) {
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
				
				if($("#ddlBrand").val() == "") {
					PassValidate = 0;
					$(".custom-combobox-input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $(".custom-combobox-input").focus();
					FirstFocus = 1;
				}
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else SubmitForm("./Master/Type/Insert.php");
			}
		</script>
	</body>
</html>
