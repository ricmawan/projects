<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			#divTableContent {
				min-height: 310px;
				max-height: 310px;
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
									<select id="ddlBranch" name="ddlBranch" tabindex=8 class="form-control-custom" placeholder="Pilih Cabang" >
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
									<input id="hdnSaleReturnID" name="hdnSaleReturnID" type="hidden" value=0 />
									<input id="hdnSaleID" name="hdnSaleID" type="hidden" value=0 />
									<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
								</div>
								<div class="col-md-3 col-sm-3 has-float-label">
									<div class="ui-widget" style="width: 100%;">
										<select id="ddlCategory" name="ddlCategory" tabindex=8 class="form-control-custom" placeholder="Pilih Kategori" >
											<option value="" selected> </option>
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
			var table3;
			var today;
			var rowEdit;
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				$("#txtSaleNumber").focus();
				table2 = $("#grid-transaction").DataTable({
							"keys": false,
							"scrollY": "252px",
							"scrollX": false,
							"scrollCollapse": false,
							"paging": false,
							"searching": false,
							"order": [],
							"columns": [
								{ "width": "20%", "orderable": false, className: "dt-head-center" },
								{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
								{ "width": "10%", "orderable": false, className: "dt-head-center" },
								{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
								{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" }
							],
							"processing": false,
							"serverSide": false,
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
								setTimeout(function() {
									$("#grid-transaction").find("#select_all_salereturn").first().remove()
								}, 0);
							}
						});
				table2.columns.adjust();
			}

			
			var saleDetailsCounter = 0;
			function getSaleDetails() {
				if(saleDetailsCounter == 0 && $("#txtSaleNumber").prop("readonly") == false) 
				{
					saleDetailsCounter = 1;
					var saleNumber = $("#txtSaleNumber").val();
					$.ajax({
						url: "./Transaction/StockAdjust2/SaleDetails.php",
						type: "POST",
						data: { SaleNumber : saleNumber },
						dataType: "json",
						success: function(Data) {
							if(Data.FailedFlag == '0') {
								table2.clear().draw();

								if(Data.data.length > 0) {
									for(var i=0;i<Data.data.length;i++) {
										if(i == 0) {
											$("#hdnSaleID").val(Data.data[i][13]);
											$("#txtCustomerName").val(Data.data[i][12]);
										}
										table2.row.add(Data.data[i]);
									}
									$("#btnSaveSaleReturn").attr("tabindex", Data.tabindex);
									$("#btnCancelAddSaleReturn").attr("tabindex", (parseFloat(Data.tabindex) + 1));
									table2.draw();
									tableWidthAdjust();
									setTimeout(function() {
										$("#grid-transaction").find("#select_all_salereturn").first().remove()
									}, 0);

									$(".txtQTY").spinner({
										stop: function() {
											validateQTY2($(this));
										}
									});

									$("#select_all_salereturn").prop("checked", false);								
									for(var i=0;i<Data.data.length;i++) {
										$("#toggle-branch-" + Data.data[i][0]).toggles({
											drag: true, // allow dragging the toggle between positions
											click: true, // allow clicking on the toggle
											text: {
												on: 'Toko', // text for the ON position
												off: 'Gudang' // and off
											},
											on: true, // is the toggle ON on init
											animate: 250, // animation time (ms)
											easing: 'swing', // animation transition easing function
											checkbox: null, // the checkbox to toggle (for use in forms)
											clicker: null, // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
											width: 80, // width used if not set in css
											height: 18, // height if not set in css
											type: 'compact' // if this is set to 'select' then the select style toggle will be used
										});
										
										if(Data.data[i][2] == 1) $("#toggle-branch-" + Data.data[i][0]).toggles(true);
										else $("#toggle-branch-" + Data.data[i][0]).toggles(false);
												
										if(Data.data[i][13] == 0) {
											$("#toggle-branch-" + Data.data[i][0]).toggles().toggleClass('disabled', true);;
										}
									}
									//Calculate();
									setTimeout(function() {
										$("#grid-transaction").DataTable().cell( ':eq(3)' ).focus();
									}, 0);
									$("#txtTransactionDate").focus();
								}
								else {
									var counter = 0;
									Lobibox.alert("error",
									{
										msg: "No. Invoice tidak valid!",
										width: 480,
										beforeClose: function() {
											if(counter == 0) {
												setTimeout(function() {
													$("#txtSaleNumber").focus();
												}, 0);
												counter = 1;
											}
										}
									});
									return 0;
								}
							}
							else {
								var counter = 0;
								Lobibox.alert("error",
								{
									msg: "Gagal memuat data",
									width: 480
								});
								return 0;
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$("#loading").hide();
							var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
							LogEvent(errorMessage, "/Transaction/SaleReturn/index.php");
							Lobibox.alert("error",
							{
								msg: errorMessage,
								width: 480
							});
							return 0;
						}
					});
				}
				else if($("#txtSaleNumber").prop("readonly") == true) {
					$("#txtTransactionDate").focus();
				}
				setTimeout(function() {
					saleDetailsCounter = 0;
				}, 1000);
			}
			
			function tableWidthAdjust() {
				var tableWidth = $("#divTableContent").find("table").width();
				var barWidth = table2.settings()[0].oScroll.iBarWidth;
				var newWidth = tableWidth - barWidth + 2;
				$("#divTableContent").find("table").css({
					"width": newWidth + "px"
				});
			}
			
			function resetForm() {
				$("#hdnSaleReturnID").val(0);
				$("#hdnSaleID").val(0);
				$("#txtTransactionDate").datepicker("setDate", new Date());
				$("#txtSaleNumber").val("");
				$("#lblTotal").html("0");
				table2.clear().draw();
				$("#select_all_salereturn").prop("checked", false);
				$("#select_all_salereturn").attr("checked", false);
			}
			
			function finish() {
				if($("#hdnSaleID").val() != 0) {
					if($("input:checkbox[class=chkSaleDetails]:checked").length > 0)
					{
						saveConfirm(function(action) {
							if(action == "Ya") {
								$("#loading").show();
								$.ajax({
									url: "./Transaction/SaleReturn/Insert.php",
									type: "POST",
									data: $("#PostForm").serialize(),
									dataType: "json",
									success: function(data) {
										if(data.FailedFlag == '0') {
											$("#loading").hide();
											$("#FormData").dialog("destroy");
											$("#divModal").hide();
											resetForm();
											//table2.destroy();
											var counter = 0;
											Lobibox.alert("success",
											{
												msg: data.Message,
												width: 480,
												delay: 2000
											});
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
							msg: "Minimal pilih 1 data!",
							width: 480,
							beforeClose: function() {
								if(counter == 0) {
									setTimeout(function() {
										$("#select_all_salereturn").focus();
									}, 0);
									counter = 1;
								}
							}
						});
					}
				}
				else {
					var counter = 0;
					Lobibox.alert("error",
					{
						msg: "Silahkan input No. Invoice!",
						width: 480,
						beforeClose: function() {
							if(counter == 0) {
								setTimeout(function() {
									$("#txtSaleNumber").focus();
								}, 0);
								counter = 1;
							}
						}
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

				$("#ddlCategory").combobox();
				
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
				
				$("#txtTransactionDate").datepicker({
					dateFormat: 'DD, dd M yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu" ],
					monthNames: [ "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember" ],
					monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des" ],
					maxDate : "+0D",
					showOn: "button",
					buttonImage: "./assets/img/calendar.gif",
					buttonImageOnly: true,
					buttonText: "Pilih Tanggal",
					onSelect: function(dateText, obj) {
						transactionDate = obj.selectedYear + "-" + ("0" + (obj.selectedMonth + 1)).slice(-2) + "-" + ("0" + obj.selectedDay).slice(-2);
						$("#hdnTransactionDate").val(transactionDate);
					}
				}).datepicker("setDate", new Date());
				
				$("#txtTransactionDate").attr("readonly", "true");
				$("#txtTransactionDate").css({
					"background-color": "#FFF",
					"cursor": "text"
				});
				
				var transactionDate = new Date();
				transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
				today = transactionDate;
				$("#hdnTransactionDate").val(transactionDate);
				
				keyFunction();
				enterLikeTab();
				var counterSaleReturn = 0;
				
				var counterKey = 0;
				$(document).on("keydown", function (evt) {
					if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0 && $("#FormData").css("display") == "none" && $("#delete-confirm").css("display") == "none") {
						$("#grid-data_wrapper").find("input[type='search']").focus();
					}
					setTimeout(function() { counterKey = 0; } , 1000);
				});
				
				openDialog(0, 0);
			});
		</script>
	</body>
</html>