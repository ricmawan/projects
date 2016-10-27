<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Rasio Barang</h5>
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Barang :
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
															MI.Price
														FROM 
															master_item MI
														WHERE
															CASE
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
													echo "<option value='".$row['ItemID']."' price='".$row['Price']."' >".$row['ItemCode']." - ".$row['ItemName']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tanggal :
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<input id="txtDate" name="txtDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" />
									</div>
								</div>
							</div>
						</form>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-info" id="btnView" onclick="Preview();" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-info" id="btnPrint" onclick="PrintReport(2);" ><i class="fa fa-print"></i> Print</button>&nbsp;&nbsp;
								<button class="btn btn-danger" id="btnExport" onclick="PrintReport(1);" ><i class="fa fa-file-pdf-o "></i> Eksport PDF</button>&nbsp;&nbsp;
							</div>
						</div>
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="MachineType" >Tipe</th>
										<th data-column-id="MachineCode" >Plat No</th>
										<th data-column-id="StartDate" >Tanggal</th>
										<th data-column-id="StartKilometer" data-align="right">KM Awal</th>
										<th data-column-id="EndDate" >Tanggal</th>
										<th data-column-id="EndKilometer" data-align="right">KM Akhir</th>
										<th data-column-id="Difference" data-align="right">Selisih</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>			
			function Preview() {
				var ItemID = $("#ddlItem").val();
				var txtDate = $("#txtDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(txtDate == "") {
					$("#txtDate").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#txtDate").focus();
					FirstFocus = 1;
				}
				
				if(ItemID == 0) {
					$("#ddlItem").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlItem").next().find("input").focus();
					FirstFocus = 1;
				}

				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					$("#loading").show();
					$("#grid-data").bootgrid('destroy');
					$("#grid-data").bootgrid({
						ajax: true,
						post: function ()
						{
							/* To accumulate custom parameter with the request object */
							return {
								id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
							};
						},
						labels: {
							all: "Semua Data",
							//infos: "Menampilkan {{ctx.start}} sampai {{ctx.end}} dari {{ctx.total}} data",
							infos: "Menampilkan {{ctx.total}} data",
							loading: "Loading...",
							noResults: "Tidak Ada Data Yang Ditemukan!",
							refresh: "Refresh",
							search: "Cari"
						},
						responseHandler: function(response) {
							$(".grandtotal").html(response.GrandTotal);
							return response;
						},
						url: "Report/ItemRatio/DataSource.php?ItemID=" + ItemID  + "&txtDate=" + txtDate,
						selection: true,
						multiSelect: true,
						rowSelect: true,
						keepSelection: true
					});
					$("#dvTable").show();
					$("#loading").hide();
				}
			}

			function PrintReport(PrintType) {
				var ItemID = $("#ddlItem").val();
				var txtDate = $("#txtDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(txtDate == "") {
					$("#txtDate").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#txtDate").focus();
					FirstFocus = 1;
				}

				if(ItemID == 0) {
					$("#ddlItem").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlItem").next().find("input").focus();
					FirstFocus = 1;
				}
				
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					var searchPhrase = $("#grid-data").bootgrid("getSearchPhrase");
					var sortDictionary = JSON.stringify($("#grid-data").bootgrid("getSortDictionary"));
					form = $("#PostForm");
					form.attr("action", "./Report/ItemRatio/Print.php?PrintType=" + PrintType + "&searchPhrase=" + searchPhrase + "&sort=" + sortDictionary);
					if(PrintType == 2) form.attr("target", "_blank");
					else form.removeAttr("target");
					form.submit();
				}
			}

			$(document).ready(function () {				
				$("#ddlItem").combobox();
				$("#ddlItem").next().find("input").click(function() {
					$(this).val("");
				});
			});
		</script>
	</body>
</html>
