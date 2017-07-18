<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			th[data-column-id="Opsi"] {
				width: 125px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Barang Keluar</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<input id="hdnEditFlag" name="hdnEditFlag" type="hidden" <?php echo 'value='.$EditFlag; ?> />
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="OutgoingIDNo" data-type="numeric" data-visible="false" data-identifier="true">ID Transaksi</th>
										<th data-column-id="OutgoingNumber">No Nota</th>
										<th data-column-id="TransactionDate" data-type="numeric">Tanggal</th>
										<th data-column-id="CustomerName">Nama Pelanggan</th>
										<th data-column-id="DeliveryCost" data-align="right">Ongkos Kirim</th>
										<th data-column-id="Total" data-align="right">Total</th>
										<th data-column-id="Remarks">Catatan</th>
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
									</tr>
								</thead>
							</table>
						</div>
						<button class="btn btn-primary menu" link="./Transaction/Outgoing/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
						<?php if($DeleteFlag == true) echo '<button class="btn btn-danger" onclick="DeleteData(\'./Transaction/Outgoing/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>'; ?>
					</div>
				</div>
			</div>
			<div id="dialog-delivery" title="Update Ongkos Kirim" style="display: none;" >
				<div class="col-md-12">
					<input type="hidden" id="hdnOutgoingID" name="hdnOutgoingID" value=0 autofocus="autofocus" />
					<div class="row" >
						<div class="col-md-4 labelColumn" >
							No Nota:
						</div>
						<div class="col-md-8">
							<span id="lblOutgoingNumber" style="font-weight: bold; font-size: 15px; color: red;"></span>
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-4 labelColumn" >
							Pelanggan:
						</div>
						<div class="col-md-8">
							<span id="lblCustomerName" style="font-weight: bold; font-size: 15px; color: red;"></span>
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-4 labelColumn" >
							Ongkos Kirim:
						</div>
						<div class="col-md-8">
							<input type="text" id="txtDeliveryCost" style="text-align:right;"  onclick="this.select();"  name="txtDeliveryCost" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
						</div>
					</div>
					<br />
				</div>
			</div>
			<div id="dialog-payment" title="Pembayaran" style="display: none;" >
				<div class="col-md-12">
					<input type="hidden" id="hdnOutgoingID2" name="hdnOutgoingID2" value=0 autofocus="autofocus" />
					<div class="row" >
						<div class="col-md-4 labelColumn" >
							No Nota:
						</div>
						<div class="col-md-8">
							<span id="lblOutgoingNumber2" style="font-weight: bold; font-size: 15px; color: red;"></span>
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-4 labelColumn" >
							Pelanggan:
						</div>
						<div class="col-md-8">
							<span id="lblCustomerName2" style="font-weight: bold; font-size: 15px; color: red;"></span>
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-4 labelColumn" >
							Jumlah Pembayaran:
						</div>
						<div class="col-md-8">
							<input type="text" id="txtAmount" name="txtAmount" style="text-align:right;" onclick="this.select();" class="form-control-custom" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
						</div>
					</div>
					<br />
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				var grid = $("#grid-data").bootgrid({
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
								infos: "Menampilkan {{ctx.start}} sampai {{ctx.end}} dari {{ctx.total}} data",
								loading: "Loading...",
								noResults: "Tidak Ada Data Yang Ditemukan!",
								refresh: "Refresh",
								search: "Cari"
							},
							url: "./Transaction/Outgoing/DataSource.php",
							selection: true,
							multiSelect: true,
							rowSelect: true,
							keepSelection: true,
							formatters: {
								"commands": function(column, row)
								{
									var option;
									if($("#hdnEditFlag").val() == true) {
										option = "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" class=\"fa fa-edit\" data-link=\"./Transaction/Outgoing/Detail.php?ID=" + row.OutgoingID + "\" acronym title=\"Ubah Data\"></i>&nbsp;&nbsp;&nbsp;";
										option += "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" class=\"fa fa-print\" acronym title=\"Cetak Nota\"></i>&nbsp;&nbsp;&nbsp;";
										option += "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" class=\"fa fa-truck\" acronym title=\"Cetak Surat Jalan\"></i>&nbsp;&nbsp;&nbsp;";
										option += "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" data-outgoing-number=\"" + row.OutgoingNumber + "\" data-customer-name=\"" + row.CustomerName + "\" data-delivery-cost=\"" + row.DeliveryCost + "\" class=\"fa fa-ship\" acronym title=\"Ongkos Kirim\"></i>&nbsp;&nbsp;&nbsp;";
										option += "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" data-outgoing-number=\"" + row.OutgoingNumber + "\" data-customer-name=\"" + row.CustomerName + "\" data-delivery-cost=\"" + row.DeliveryCost + "\" class=\"fa fa-usd\" acronym title=\"Pembayaran\"></i>";
									}
									else {
										option = "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" class=\"fa fa-print\" acronym title=\"Cetak Nota\"></i>&nbsp;&nbsp;&nbsp;";
										option += "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" class=\"fa fa-truck\" acronym title=\"Cetak Surat Jalan\"></i>&nbsp;&nbsp;&nbsp;";
										option += "<i style='cursor:pointer;' data-row-id=\"" + row.OutgoingID + "\" data-outgoing-number=\"" + row.OutgoingNumber + "\" data-customer-name=\"" + row.CustomerName + "\" data-delivery-cost=\"" + row.DeliveryCost + "\" class=\"fa fa-ship\" acronym title=\"Ongkos Kirim\"></i>";
									}
									return option;
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							grid.find(".fa-edit").on("click", function(e)
							{
								Redirect($(this).data("link"));
							});
							
							grid.find(".fa-print").on("click", function(e)
							{
								PrintInvoice($(this).data("row-id"));
							});
							
							grid.find(".fa-truck").on("click", function(e)
							{
								PrintShipment($(this).data("row-id"));
							});
							
							grid.find(".fa-ship").on("click", function(e)
							{
								DeliveryCost($(this).data("row-id"), $(this).data("outgoing-number"), $(this).data("customer-name"), $(this).data("delivery-cost"));
							});
							
							grid.find(".fa-usd").on("click", function(e)
							{
								Payment($(this).data("row-id"), $(this).data("outgoing-number"), $(this).data("customer-name"));
							});
						});
			});
			
			function PrintInvoice(ID) {
				$("#loading").show();
				$.ajax({
					url: "./Transaction/Outgoing/PrintInvoice.php",
					type: "POST",
					data: { hdnOutgoingID : ID },
					dataType: "json",
					success: function(data) {
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						$("#loading").hide();
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Koneksi gagal", "error");
				
					}
				});
			}
			
			function PrintShipment(ID) {
				$("#loading").show();
				$.ajax({
					url: "./Transaction/Outgoing/PrintShipment.php",
					type: "POST",
					data: { hdnOutgoingID : ID },
					dataType: "json",
					success: function(data) {
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						$("#loading").hide();
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Koneksi gagal", "error");
					}
				});
			}
			
			function DeliveryCost(ID, OutgoingNumber, CustomerName, DeliveryCost) {
				$("#lblOutgoingNumber").html("");
				$("#lblCustomerName").html("");
				$("#lblOutgoingNumber").html(OutgoingNumber);
				$("#lblCustomerName").html(CustomerName);
				$("#txtDeliveryCost").val(returnRupiah(DeliveryCost));
				$("#hdnOutgoingID").val(ID);
				$("#dialog-delivery").dialog({
					autoOpen: false,
					show: {
						effect: "fade",
						duration: 500
					},
					hide: {
						effect: "fade",
						duration: 500
					},
					resizable: false,
					height: "auto",
					width: 400,
					close: function() {
						$(this).dialog("destroy");
					},
					modal: true,
					buttons: {
						"Simpan": function() {
							$("#loading").show();
							$.ajax({
								url: "./Transaction/Outgoing/UpdateDeliveryCost.php",
								type: "POST",
								data: { OutgoingID : $("#hdnOutgoingID").val(), txtDeliveryCost : $("#txtDeliveryCost").val() },
								dataType: "json",
								success: function(data) {
									$("#loading").hide();
									if(data.FailedFlag == '0') {
										$.notify(data.Message, "success");
										$("#dialog-delivery").dialog("destroy");
										$("#grid-data").bootgrid("reload");
									}
									else {
										$.notify(data.Message, "error");					
									}
								},
								error: function(data) {
									$("#loading").hide();
									$.notify("Terjadi kesalahan sistem!", "error");
								}
							});
						},
						"Batal": function() {
							$(this).dialog("destroy");
							return false;
						}
					}
				}).dialog("open");
			}
			
			function Payment(ID, OutgoingNumber, CustomerName) {
				$("#lblOutgoingNumber2").html("");
				$("#lblCustomerName2").html("");
				$("#lblOutgoingNumber2").html(OutgoingNumber);
				$("#lblCustomerName2").html(CustomerName);
				$("#hdnOutgoingID2").val(ID);
				$("#loading").show();
				$.ajax({
					url: "./Transaction/Outgoing/Payment.php",
					type: "POST",
					data: { OutgoingID : $("#hdnOutgoingID").val(), txtDeliveryCost : $("#txtDeliveryCost").val() },
					dataType: "json",
					success: function(data) {
						$("#loading").hide();
						if(data.FailedFlag == '0') {
							$.notify(data.Message, "success");
							$("#dialog-delivery").dialog("destroy");
							$("#grid-data").bootgrid("reload");
						}
						else {
							$.notify(data.Message, "error");					
						}
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
				$("#dialog-payment").dialog({
					autoOpen: false,
					show: {
						effect: "fade",
						duration: 500
					},
					hide: {
						effect: "fade",
						duration: 500
					},
					resizable: false,
					height: "auto",
					width: 400,
					close: function() {
						$(this).dialog("destroy");
					},
					modal: true,
					buttons: {
						"Simpan": function() {
							$("#loading").show();
							$.ajax({
								url: "./Transaction/Outgoing/Payment.php",
								type: "POST",
								data: { OutgoingID : $("#hdnOutgoingID").val(), txtDeliveryCost : $("#txtDeliveryCost").val() },
								dataType: "json",
								success: function(data) {
									$("#loading").hide();
									if(data.FailedFlag == '0') {
										$.notify(data.Message, "success");
										$("#dialog-delivery").dialog("destroy");
										$("#grid-data").bootgrid("reload");
									}
									else {
										$.notify(data.Message, "error");					
									}
								},
								error: function(data) {
									$("#loading").hide();
									$.notify("Terjadi kesalahan sistem!", "error");
								}
							});
						},
						"Batal": function() {
							$(this).dialog("destroy");
							return false;
						}
					}
				}).dialog("open");
			}
		</script>
	</body>
</html>
