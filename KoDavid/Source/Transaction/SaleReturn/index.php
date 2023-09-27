<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			#divTableContent {
				min-height: 380px;
				max-height: 380px;
				overflow-y: auto;
			}

			.chkSaleDetails {
				margin-top : 0 !important;
			}
			.ui-spinner {
				width: 100%;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <span style="width:50%;display:inline-block;">
							 <h5>Retur Penjualan</h5>
						</span>
						<span style="width:49%;display:inline-block;text-align:right;">
							<button id="btnAdd" class="btn btn-primary" onclick="openDialog(0, 0);"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
							<?php
								if($DeleteFlag == true) echo '<button id="btnDelete" class="btn btn-danger" onclick="fnDeleteData();" ><i class="fa fa-close"></i> Hapus</button>';
								echo '<input id="hdnEditFlag" name="hdnEditFlag" type="hidden" value="'.$EditFlag.'" />';
								echo '<input id="hdnDeleteFlag" name="hdnDeleteFlag" type="hidden" value="'.$DeleteFlag.'" />';
							?>
						</span>
					</div>
					<div class="panel-body">
						<div class="table-responsive" style="overflow-x:hidden;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th><input id="select_all" name="select_all" type="checkbox" onclick="chkAll();" /></th>
										<th>No</th>
										<th>No. Invoice</th>
										<th>Tanggal</th>
										<th>Customer</th>
										<th>Total</th>
									</tr>
								</thead>
							</table>
						</div>
						<br />
						<div class="row col-md-12" >
							<h5>INSERT = Tambah Data; ENTER/DOUBLE KLIK = Edit; DELETE = Hapus; SPASI = Menandai Data;</h5>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="FormData" title="Tambah Kategori" style="display: none;">
			<form class="col-md-12" id="PostForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-1 labelColumn">
						No. Invoice :
						<input id="hdnSaleReturnID" name="hdnSaleReturnID" type="hidden" value=0 />
						<input id="hdnSaleID" name="hdnSaleID" type="hidden" value=0 />
						<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
					</div>
					<div class="col-md-2">
						<input id="txtSaleNumber" name="txtSaleNumber" type="text" tabindex=5 class="form-control-custom" onfocus="this.select();" onkeypress="isEnterKey(event, 'getSaleDetails');" onchange="getSaleDetails();" autocomplete=off placeholder="No. Invoice" />
					</div>
					
					<div class="col-md-1 labelColumn">
						Tanggal :
					</div>
					<div class="col-md-2">
						<input id="txtTransactionDate" name="txtTransactionDate" type="text" tabindex=6 class="form-control-custom" style="width: 87%; display: inline-block;margin-right: 5px;" onfocus="this.select();" autocomplete=off placeholder="Tanggal" required />
					</div>
					
					<div class="col-md-1 labelColumn">
						Pelanggan :
					</div>
					<div class="col-md-2">
						<input id="txtCustomerName" name="txtCustomerName" type="text" class="form-control-custom" readonly />
					</div>
				</div>
				<hr style="margin: 5px 0 0 0;" />
				<div class="row" >
					<div id="divTableContent" class="table-responsive" style="overflow-x:hidden;">
						<table id="grid-transaction" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th>SaleReturnDetailsID</th>
									<th>ItemID</th>
									<th>BranchID</th>
									<th><input id="select_all_salereturn" name="select_all_salereturn" type="checkbox" onclick="checkAllSaleReturn();" tabindex=7 /></th>
									<th>Cabang</th>
									<th>Kode Barang</th>
									<th>Nama Barang</th>
									<th>Qty</th>
									<th>Satuan</th>
									<th>Harga Jual</th>
									<th>Sub Total</th>
									<th>BuyPrice</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="row" >
					<h2 style="display: inline-block;float: left;" >TOTAL : &nbsp;</h2><span id="lblTotal" >0</span>
				</div>
				<br />
				<div class="row" >
					<h5>F10 = Transaksi Selesai; F12 = Daftar Transaksi; ESC = Tutup;</h5>
				</div>
			</form>
		</div>
		<div id="transactionList-dialog" title="Daftar Transaksi" style="display: none;">
			<div class="row col-md-12" >
				<div id="divTableItem" class="table-responsive" style="overflow-x:hidden;">
					<table id="grid-sale" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
						<thead>
							<tr>
								<th>No. Invoice</th>
								<th>Tanggal</th>
								<th>Customer</th>
								<th>Total</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
		<script>
			var table;
			var table2;
			var table3;
			var today;
			var rowEdit;
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#FormData").attr("title", "Edit Retur Penjualan");
					$("#hdnSaleReturnID").val(Data[6]);
					$("#txtCustomerName").val(Data[4]);
					$("#txtSaleNumber").val(Data[2]);
					$("#lblTotal").html(Data[5]);
					$("#txtTransactionDate").datepicker("setDate", new Date(Data[7]));
					$("#hdnTransactionDate").val(Data[7]);
					$("#txtSaleNumber").prop("readonly", true);
					getSaleReturnDetails(Data[6]);
				}
				else {
					$("#FormData").attr("title", "Tambah Retur Penjualan");
					$("#txtSaleNumber").prop("readonly", false);
				}
				var index = table.cell({ focused: true }).index();
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0 && $("select:focus").length == 0) { //right arrow
								$("#btnCancelAddSaleReturn").focus();
							}
							else if(e.keyCode == 37 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0 && $("select:focus").length == 0) { //left arrow
								$("#btnSaveSaleReturn").focus();
							}
						});
						setTimeout(function() {
							$("#txtSaleNumber").focus();
						}, 0);
						table.keys.disable();
						table2 = $("#grid-transaction").DataTable({
									"destroy": true,
									"keys": false,
									"scrollY": "330px",
									"scrollX": false,
									"scrollCollapse": false,
									"paging": false,
									"searching": false,
									"order": [],
									"columns": [
										{ "visible": false },
										{ "visible": false },
										{ "visible": false },
										{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-center" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-center" },
										{ "width": "15%", "orderable": false, className: "dt-head-center" },
										{ "width": "20%", "orderable": false, className: "dt-head-center" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "10%", "orderable": false, className: "dt-head-center" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "visible": false }
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
						$("#divModal").show();
					},
					
					close: function() {
						$(this).dialog("destroy");
						$("#divModal").hide();
						table.ajax.reload(function() {
							table.keys.enable();
							if(typeof index !== 'undefined') table.cell(index).focus();
						}, false);
						resetForm();
						table2.destroy();
					},
					resizable: false,
					height: 620,
					width: 1280,
					modal: false,
					buttons: [
					{
						text: "Simpan",
						id: "btnSaveSaleReturn",
						click: function() {
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
														table2.destroy();
														var counter = 0;
														Lobibox.alert("success",
														{
															msg: data.Message,
															width: 480,
															delay: 2000,
															beforeClose: function() {
																if(counter == 0) {
																	table.keys.enable();
																	counter = 1;
																}
															},
															shown: function() {
																setTimeout(function() {
																	table.ajax.reload(function() {
																		table.keys.enable();
																		if(typeof index !== 'undefined') table.cell(index).focus();
																		table.keys.disable();
																	}, false);
																}, 0);
															}
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
					},
					{
						text: "Tutup",
						id: "btnCancelAddSaleReturn",
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
							table.ajax.reload(function() {
								table.keys.enable();
								if(typeof index !== 'undefined') table.cell(index).focus();
							}, false);
							resetForm();
							table2.destroy();
							return false;
						}
					}]
				}).dialog("open");
			}

			function updateBranch(SaleDetailsID) {
				setTimeout(function() {
					var BranchID = 1;
					var str = SaleDetailsID.split("-");
					if($('#toggle-branch-' + str[2]).data('toggles').active == false) BranchID = 2;
					$("#hdnBranchID" + str[2]).val(BranchID);
				}, 0);
			}

			function checkAllSaleReturn() {
				if($("#select_all_salereturn").prop("checked") == true) {
					$("input:checkbox[class=chkSaleDetails]").each(function() {
						if($(this).prop("disabled") == false) {
							$(this).prop("checked", true);
							$(this).attr("checked", true);
						}
					});
				}
				else {
					$("input:checkbox[class=chkSaleDetails]").each(function() {
						$(this).prop("checked", false);
						$(this).attr("checked", false);
					});
				}
				Calculate();
			}
			
			function getSaleReturnDetails(SaleReturnID) {
				$.ajax({
					url: "./Transaction/SaleReturn/SaleReturnDetails.php",
					type: "POST",
					data: { SaleReturnID : SaleReturnID },
					dataType: "json",
					success: function(Data) {
						if(Data.FailedFlag == '0') {
							for(var i=0;i<Data.data.length;i++) {
								table2.row.add(Data.data[i]).draw();
							}
							//table2.draw();
							tableWidthAdjust();
							setTimeout(function() {
								$("#grid-transaction").find("#select_all_salereturn").first().remove()
							}, 0);
							$("#btnSaveSaleReturn").attr("tabindex", Data.tabindex);
							$("#btnCancelAddSaleReturn").attr("tabindex", (parseFloat(Data.tabindex) + 1));

							$(".txtQTY").spinner({
								stop: function() {
									validateQTY2($(this));
								}
							});
							
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
							}

							$("#select_all_salereturn").click();
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

			var saleDetailsCounter = 0;
			function getSaleDetails() {
				if(saleDetailsCounter == 0 && $("#txtSaleNumber").prop("readonly") == false) 
				{
					saleDetailsCounter = 1;
					var saleNumber = $("#txtSaleNumber").val();
					$.ajax({
						url: "./Transaction/SaleReturn/SaleDetails.php",
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
										table2.row.add(Data.data[i]).draw();
									}
									$("#btnSaveSaleReturn").attr("tabindex", Data.tabindex);
									$("#btnCancelAddSaleReturn").attr("tabindex", (parseFloat(Data.tabindex) + 1));
									//table2.draw();
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

			function validateQTY2(el) {
				if(parseFloat(el.value) > parseFloat(el.max)) {
					el.value = el.max;
					$(el).notify("Maksimal: " + el.max, { position:"right", className:"warn", autoHideDelay: 2000 });
				}
				else if(parseFloat(el.value) <=0 ) {
					el.value = 1;
					$(el).notify("Minimal: 1", { position:"right", className:"warn", autoHideDelay: 2000 });
				}
				Calculate();
			}
			
			function Calculate() {
				var grandTotal = 0;
				$("input:checkbox[class=chkSaleDetails]:checked").each(function() {
					var qty = parseFloat($(this).closest("tr").find("input[type=number]").val());
					var salePrice = parseFloat($(this).closest("tr").find("label").html().replace(/\,/g, ""));
					var subTotal = qty * salePrice; 
					grandTotal += subTotal;
					$(this).closest("tr").find("td").last().html(returnRupiah(subTotal.toString()));
				});
				$("input:checkbox[class=chkSaleDetails]:not(:checked)").each(function() {
					$(this).closest("tr").find("td").last().html("0");
				});
				$("#lblTotal").html(returnRupiah(grandTotal.toString()));
			}
			
			function tableWidthAdjust() {
				/*var tableWidth = $("#divTableContent").find("table").width();
				var barWidth = table2.settings()[0].oScroll.iBarWidth;
				var newWidth = tableWidth - barWidth + 2;
				$("#divTableContent").find("table").css({
					"width": newWidth + "px"
				});*/
				table2.columns.adjust().draw();
			}
			
			function resetForm() {
				$("#hdnSaleReturnID").val(0);
				$("#hdnSaleID").val(0);
				$("#txtTransactionDate").datepicker("setDate", new Date());
				var transactionDate = new Date();
				transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
				today = transactionDate;
				$("#hdnTransactionDate").val(transactionDate);
				$("#txtSaleNumber").val("");
				$("#lblTotal").html("0");
				table2.clear().draw();
				$("#select_all_salereturn").prop("checked", false);
				$("#select_all_salereturn").attr("checked", false);
			}
			
			function fnDeleteData() {
				var index = table.cell({ focused: true }).index();
				table.keys.disable();
				DeleteData("./Transaction/SaleReturn/Delete.php", function(action) {
					if(action == "success") {
						$("#select_all").prop("checked", false);
						table.ajax.reload(function() {
							table.keys.enable();
							if(typeof index !== 'undefined') {
								try {
									table.cell(index).focus();
								}
								catch (err) {
									$("#grid-data").DataTable().cell( ':eq(0)' ).focus();
								}
							}
							if(table.page.info().page == table.page.info().pages) {
								setTimeout(function() {
									table.page("previous").draw('page');
								}, 0);
							}
						}, false);
					}
					else {
						table.keys.enable();
						return false;
					}
				});
			}

			function transactionList() {
				$("#transactionList-dialog").dialog({
					autoOpen: false,
					open: function() {
						table.keys.disable();
						table2.keys.disable();
						table3 = $("#grid-sale").DataTable({
									"destroy": true,
									"keys": true,
									"scrollY": "280px",
									"scrollX": false,
									"scrollCollapse": false,
									"paging": false,
									"searching": true,
									"order": [],
									"columns": [
										{ "width": "30%", "orderable": false, className: "dt-head-center" },
										{ "width": "30%", "orderable": false, className: "dt-head-center" },
										{ "width": "30%", "orderable": false, className: "dt-head-center" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" }
									],
									"ajax": "./Transaction/SaleReturn/TransactionList.php",
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
										$("#grid-sale").DataTable().cell( ':eq(0)' ).focus();
									},
									"drawCallback": function() {
										setTimeout(function() { table3.columns.adjust(); } , 0);
									}
								});
						var counterPickTransaction = 0;
						table3.on( 'key', function (e, datatable, key, cell, originalEvent) {
							if(key == 13 && $("#transactionList-dialog").css("display") == "block") {
								if(counterPickTransaction == 0) {
									counterPickTransaction = 1;
									var data = table3.row($(table3.cell({ focused: true }).node()).parent('tr')).data();
									$("#txtSaleNumber").val(data[0]);
									setTimeout(function() { 
										getSaleDetails();
									}, 0);
									$("#transactionList-dialog").dialog("destroy");
									table3.destroy();
									table.keys.enable();
									table2.keys.enable();
								}
								setTimeout(function() { counterPickTransaction = 0; } , 1000);
							}
						});
						
						$('#grid-sale tbody').on('dblclick', 'tr', function () {
							if( $("#transactionList-dialog").css("display") == "block") {
								var data = table3.row(this).data();
								$("#txtSaleNumber").val(data[0]);
								getSaleDetails();
								$("#transactionList-dialog").dialog("destroy");
								table3.destroy();
								table.keys.enable();
								table2.keys.enable();
							}
						});
						
						table3.on( 'search.dt', function () {
							setTimeout(function() { $("#grid-sale").DataTable().cell( ':eq(0)' ).focus(); }, 100 );
						});
						
						var counterKeyTransaction = 0;
						$(document).on("keydown", function (evt) {
							if(counterKeyTransaction == 0) {
								counterKeyTransaction = 1;
								if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0) {
									$("#transactionList-dialog").find("input[type='search']").focus();
								}
								else if(evt.keyCode == 27 && $("#transactionList-dialog").css("display") == "block") {
									$("#transactionList-dialog").dialog("destroy");
									table3.destroy();
									table2.keys.enable();
								}
							}
							setTimeout(function() { counterKeyTransaction = 0; } , 1000);
						});
					},
					
					close: function() {
						$(this).dialog("destroy");
						table3.destroy();
						//table.keys.enable();
						table2.keys.enable();
						$("#txtSaleNumber").focus();
					},
					resizable: false,
					height: 420,
					width: 1280,
					modal: true /*,
					buttons: [
					{
						text: "Tutup",
						tabindex: 15,
						id: "btnCancelPickTransaction",
						click: function() {
							$(this).dialog("destroy");
							table3.destroy();
							table.keys.enable();
							table2.keys.enable();
							return false;
						}
					}]*/
				}).dialog("open");
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
											table2.destroy();
											var counter = 0;
											Lobibox.alert("success",
											{
												msg: data.Message,
												width: 480,
												delay: 2000,
												beforeClose: function() {
													if(counter == 0) {
														table.keys.enable();
														counter = 1;
													}
												},
												shown: function() {
													setTimeout(function() {
														table.ajax.reload(function() {
															table.keys.enable();
															if(typeof index !== 'undefined') table.cell(index).focus();
															table.keys.disable();
														}, false);
													}, 0);
												}
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
							table.columns.adjust().draw();
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
				table = $("#grid-data").DataTable({
								"destroy": true,
								"keys": true,
								"scrollY": "330px",
								"rowId": "SaleReturnID",
								"scrollCollapse": true,
								"order": [],
								"columns": [
									{ "width": "20px", "orderable": false, className: "dt-head-center dt-body-center" },
									{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ "orderable": false, className: "dt-head-center dt-body-right" }
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Transaction/SaleReturn/DataSource.php",
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
								"drawCallback": function() {
									setTimeout(function() { table.columns.adjust(); } , 0);
								}
							});
				
				table.on( 'key', function (e, datatable, key, cell, originalEvent) {
					var index = table.cell({ focused: true }).index();
					if(key == 32) { //space
						var checkbox = $(".focus").find("input:checkbox");
						if(checkbox.prop("checked") == true) {
							checkbox.prop("checked", false);
							checkbox.attr("checked", false);
						}
						else {
							checkbox.prop("checked", true);
							checkbox.attr("checked", true);
						}
					}
					else if(counterSaleReturn == 0) {
						counterSaleReturn = 1;
						var data = datatable.row( cell.index().row ).data();
						if(key == 13) {
							if(($(".ui-dialog").css("display") == "none" || $("#delete-confirm").css("display") == "none") && $("#hdnEditFlag").val() == "1" ) {
								openDialog(data, 1);
							}
						}
						else if(key == 46 && $("#hdnDeleteFlag").val() == "1") {
							var DeleteID = new Array();
							$("input:checkbox[name=select]:checked").each(function() {
								if($(this).val() != 'all') DeleteID.push($(this).val());
							});
							if(DeleteID.length == 0) {
								table.keys.disable();
								var deletedData = new Array();
								deletedData.push(data[6] + "^" + data[2]);
								SingleDelete("./Transaction/SaleReturn/Delete.php", deletedData, function(action) {
									if(action == "success") {
										table.ajax.reload(function() {
											table.keys.enable();
											if(typeof index !== 'undefined') {
												try {
													table.cell(index).focus();
												}
												catch (err) {
													$("#grid-data").DataTable().cell( ':eq(0)' ).focus();
												}
											}
											if(table.page.info().page == table.page.info().pages) {
												setTimeout(function() {
													table.page("previous").draw('page');
												}, 0);
											}
										}, false);
									}
									else {
										table.keys.enable();
										return false;
									}
								});
							}
							else {
								fnDeleteData();
							}
						}
						setTimeout(function() { counterSaleReturn = 0; } , 1000);
					}
				});
				
				table.on('page', function() {
					$("#select_all").prop("checked", false);
				});
				
				var counterKey = 0;
				$(document).on("keydown", function (evt) {
					var index = table.cell({ focused: true }).index();
					if (evt.keyCode == 46 && $("#hdnDeleteFlag").val() == "1" && typeof index == 'undefined' && $("#FormData").css("display") == "none") { //delete button
						evt.preventDefault();
						if(counterKey == 0) {
							fnDeleteData();
							counterKey = 1;
						}
					}
					else if(evt.keyCode == 123 && $("#transactionList-dialog").css("display") == "none" && $("#FormData").css("display") == "block") {
						evt.preventDefault();
						if(counterKey == 0) {
							transactionList();
							counterKey = 1;
						}
					}
					else if(evt.keyCode == 123) {
						evt.preventDefault();
					}
					else if(evt.keyCode == 121 && $("#transactionList-dialog").css("display") == "none" && $("#FormData").css("display") == "block"  && $(".lobibox").css("display") != "block") {
						evt.preventDefault();
						if(counterKey == 0) {
							finish();
							counterKey = 1;
						}
					}
					else if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0 && $("#FormData").css("display") == "none" && $("#delete-confirm").css("display") == "none") {
						$("#grid-data_wrapper").find("input[type='search']").focus();
					}
					setTimeout(function() { counterKey = 0; } , 1000);
				});
				
				$('#grid-data tbody').on('dblclick', 'tr', function () {
					if($("#hdnEditFlag").val() == "1" ) {
						var data = table.row(this).data();
						openDialog(data, 1);
					}
				});
			});
		</script>
	</body>
</html>