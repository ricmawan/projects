<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			#divTableContent {
				min-height: 280px;
				max-height: 280px;
				overflow-y: auto;
			}

			.chkSaleDetails {
				margin-top : 0 !important;
			}
			.ui-spinner {
				width: 100%;
			}

			.btn-mobile {
				padding: 2px 12px;
				vertical-align: baseline;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<span style="width:50%;display:inline-block;">
							 <h5 style="margin-bottom: 5px;margin-top: 5px;">Stock Opname</h5>
						</span>
						<span style="width:49%;display:inline-block;text-align:right;">
							<button id="btnSave" class="btn btn-default btn-mobile" onclick="finish();"><i class="fa fa-save "></i> Simpan</button>&nbsp;
						</span>
					</div>
					<div class="panel-body">
						<form class="col-md-12 col-sm-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-3 col-sm-3 has-float-label" >
									<select id="ddlBranch" name="ddlBranch" tabindex=8 class="form-control-custom" placeholder="Pilih Cabang" onchange="ReloadTable()" >
										<?php
											$sql = "CALL spSelDDLBranch('".$_SESSION['UserLogin']."')";
											if (! $result = mysqli_query($dbh, $sql)) {
												logEvent(mysqli_error($dbh), '/Master/FirstStock/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
												return 0;
											}
											while($row = mysqli_fetch_array($result)) {
												echo "<option value='".$row['BranchID']."' >".$row['BranchCode']." - ".$row['BranchName']."</option>";
											}
											mysqli_free_result($result);
											mysqli_next_result($dbh);
										?>
									</select>
									<label for="ddlBranch" class="lblInput" >Cabang</label>
									<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
									<input id="hdnCategoryID" name="hdnCategoryID" type="hidden" />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
								</div>
								<div class="col-md-3 col-sm-3 has-float-label">
									<div class="ui-widget" style="width: 100%;">
										<select id="ddlCategory" name="ddlCategory" onchange="ReloadTable();" tabindex=8 class="form-control-custom" placeholder="Pilih Kategori" >
											<option value=0 selected>-- Pilih Kategori -- </option>
											<?php
												$sql = "CALL spSelDDLCategory('".$_SESSION['UserLogin']."')";
												if (! $result = mysqli_query($dbh, $sql)) {
													logEvent(mysqli_error($dbh), '/Master/Item/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
													return 0;
												}
												while($row = mysqli_fetch_array($result)) {
													echo "<option value='".$row['CategoryID']."' >".$row['CategoryCode']." - ".$row['CategoryName']."</option>";
												}
												mysqli_free_result($result);
												mysqli_next_result($dbh);
											?>
										</select>
										<label for="ddlCategory" class="lblInput" >Kategori</label>
									</div>
								</div>
							</div>
							<hr style="margin: 5px 0 0 0;" />
							<div class="row" >
								<div id="divTableContent" class="table-responsive" style="overflow-x:hidden;">
									<table id="grid-transaction" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
										<thead>
											<tr>
												<th>ID Barang</th>
												<th>Kode Barang</th>
												<th>Nama Barang</th>
												<th>Stok Program</th>
												<th>Stok Fisik Program</th>
												<th>Stok Fisik Asli</th>
											</tr>
										</thead>
									</table>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			var table2;
			var today;
			var rowEdit;
			var dataJSON = [];
			var ddlCategory = 0;

			function ReloadTable() {
				if(ddlCategory == $("#ddlCategory").val()) {
					table2.ajax.reload(function() {
						table2.columns.adjust();
						tableWidthAdjust();
					}, false);
				}
				else {
					table2.ajax.reload(function() {
						table2.columns.adjust();
						tableWidthAdjust();
					});
				}
				ddlCategory = $("#ddlCategory").val();
			}

			function addData(ItemID, Quantity, AdjustedQuantity, BuyPrice, SalePrice) {
				var addFlag = 1;
				for(var i=dataJSON.length-1;i>=0;i--) {
					if(dataJSON[i].ItemID == ItemID) {
						addFlag = 0;
						dataJSON.splice(i, 1);
						if(Quantity != parseFloat(AdjustedQuantity)) {
							dataJSON.push({"ItemID":ItemID, "Quantity": Quantity, "AdjustedQuantity": parseFloat(AdjustedQuantity), "BuyPrice" : BuyPrice, "SalePrice" : SalePrice });
						}
					}
				}
				if (addFlag == 1) dataJSON.push({"ItemID":ItemID, "Quantity": Quantity, "AdjustedQuantity": parseFloat(AdjustedQuantity), "BuyPrice" : BuyPrice, "SalePrice" : SalePrice });
			}
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				$("#txtSaleNumber").focus();
				table2 = $("#grid-transaction").DataTable({
							"keys": true,
							"scrollY": "176px",
							"scrollX": false,
							"scrollCollapse": false,
							"paging": true,
							"searching": false,
							"order": [],
							"lengthChange": false,
							"pageLength": 100,
							"columns": [
								{ "visible": false },
								{ "width": "20%", "orderable": false, className: "dt-head-center" },
								{ "width": "35%", "orderable": false, className: "dt-head-center" },
								{ "width": "15%", "orderable": false, className: "dt-head-center dt-body-right" },
								{ "width": "15%", "orderable": false, className: "dt-head-center dt-body-right" },
								{ "width": "15%", "orderable": false, className: "dt-head-center dt-body-right" }
							],
							"ajax": {
								"url": "./Transaction/StockAdjust/ItemList.php",
								"data": function ( d ) {
									d.BranchID = $("#ddlBranch").val(),
									d.CategoryID = $("#ddlCategory").val()
								}
							},
							"processing": true,
							"serverSide": true,
							"language": {
								"info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
								"infoFiltered": "",
								"infoEmpty": "",
								"zeroRecords": "Data tidak ditemukan",
								"lengthMenu": "&nbsp;&nbsp;_MENU_ data",
								"search": "Cari",
								"processing": "Loading",
								"paginate": {
									"next": ">",
									"previous": "<",
									"last": "»",
									"first": "«"
								}
							},
							"initComplete": function(settings, json) {
								setTimeout(function() {
									$("#grid-transaction").find("#select_all_salereturn").first().remove();
									table2.columns.adjust();
									tableWidthAdjust();
								}, 0);
							}
						});

				table2.on('page', function(e) {
					if(dataJSON.length > 0) {
						Lobibox.alert("error",
						{
							msg: "Terjadi perubahan pada data. Harap simpan perubahan!",
							width: 480
						});
						e.preventDefault();
					}
				});
			}
			
			function tableWidthAdjust() {
				var tableWidth = $("#divTableContent").find("table").width();
				var barWidth = table2.settings()[0].oScroll.iBarWidth;
				var newWidth = tableWidth - barWidth + 2;
				$("#divTableContent").find("table").css({
					"width": newWidth + "px"
				});
			}
			
			function finish() {
				if(dataJSON.length > 0)
				{
					saveConfirm(function(action) {
						if(action == "Ya") {
							$("#loading").show();
							$.ajax({
								url: "./Transaction/StockAdjust/Insert.php",
								type: "POST",
								data: { dataJSON : JSON.stringify(dataJSON), TransactionDate : $("#hdnTransactionDate").val(), BranchID : $("#ddlBranch").val() },
								dataType: "json",
								success: function(data) {
									if(data.FailedFlag == '0') {
										$("#loading").hide();
										$("#FormData").dialog("destroy");
										$("#divModal").hide();
										var counter = 0;
										Lobibox.alert("success",
										{
											msg: data.Message,
											width: 480,
											delay: 2000
										});
										dataJSON = [];
										ReloadTable();
									}
									else {
										$("#loading").hide();
										var counter = 0;
										Lobibox.alert("warning",
										{
											msg: data.Message,
											width: 480,
											delay: false
										});
										return 0;
									}
								},
								error: function(jqXHR, textStatus, errorThrown) {
									$("#loading").hide();
									var counter = 0;
									var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
									LogEvent(errorMessage, "/Master/Item/index.php");
									Lobibox.alert("error",
									{
										msg: errorMessage,
										width: 480
									});
									return 0;
								}
							});
						}
						else {
							return false;
						}
					});
				}
				else {
					var counter = 0;
					Lobibox.alert("error",
					{
						msg: "Minimal ubah 1 data!",
						width: 480
					});
				}
			}
			
			var waitForFinalEvent = (function () {
		        var timers = {};
		        return function (callback, ms, uniqueId) {
		            if (!uniqueId) {
		                uniqueId = "Don't call this twice without a uniqueId";
		            }
		            if (timers[uniqueId]) {
		                clearTimeout(timers[uniqueId]);
		            }
		            timers[uniqueId] = setTimeout(callback, ms);
		        };
		    })();
			
			$(document).ready(function() {
				$( window ).resize(function() {
					waitForFinalEvent(function () {
		               	setTimeout(function() {
							if ( $.fn.DataTable.isDataTable( '#grid-transaction' ) ) {
								tableWidthAdjust();
							}
							if ( $.fn.DataTable.isDataTable( '#grid-item' ) ) {
								table3.columns.adjust().draw();
							}
						}, 0);
		            }, 500, "resizeWindow");
				});
				
				$('#grid-data').on('click', 'input[type="checkbox"]', function() {
				    $(this).blur();
				});
				
				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Transaction/SaleReturn/index.php");
					Lobibox.alert("error",
					{
						msg: "Terjadi kesalahan. Memuat ulang halaman.",
						width: 480,
						//delay: 2000,
						beforeClose: function() {
							if(counterError == 0) {
								location.reload();
								counterError = 1;
							}
						}
					});
				};
				
				var transactionDate = new Date();
				transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
				today = transactionDate;
				$("#hdnTransactionDate").val(transactionDate);
				
				keyFunction();
				enterLikeTab();
				var counterSaleReturn = 0;
				
				var counterKey = 0;
				$(document).on("keydown", function (evt) {
					
					setTimeout(function() { counterKey = 0; } , 1000);
				});
				
				openDialog(0, 0);
			});
		</script>
	</body>
</html>