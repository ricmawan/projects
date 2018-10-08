<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading" style="padding: 1px 15px;">
						 <h5>Detail Stok</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Kode:
							</div>
							<div class="col-md-3">
								<input id="txtItemCode" name="txtItemCode" type="text" class="form-control-custom" style="width: 100%;" onfocus="this.select();" onkeypress="isEnterKey(event, 'validateItemCode');" onchange="validateItemCode();" autocomplete=off placeholder="Kode Barang" />
								<input type="hidden" name="hdnItemID" id="hdnItemID" value=0 />
							</div>
							<div class="col-md-3">
								<input id="txtItemName" name="txtItemName" type="text" class="form-control-custom" style="width: 100%;" placeholder="Nama Barang" readonly />
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-1 labelColumn">
								Satuan:
							</div>
							<div class="col-md-3">
								<select id="ddlUnit" name="ddlUnit" tabindex=13 class="form-control-custom" onchange="changeItemCode();" >
									<option >--</option>
								</select>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-1 labelColumn">
								Cabang:
							</div>
							<div class="col-md-3">
								<select id="ddlBranch" name="ddlBranch" tabindex=8 class="form-control-custom" placeholder="Pilih Cabang" >
									<!--<option value=0 selected >-- Semua Cabang --</option>-->
									<?php
										$sql = "CALL spSelDDLBranch('".$_SESSION['UserLogin']."')";
										if (! $result = mysqli_query($dbh, $sql)) {
											logEvent(mysqli_error($dbh), '/Report/StockDetails/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
											return 0;
										}
										while($row = mysqli_fetch_array($result)) {
											echo "<option value='".$row['BranchID']."' >".$row['BranchCode']." - ".$row['BranchName']."</option>";
										}
										mysqli_free_result($result);
										mysqli_next_result($dbh);
									?>
								</select>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-1 labelColumn">
								Tanggal :
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtFromDate" name="txtFromDate" type="text" class="form-control-custom" style="background-color: #FFF;cursor: text;" placeholder="Dari Tanggal" readonly />
								</div>
							</div>
							<div style="float:left;" class="labelColumn">
								-
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtToDate" name="txtToDate" type="text" class="form-control-custom" style="background-color: #FFF;cursor: text;" placeholder="Sampai Tanggal" readonly />
								</div>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-info" id="btnView" onclick="Preview();" style="padding-top: 1px;padding-bottom: 1px;" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-success" id="btnExcel" onclick="ExportExcel();" style="padding-top: 1px;padding-bottom: 1px;"" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
						<hr style="margin: 10px 0;" />
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th>Tipe Transaksi</th>
										<th>Tanggal</th>
										<th>Pelanggan/Supplier</th>
										<th>Qty</th>
										<th>Stok</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="itemList-dialog" title="Daftar Barang" style="display: none;">
			<div class="row col-md-12" >
				<div id="divTableItem" class="table-responsive" style="overflow-x:hidden;">
					<table id="grid-item" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
						<thead>
							<tr>
								<th>Kode Barang</th>
								<th>Nama Barang</th>
								<th>Satuan</th>
								<th>H Beli</th>
								<th>H Ecer</th>
								<th>H Grosir 1</th>
								<th>QTY1</th>
								<th>H Grosir 2</th>
								<th>QTY2</th>
								<th>Stok</th>
								<th>Fisik</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
		<div id="divBranch" style="display:none;">
			<select class="form-control-custom" placeholder="Pilih Cabang" onchange="branchChange(this.value);" >
				<!--<option value=0 selected >-- Semua Cabang --</option>-->
				<?php
					$sql = "CALL spSelDDLBranch('".$_SESSION['UserLogin']."')";
					if (! $result = mysqli_query($dbh, $sql)) {
						logEvent(mysqli_error($dbh), '/Report/Sale/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
						return 0;
					}
					while($row = mysqli_fetch_array($result)) {
						echo "<option value='".$row['BranchID']."' >".$row['BranchCode']." - ".$row['BranchName']."</option>";
					}
					mysqli_free_result($result);
					mysqli_next_result($dbh);
				?>
			</select>
			<input type="hidden" id="hdnBranchID" name="hdnBranchID" value=1 />
		</div>
		<script>		
			var table;
			var table3;
			var FirstPass = 1;
			var setCookie = function(name, value, expiracy) {
				var exdate = new Date();
				exdate.setTime(exdate.getTime() + expiracy * 1000);
				var c_value = escape(value) + ((expiracy == null) ? "" : "; expires=" + exdate.toUTCString());
				document.cookie = name + "=" + c_value + '; path=/';
			};

			var getCookie = function(name) {
				var i, x, y, ARRcookies = document.cookie.split(";");
				for (i = 0; i < ARRcookies.length; i++) {
					x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
					y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
					x = x.replace(/^\s+|\s+$/g, "");
					if (x == name) {
						return y ? decodeURI(unescape(y.replace(/\+/g, ' '))) : y; //;//unescape(decodeURI(y));
					}
				}
			};

			function changeItemCode() {
				var itemCode = $("#ddlUnit option:selected").attr("itemcode");
				$("#txtItemCode").val(itemCode);
			}

			function Preview() {
				var ItemName = $("#txtItemName").val();
				var BranchID = $("#ddlBranch").val();
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
			
				if(txtFromDate != "" && txtToDate != "") {
					var FromDate = txtFromDate.split("-");
					FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
					var ToDate = txtToDate.split("-");
					ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
					if(FromDate > ToDate) {
						$("#txtToDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						PassValidate = 0;
						if(FirstFocus == 0) $("#txtToDate").focus();
						FirstFocus = 1;
					}
				}

				if(ItemName == "") {
					$("#txtItemCode").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#txtItemCode").focus();
					FirstFocus = 1;
				}
				
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					FirstPass = 0;
					$.ajax({
						url: "./Report/StockDetails/CheckItem.php",
						type: "POST",
						data: { itemCode : $("#txtItemCode").val() },
						dataType: "json",
						success: function(data) {
							$("#loading").hide();
							if(data.FailedFlag == '0') {
								$("#txtItemName").val(data.ItemName);
								if(data.AvailableUnit.length > 0) {
									$("#ddlUnit").find('option').remove();
									for(var i=0;i<data.AvailableUnit.length;i++) {
										$("#ddlUnit").append("<option value=" + data.AvailableUnit[i][0] + " itemcode='" + data.AvailableUnit[i][2] + "' conversionQuantity=" + data.AvailableUnit[i][3] + " >" + data.AvailableUnit[i][1] + "</option>");
									}
								}
								$("#ddlUnit").val(data.UnitID);
								$("#hdnItemID").val(data.ItemID);
								$("#loading").show();
								$("#dvTable").show();
								table.ajax.reload();
								table.columns.adjust();
								$("#loading").hide();
							}
							else {
								//add new item
								if(data.ErrorMessage == "") {
									$("#txtItemCode").notify("Kode tidak valid!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
									$("#txtItemName").val("");
								}
								else {
									var counter = 0;
									Lobibox.alert("error",
									{
										msg: data.ErrorMessage,
										width: 480,
										beforeClose: function() {
											if(counter == 0) {
												setTimeout(function() {
													$("#txtItemCode").focus();
												}, 0);
												counter = 1;
											}
										}
									});
									return 0;
								}
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$("#loading").hide();
							var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
							LogEvent(errorMessage, "/Report/StockDetails/index.php");
							Lobibox.alert("error",
							{
								msg: errorMessage,
								width: 480
							});
							return 0;
						}
					});
				}
			}

			var counterGetItem = 0;
			function validateItemCode() {
				if(counterGetItem == 0) {
					counterGetItem = 1;
					var itemCode = $("#txtItemCode").val();
					if(itemCode == "") $("#txtItemCode").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					else {
						$("#loading").show();
						$.ajax({
							url: "./Report/StockDetails/CheckItem.php",
							type: "POST",
							data: { itemCode : itemCode },
							dataType: "json",
							success: function(data) {
								$("#loading").hide();
								if(data.FailedFlag == '0') {
									$("#txtItemName").val(data.ItemName);
									if(data.AvailableUnit.length > 0) {
										$("#ddlUnit").find('option').remove();
										for(var i=0;i<data.AvailableUnit.length;i++) {
											$("#ddlUnit").append("<option value=" + data.AvailableUnit[i][0] + " itemcode='" + data.AvailableUnit[i][2] + "' conversionQuantity=" + data.AvailableUnit[i][3] + " >" + data.AvailableUnit[i][1] + "</option>");
										}
									}
									$("#ddlUnit").val(data.UnitID);
									$("#hdnItemID").val(data.ItemID);
								}
								else {
									//add new item
									if(data.ErrorMessage == "") {
										$("#txtItemCode").notify("Kode tidak valid!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
										$("#txtItemName").val("");
									}
									else {
										var counter = 0;
										Lobibox.alert("error",
										{
											msg: data.ErrorMessage,
											width: 480,
											beforeClose: function() {
												if(counter == 0) {
													setTimeout(function() {
														$("#txtItemCode").focus();
													}, 0);
													counter = 1;
												}
											}
										});
										return 0;
									}
								}
							},
							error: function(jqXHR, textStatus, errorThrown) {
								$("#loading").hide();
								var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
								LogEvent(errorMessage, "/Report/StockDetails/index.php");
								Lobibox.alert("error",
								{
									msg: errorMessage,
									width: 480
								});
								return 0;
							}
						});
					}
				}
				setTimeout(function() { counterGetItem = 0; }, 1000);
			}

			function ExportExcel() {
				var ItemCode = $("#txtItemCode").val();
				var ItemName = $("#txtItemName").val();
				var BranchID = $("#ddlBranch").val();
				var BranchName = $("#ddlBranch option:selected").text();
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;

				if(txtFromDate != "" && txtToDate != "") {
					var FromDate = txtFromDate.split("-");
					FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
					var ToDate = txtToDate.split("-");
					ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
					if(FromDate > ToDate) {
						$("#txtToDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						PassValidate = 0;
						if(FirstFocus == 0) $("#txtToDate").focus();
						FirstFocus = 1;
					}
				}

				if(ItemName == "") {
					$("#txtItemName").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#txtItemName").next().find("input").focus();
					FirstFocus = 1;
				}		
				
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					$.ajax({
						url: "./Report/StockDetails/CheckItem.php",
						type: "POST",
						data: { itemCode : $("#txtItemCode").val() },
						dataType: "json",
						success: function(data) {
							$("#loading").hide();
							if(data.FailedFlag == '0') {
								$("#txtItemName").val(data.ItemName);
								if(data.AvailableUnit.length > 0) {
									$("#ddlUnit").find('option').remove();
									for(var i=0;i<data.AvailableUnit.length;i++) {
										$("#ddlUnit").append("<option value=" + data.AvailableUnit[i][0] + " itemcode='" + data.AvailableUnit[i][2] + "' conversionQuantity=" + data.AvailableUnit[i][3] + " >" + data.AvailableUnit[i][1] + "</option>");
									}
								}
								$("#ddlUnit").val(data.UnitID);
								$("#hdnItemID").val(data.ItemID);
								var ItemID = data.ItemID;
								var conversionQuantity = $("#ddlUnit option:selected").attr("conversionQuantity");
								FirstPass = 0;
								$("#loading").show();
								setCookie('downloadStarted', 0, 100); //Expiration could be anything... As long as we reset the value
								setTimeout(checkDownloadCookie, 1000); //Initiate the loop to check the cookie.
								$("#excelDownload").attr("src", "Report/StockDetails/ExportExcel.php?BranchID=" + BranchID + "&ItemID=" + ItemID + "&ItemName=" + ItemName + "&BranchName=" + BranchName + "&FromDate=" + txtFromDate + "&ToDate=" + txtToDate + "&conversionQuantity=" + conversionQuantity);
							}
							else {
								//add new item
								if(data.ErrorMessage == "") {
									$("#txtItemCode").notify("Kode tidak valid!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
									$("#txtItemName").val("");
								}
								else {
									var counter = 0;
									Lobibox.alert("error",
									{
										msg: data.ErrorMessage,
										width: 480,
										beforeClose: function() {
											if(counter == 0) {
												setTimeout(function() {
													$("#txtItemCode").focus();
												}, 0);
												counter = 1;
											}
										}
									});
									return 0;
								}
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$("#loading").hide();
							var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
							LogEvent(errorMessage, "/Report/StockDetails/index.php");
							Lobibox.alert("error",
							{
								msg: errorMessage,
								width: 480
							});
							return 0;
						}
					});
				}
			}

			var downloadTimeout;
			var checkDownloadCookie = function() {
				if (getCookie("downloadStarted") == 1) {
					setCookie("downloadStarted", "false", 100); //Expiration could be anything... As long as we reset the value
					$("#loading").hide();
				}
				else {
					downloadTimeout = setTimeout(checkDownloadCookie, 1000); //Re-run this function in 1 second.
				}
			};

			function itemList() {
				$("#itemList-dialog").dialog({
					autoOpen: false,
					open: function() {
						table.keys.disable();
						table3 = $("#grid-item").DataTable({
									"keys": true,
									"scrollY": "295px",
									"scrollX": false,
									"scrollCollapse": false,
									"paging": false,
									"searching": true,
									"order": [],
									"columns": [
										{ "width": "15%", "orderable": false, className: "dt-head-center" },
										{ "width": "20%", "orderable": false, className: "dt-head-center" },
										{ "width": "5%", "orderable": false, className: "dt-head-center" },
										{ "width": "7.5%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "7.5%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "7.5%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "7.5%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-right" }
									],
									"ajax": {
										"url": "./Transaction/Purchase/ItemList.php",
										"data": function ( d ) {
											d.BranchID = $("#ddlBranch").val()
										}
									},
									"processing": true,
									"serverSide": true,
									"language": {
										"info": "",
										"infoFiltered": "",
										"infoEmpty": "",
										"zeroRecords": "Data tidak ditemukan",
										"lengthMenu": "&nbsp;&nbsp;_MENU_ data",
										"search": "Cari",
										"processing": "",
										"paginate": {
											"next": ">",
											"previous": "<",
											"last": "»",
											"first": "«"
										}
									},
									"initComplete": function(settings, json) {
										table3.columns.adjust();
										$("#grid-item").DataTable().cell( ':eq(0)' ).focus();
									},
									"sDom": '<"toolbar">frtip'
								});

						$(".toolbar").css({
							"display" : "inline-block"
						});

						$("div.toolbar").html($("#divBranch").html());

						$("div.toolbar").find("select").val($("#ddlBranch").val());

						var counterPickItem = 0;
						table3.on( 'key', function (e, datatable, key, cell, originalEvent) {
							//var index = table3.cell({ focused: true }).index();
							if(counterPickItem == 0) {
								counterPickItem = 1;
								var data = datatable.row( table3.cell({ focused: true }).index().row ).data();
								if(key == 13 && $("#itemList-dialog").css("display") == "block") {
									$("#txtItemCode").val(data[0]);
									validateItemCode();
									$("#itemList-dialog").dialog("destroy");
									table.keys.enable();
									table3.destroy();
								}
								setTimeout(function() { counterPickItem = 0; } , 1000);
							}
						});
						
						$('#grid-item tbody').on('dblclick', 'tr', function () {
							if( $("#itemList-dialog").css("display") == "block") {
								var data = table3.row(this).data();
								$("#txtItemCode").val(data[0]);
								//$("#txtItemName").val(data[1]);
								validateItemCode();
								$("#itemList-dialog").dialog("destroy");
								table.keys.enable();
								table3.destroy();
							}
						});
						
						table3.on( 'search.dt', function () {
							setTimeout(function() { $("#grid-item").DataTable().cell( ':eq(0)' ).focus(); }, 100 );
						});
						
						var counterKeyItem = 0;
						$(document).on("keydown", function (evt) {
							if(counterKeyItem == 0) {
								counterKeyItem = 1;
								if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0) {
									$("#itemList-dialog").find("input[type='search']").focus();
								}
							}
							setTimeout(function() { counterKeyItem = 0; } , 1000);
						});
					},
					
					close: function() {
						$(this).dialog("destroy");
						table3.destroy();
						table.keys.enable();
					},
					resizable: false,
					height: 420,
					width: 1280,
					modal: true /*,
					buttons: [
					{
						text: "Tutup",
						tabindex: 13,
						id: "btnCancelPickItem",
						click: function() {
							$(this).dialog("destroy");
							table3.destroy();
							table.keys.enable();
							return false;
						}
					}]*/
				}).dialog("open");
			}

			$(document).ready(function () {
				$( window ).resize(function() {
					table.columns.adjust().draw();
				});
				
				$("#txtToDate, #txtFromDate").datepicker({
					dateFormat: 'dd-mm-yy',
					maxDate : "+0D"
				});

				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Transaction/StockDetails/index.php");
					Lobibox.alert("error",
					{
						msg: "Terjadi kesalahan. Memuat ulang halaman.",
						width: 480,
						//delay: 2000,
						beforeClose: function() {
							if(counterError == 0) {
								//location.reload();
								counterError = 1;
							}
						}
					});
				};

				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "280px",
								"scrollX": false,
								"scrollCollapse": false,
								"paging": false,
								"searching": false,
								"order": [],
								"columns": [
									{ "orderable": false, className: "dt-head-center" },
									{ "orderable": false, className: "dt-head-center" },
									{ "orderable": false, className: "dt-head-center" },
									{ "orderable": false, className: "dt-head-center dt-body-right" },
									{ "orderable": false, className: "dt-head-center dt-body-right" }						
								],
								"processing": true,
								"serverSide": true,
								"ajax": {
									"url": "./Report/StockDetails/DataSource.php",
									"data": function ( d ) {
										d.BranchID = $("#ddlBranch").val(),
										d.ItemID = $("#hdnItemID").val(),
										d.FirstPass = FirstPass,
										d.FromDate = $("#txtFromDate").val(),
										d.ToDate = $("#txtToDate").val(),
										d.conversionQuantity = $("#ddlUnit option:selected").attr("conversionQuantity")
									}
								},
								"language": {
									"info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
									"infoFiltered": "",
									"infoEmpty": "",
									"zeroRecords": "Data tidak ditemukan",
									"lengthMenu": "&nbsp;&nbsp;_MENU_ data",
									"search": "Cari",
									"processing": "Memproses",
									"paginate": {
										"next": ">",
										"previous": "<",
										"last": "»",
										"first": "«"
									}
								}
							});
			});

			var counterKey = 0;
			$(document).on("keydown", function (evt) {
				if(evt.keyCode == 123 && $("#itemList-dialog").css("display") == "none") {
					evt.preventDefault();
					if(counterKey == 0) {
						itemList();
						counterKey = 1;
					}
				}
				else if(evt.keyCode == 123) {
					evt.preventDefault();
				}
				setTimeout(function() { counterKey = 0; } , 1000);
			});
		</script>
	</body>
</html>
