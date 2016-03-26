<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$CustomerID = mysql_real_escape_string($_GET['ID']);
		$CustomerName = "";
		$Address = "";
		$City = "";
		$Telephone = "";
		$IsEdit = 0;
		$SalesID = 0;
		
		if($CustomerID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						CustomerID,
						SalesID,
						CustomerName,
						Address,
						City,
						Telephone
					FROM
						master_customer
					WHERE
						CustomerID = $CustomerID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$CustomerID = $row['CustomerID'];
			$CustomerName = $row['CustomerName'];
			$Address = $row['Address'];
			$City = $row['City'];
			$Telephone = $row['Telephone'];
			$SalesID = $row['SalesID'];
		}
	}
?>
<html>
	<head>
		<style>
			#txtAddress {
				height: 100px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Pelanggan</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Pelanggan :
								</div>
								<div class="col-md-3">
									<input id="hdnCustomerID" name="hdnCustomerID" type="hidden" <?php echo 'value="'.$CustomerID.'"'; ?> />
								<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
								<input id="txtCustomerName" maxlength=30 name="txtCustomerName" type="text" class="form-control-custom" placeholder="Nama pelanggan" required   <?php echo 'value="'.$CustomerName.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Telepon :
								</div>
								<div class="col-md-3">
									<input id="txtTelephone" name="txtTelephone" type="text" class="form-control-custom" placeholder="Telepon" <?php echo 'value="'.$Telephone.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Alamat :
								</div>
								<div class="col-md-3">
									<textarea id="txtAddress" name="txtAddress" class="form-control-custom" placeholder="Alamat"> <?php echo $Address; ?></textarea>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Kota :
								</div>
								<div class="col-md-3">
									<input id="txtCity" maxlength=30 name="txtCity" type="text" class="form-control-custom" placeholder="Kota" <?php echo 'value="'.$City.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Sales :
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
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitValidate();" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function SubmitValidate() {
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
						SubmitForm('./Master/Customer/Insert.php')
					}
			}
		</script>
	</body>
</html>