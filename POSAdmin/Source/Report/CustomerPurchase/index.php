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
						 <h5>Omset Pelanggan</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Pelanggan:
							</div>
							<div class="col-md-2">
								<div class="ui-widget" style="width: 100%;">
									<select id="ddlCustomer" name="ddlCustomer" tabindex=7 class="form-control-custom" placeholder="Pilih Pelanggan" >
										<?php
											$sql = "CALL spSelDDLCustomer('".$_SESSION['UserLogin']."')";
											if (! $result = mysqli_query($dbh, $sql)) {
												logEvent(mysqli_error($dbh), '/Master/Sale/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
												return 0;
											}
											while($row = mysqli_fetch_array($result)) {
												echo "<option value='".$row['CustomerID']."' >".$row['CustomerCode']." - ".$row['CustomerName']."</option>";
											}
											mysqli_free_result($result);
											mysqli_next_result($dbh);
										?>
									</select>
								</div>
							</div>
							<div class="col-md-1 labelColumn">
								Tanggal :
							</div>
							<div class="col-md-2">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtFromDate" name="txtFromDate" type="text" class="form-control-custom" style="background-color: #FFF;cursor: text;" placeholder="Dari Tanggal" readonly />
								</div>
							</div>
							<div style="float:left;" class="labelColumn">
								-
							</div>
							<div class="col-md-2">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtToDate" name="txtToDate" type="text" class="form-control-custom" style="background-color: #FFF;cursor: text;" placeholder="Sampai Tanggal" readonly />
								</div>
							</div>
							<div class="col-md-3">
								<button class="btn btn-info" id="btnView" onclick="Preview();" style="padding-top: 1px;padding-bottom: 1px;" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
								<button class="btn btn-success" id="btnExcel" onclick="ExportExcel();" style="padding-top: 1px;padding-bottom: 1px;"" ><i class="fa fa-file-excel-o "></i> Eksport Excel</button>&nbsp;&nbsp;
							</div>
						</div>
						<hr style="margin: 10px 0;" />
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th>No. Invoice</th>
										<th>Tanggal</th>
										<th>Total</th>
									</tr>
								</thead>
								<tfoot id="tfootTable">
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>		
			var table;
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
			function Preview() {
				var CustomerID = $("#ddlCustomer").val();
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

				if(CustomerID == "") {
					$("#ddlCustomer").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlCustomer").next().find("input").focus();
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
					$("#loading").show();
					$("#dvTable").show();
					table.ajax.reload(function(json) {
						$("#tfootTable").html("<tr><td colspan='2'>&nbsp;</td><td>&nbsp;</td><td>Sub Total:</td><td>" + json.SubTotal + "</td></tr><tr><td colspan='2'>&nbsp;</td><td>&nbsp;</td><td>Grand Total:</td><td>" + json.GrandTotal + "</td></tr>");
						$("#tfootTable").find("td").css({
							"border" : "0",
							"font-size" : "14px",
							"font-weight" : "bold",
							"padding-right" : "10px",
							"padding-top" : "5px",
							"padding-bottom" : "5px",
							"text-align" : "right"
						});
					});
					table.columns.adjust();
					$("#loading").hide();
				}
			}
			function ExportExcel() {
				var CustomerID = $("#ddlCustomer").val();
				var CustomerName = $("#ddlCustomer option:selected").text();
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
				
				if(CustomerID == "") {
					$("#ddlCustomer").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlCustomer").next().find("input").focus();
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
					$("#loading").show();
					setCookie('downloadStarted', 0, 100); //Expiration could be anything... As long as we reset the value
					setTimeout(checkDownloadCookie, 1000); //Initiate the loop to check the cookie.
					$("#excelDownload").attr("src", "Report/CustomerPurchase/ExportExcel.php?CustomerID=" + CustomerID + "&CustomerName=" + CustomerName + "&FromDate=" + txtFromDate + "&ToDate=" + txtToDate);
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

			$(document).ready(function () {
				$( window ).resize(function() {
					table.columns.adjust().draw();
				});
				
				$("#ddlCustomer").combobox();
				$("#ddlCustomer").next().find("input").click(function() {
					$(this).val("");
				});

				$("#txtToDate, #txtFromDate").datepicker({
					dateFormat: 'dd-mm-yy',
					maxDate : "+0D"
				});

				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Transaction/CustomerPurchase/index.php");
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
							"destroy": true,
							"keys": true,
							"scrollY": "285px",
							"rowId": "ItemID",
							"scrollCollapse": true,
							"order": [0, "asc"],
							"columns": [
								{ "data": "SaleNumber", className: "dt-head-center" },
								{ "data": "TransactionDate", className: "dt-head-center" },
								{ "data": "Total", "orderable": false, className: "dt-head-center dt-body-right" }
							],
							"processing": true,
							"serverSide": true,
							"ajax": {
								"url": "./Report/CustomerPurchase/DataSource.php",
								"data": function ( d ) {
									d.FromDate = $("#txtFromDate").val(),
									d.ToDate = $("#txtToDate").val(),
									d.CustomerID = $("#ddlCustomer").val(),
									d.FirstPass = FirstPass
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
							},
							"drawCallback": function( settings ) {
						        var json = table.ajax.json();
						        $("#tfootTable").html("<tr><td colspan='2'>&nbsp;</td><td>&nbsp;</td><td>Sub Total:</td><td>" + json.SubTotal + "</td></tr><tr><td colspan='2'>&nbsp;</td><td>&nbsp;</td><td>Grand Total:</td><td>" + json.GrandTotal + "</td></tr>");
								$("#tfootTable").find("td").css({
									"border" : "0",
									"font-size" : "14px",
									"font-weight" : "bold",
									"padding-right" : "10px",
									"padding-top" : "5px",
									"padding-bottom" : "5px",
									"text-align" : "right"
								});
						    }
						});
			});

		</script>
	</body>
</html>
