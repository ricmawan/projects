<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			th[data-column-id="Opsi"] {
				width: 75px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Pembayaran</h5>
					</div>
					<div class="panel-body">
						<div class="col-md-1 labelColumn">
							Pelanggan:
						</div>
						<div class="col-md-3">
							<div class="ui-widget" style="width: 100%;">
								<select name="ddlCustomer" id="ddlCustomer" class="form-control-custom" placeholder="Pilih Pelanggan" >
									<option value="" selected> </option>
									<?php
										$sql = "SELECT CustomerID, CustomerName, Address1, SalesID FROM master_customer ORDER BY CustomerName";
										if(!$result = mysql_query($sql, $dbh)) {
											echo mysql_error();
											return 0;
										}
										while($row = mysql_fetch_array($result)) {
											echo "<option value='".$row['CustomerID']."' salesid='".$row['SalesID']."' >".$row['CustomerName']." - ".$row['Address1']."</option>";
										}
									?>
								</select>
							</div>
						</div>
						<br />
						<br />
						<button class="btn btn-primary" id="btnSearch" ><i class="fa fa-search "></i> Cari</button>&nbsp;
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				$("#ddlCustomer").combobox();
				$("#ddlCustomer").next().find("input").click(function() {
					$(this).val("");
				});
			});
			
			$("#btnSearch").click(function() {
				if($("#ddlCustomer").val() == "") {
					$("#ddlCustomer").next().find("input").notify("Harus dipilih!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				
				
			});
		</script>
	</body>
</html>