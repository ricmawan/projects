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
					<div class="panel-heading">
						 <span style="width:50%;display:inline-block;">
							 <h5>Master Data Pelanggan</h5>
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
										<th>Kode Pelanggan</th>
										<th>Nama Pelanggan</th>
										<th>Telepon</th>
										<th>Alamat</th>
										<th>Kota</th>
										<th>Keterangan</th>
										<th>Pilihan Harga</th>
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
		<div id="FormData" title="Tambah Pelanggan" style="display: none;">
			<form class="col-md-12" id="PostForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-3 labelColumn">
						Kode Pelanggan :
						<input id="hdnCustomerID" name="hdnCustomerID" type="hidden" value=0 />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
					</div>
					<div class="col-md-7">
						<input id="txtCustomerCode" name="txtCustomerCode" type="text" tabindex=5 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Pelanggan" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Nama Pelanggan:
					</div>
					<div class="col-md-7">
						<input id="txtCustomerName" name="txtCustomerName" type="text" tabindex=6 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Nama Pelanggan" maxlength="30" required />
					</div>
				</div>				
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Telepon :
					</div>
					<div class="col-md-7">
						<input id="txtTelephone" name="txtTelephone" type="text" tabindex=7 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Telepon" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Alamat :
					</div>
					<div class="col-md-7">
						<input id="txtAddress" maxlength="30" name="txtAddress" type="text" tabindex=8 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Alamat" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Kota :
					</div>
					<div class="col-md-7">
						<input id="txtCity" name="txtCity" type="text" tabindex=9 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kota" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Pilihan Harga :
					</div>
					<div class="col-md-7">
						<select id="ddlCustomerPrice" name="ddlCustomerPrice" tabindex=10 class="form-control-custom" placeholder="Pilih Harga" >
							<?php
								$sql = "CALL spSelDDLCustomerPrice('".$_SESSION['UserLogin']."')";
								if (! $result = mysqli_query($dbh, $sql)) {
									logEvent(mysqli_error($dbh), '/Master/Customer/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
									return 0;
								}
								while($row = mysqli_fetch_array($result)) {
									echo "<option value='".$row['CustomerPriceID']."' >".$row['CustomerPriceName']."</option>";
								}
								mysqli_free_result($result);
								mysqli_next_result($dbh);
							?>
						</select>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Keterangan :
					</div>
					<div class="col-md-7">
						<textarea id="txtRemarks" name="txtRemarks" tabindex=11 class="form-control-custom" onkeydown="nextTabIndex(event, this);" onfocus="this.select();" autocomplete=off placeholder="Keterangan"></textarea>
					</div>
				</div>
				<br />
			</form>
		</div>
		<script>
			var table;
			function pasteIntoInput(el, text) {
				//alert();
				el.focus();
				if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
					var val = el.value;
					var selStart = el.selectionStart;
					el.value = val.slice(0, selStart) + text + val.slice(el.selectionEnd);
					el.selectionEnd = el.selectionStart = selStart + text.length;
				}
				else if (typeof document.selection != "undefined") {
					var textRange = document.selection.createRange();
					textRange.text = text;
					textRange.collapse(false);
					textRange.select();
				}
			}

			function nextTabIndex(evt, el) {
				//alert(evt.keyCode);
				if (evt.keyCode == 13 && evt.shiftKey) {
					//alert(evt.type);
					evt.preventDefault();
					if (evt.type == "keydown") {
						pasteIntoInput(el, "\n");
					}
			    }
				else if (evt.keyCode == 13  && !evt.shiftKey) {
					//alert();
					evt.preventDefault();
					//console.log(evt);
					var next = $('[tabindex="'+(el.tabIndex+1)+'"]');
					var nextTabIndex = el.tabIndex+1;
					if(next.length) {
						if(next.attr("disabled") == "disabled") {
							$(document).find(":focusable").each(function() {
								if(parseInt($(this)[0].tabIndex) > nextTabIndex) {
									$(this).focus();
									return false;
								}
							});
						}
						else {
							if(next.prop("type") == "select-one") next.simulate('mousedown');
							next.focus();
						}
					}
					else $('[tabindex="1"]').focus();
				}
			}
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#FormData").attr("title", "Edit Pelanggan");
					$("#hdnCustomerID").val(Data[9]);
					$("#txtCustomerCode").val(Data[2].toString());
					$("#txtCustomerName").val(Data[3].toString());
					$("#txtTelephone").val(Data[4].toString());
					$("#txtAddress").val(Data[5].toString());
					$("#txtCity").val(Data[6].toString());
					$("#txtRemarks").val(Data[7].toString());
					$("#ddlCustomerPrice").val(Data[10]);
				}
				else $("#FormData").attr("title", "Tambah Pelanggan");
				var index = table.cell({ focused: true }).index();
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
						table.keys.disable();
						$("#divModal").show();
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0 && $("textarea:focus").length == 0 && $("#btnOK:focus").length == 0) { //right arrow
								$("#btnCancelAddCustomer").focus();
							}
							else if(e.keyCode == 37 && $("input:focus").length == 0 && $("textarea:focus").length == 0 && $("#btnOK:focus").length == 0) { //left arrow
								$("#btnSaveCustomer").focus();
							}
						});
						setTimeout(function() {
							$("#txtCustomerCode").focus();
						}, 0);
					},
					
					close: function() {
						$(this).dialog("destroy");
						$("#divModal").hide();
						table.keys.enable();
						if(typeof index !== 'undefined') table.cell(index).focus();
						resetForm();
					},
					resizable: false,
					height: 420,
					width: 600,
					modal: false,
					buttons: [
					{
						text: "Simpan",
						id: "btnSaveCustomer",
						tabindex: 12,
						click: function() {
							saveConfirm(function(action) {
								if(action == "Ya") {
									$.ajax({
										url: "./Master/Customer/Insert.php",
										type: "POST",
										data: $("#PostForm").serialize(),
										dataType: "json",
										success: function(data) {
											if(data.FailedFlag == '0') {
												$("#loading").hide();
												$("#FormData").dialog("destroy");
												$("#divModal").hide();
												resetForm();
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
													delay: false,
													beforeClose: function() {
														if(counter == 0) {
															setTimeout(function() {
																$("#txtCustomerCode").focus();
															}, 0);
															counter = 1;
														}
													}
												});
												return 0;
											}
										},
										error: function(jqXHR, textStatus, errorThrown) {
											$("#loading").hide();
											var counter = 0;
											var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
											LogEvent(errorMessage, "/Master/Customer/index.php");
											Lobibox.alert("error",
											{
												msg: errorMessage,
												width: 480,
												beforeClose: function() {
													if(counter == 0) {
														setTimeout(function() {
															$("#txtCustomerCode").focus();
														}, 0);
														counter = 1;
													}
												}
											});
											return 0;
										}
									});
								}
								else {
									$("#txtCustomerCode").focus();
									return false;
								}
							});
						}
					},
					{
						text: "Batal",
						id: "btnCancelAddCustomer",
						tabindex: 13,
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
							table.keys.enable();
							if(typeof index !== 'undefined') table.cell(index).focus();
							resetForm();
							return false;
						}
					}]
				}).dialog("open");
			}
			
			function resetForm() {
				$("#hdnCustomerID").val(0);
				$("#txtCustomerCode").val("");
				$("#txtCustomerName").val("");
				$("#txtTelephone").val("");
				$("#txtAddress").val("");
				$("#txtCity").val("");
				$("#txtRemarks").val("");
				$("#ddlCustomerPrice").val("");
			}
			
			function fnDeleteData() {
				var index = table.cell({ focused: true }).index();
				table.keys.disable();
				DeleteData("./Master/Customer/Delete.php", function(action) {
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
					LogEvent(errorMessage, "/Master/Customer/index.php");
					Lobibox.alert("error",
					{
						msg: "Terjadi kesalahan. Memuat ulang halaman.",
						width: 480,
						delay: 2000,
						beforeClose: function() {
							if(counterError == 0) {
								location.reload();
								counterError = 1;
							}
						}
					});
				};
				
				keyFunction();
				enterLikeTab();
				var counterCustomer = 0;
				table = $("#grid-data").DataTable({
							"destroy": true,
							"keys": true,
							"scrollY": "330px",
							"rowId": "CustomerID",
							"scrollCollapse": true,
							"order": [],
							"columns": [
								{ "width": "20px", "orderable": false, className: "dt-head-center dt-body-center" },
								{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
								{ className: "dt-head-center" },
								{ className: "dt-head-center" },
								{ className: "dt-head-center" },
								{ className: "dt-head-center" },
								{ className: "dt-head-center" },
								{ className: "dt-head-center" },
								{ className: "dt-head-center" }
							],
							"processing": true,
							"serverSide": true,
							"ajax": "./Master/Customer/DataSource.php",
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
								setTimeout(function() {
									table.columns.adjust();
								}, 100);
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
					else if(counterCustomer == 0) {
						counterCustomer = 1;
						var data = datatable.row( cell.index().row ).data();
						if(key == 13) { //enter
							if(($(".ui-dialog").css("display") == "none" || $("#delete-confirm").css("display") == "none") && $("#hdnEditFlag").val() == "1") {
								openDialog(data, 1);
							}
						}
						else if(key == 46 && $("#hdnDeleteFlag").val() == "1") { //delete
							var DeleteID = new Array();
							$("input:checkbox[name=select]:checked").each(function() {
								if($(this).val() != 'all') DeleteID.push($(this).val());
							});
							if(DeleteID.length == 0) {
								table.keys.disable();
								var deletedData = new Array();
								deletedData.push(data[9] + "^" + data[3]);
								SingleDelete("./Master/Customer/Delete.php", deletedData, function(action) {
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
						setTimeout(function() { counterCustomer = 0; } , 1000);
					}
				});
				
				table.on('page', function() {
					$("#select_all").prop("checked", false);
				});
				
				var counterKeyItem = 0;
				$(document).on("keydown", function (evt) {
					if(counterKeyItem == 0) {
						counterKeyItem = 1;
						var index = table.cell({ focused: true }).index();
						if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0 && $("#FormData").css("display") == "none" && $("#delete-confirm").css("display") == "none") {
							$("#grid-data_wrapper").find("input[type='search']").focus();
						}
						else if (evt.keyCode == 46 && $("#hdnDeleteFlag").val() == "1" && typeof index == 'undefined' && $("#FormData").css("display") == "none") { //delete button
							evt.preventDefault();
							fnDeleteData();
						}
					}
					setTimeout(function() { counterKeyItem = 0; } , 1000);
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
