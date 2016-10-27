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
						 <h5>Perpanjang STNK</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<input id="hdnEditFlag" name="hdnEditFlag" type="hidden" <?php echo 'value='.$EditFlag; ?> />
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="LicenseExtensionID" data-visible="false" data-type="numeric" data-identifier="true">LicenseExtensionID</th>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="TransactionDate">Tanggal</th>
										<th data-column-id="MachineType">Tipe</th>
										<th data-column-id="MachineCode">Plat No</th>
										<!--<th data-column-id="MachineYear">Tahun</th>-->
										<th data-column-id="DueDate">Masa Berlaku</th>
										<th data-column-id="ExtensionDate">Tanggal Perpanjang</th>
										<th data-column-id="ExtensionCost" data-align="right">Biaya</th>
										<th data-column-id="Remarks">Keterangan</th>
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
									</tr>
								</thead>
							</table>
						</div>
						<button class="btn btn-primary menu" link="./Transaction/LicenseExtension/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
						<?php if($DeleteFlag == true) echo '<button class="btn btn-danger" onclick="DeleteData(\'./Transaction/LicenseExtension/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>'; ?>
					</div>
				</div>
			</div>
		</div>
		<div id="license-extension" title="Perpanjang STNK" style="display: none;">
			<form class="col-md-12" id="LicenseExtensionForm" method="POST" action="" >
				<input type="hidden" name="hdnLicenseExtensionID" id="hdnLicenseExtensionID" />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Tipe :
					</div>
					<div class="col-md-6">
						<span id="Tipe" ></span>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Plat No :
					</div>
					<div class="col-md-6">
						<span id="PlatNo" ></span>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Masa Berlaku :
					</div>
					<div class="col-md-6">
						<span id="MasaBerlaku" ></span>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Tanggal Perpanjang :
					</div>
					<div class="col-md-6">
						<input id="txtExtensionDate" name="txtExtensionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Biaya :
					</div>
					<div class="col-md-6">
						<input id="txtExtensionCost" name="txtExtensionCost" type="text" class="form-control-custom" placeholder="Biaya" style="text-align:right;" value="0.00" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" />
					</div>
				</div>
			</form>
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
							url: "./Transaction/LicenseExtension/DataSource.php",
							selection: true,
							multiSelect: true,
							rowSelect: true,
							keepSelection: true,
							formatters: {
								"commands": function(column, row)
								{
									var option;
									if($("#hdnEditFlag").val() == true) {
										option = "<i style='cursor:pointer;' data-row-id=\"" + row.LicenseExtensionID + "\" class=\"fa fa-edit\" data-link=\"./Transaction/LicenseExtension/Detail.php?ID=" + row.LicenseExtensionID + "\" acronym title=\"Ubah Data\"></i>&nbsp;&nbsp;&nbsp;";
										if(row.IsExtended == 0) option += "<i style='cursor:pointer;' data-row-id=\"" + row.LicenseExtensionID + "\" data-machine-type=\"" + row.MachineType + "\" data-machine-code=\"" + row.MachineCode + "\" data-due-date=\"" + row.DueDate + "\" class=\"fa fa-check\" acronym title=\"Sudah diperpanjang\"></i>";
									}
									else if(row.IsExtended == 0) {
										option = "<i style='cursor:pointer;' data-row-id=\"" + row.LicenseExtensionID + "\" data-machine-type=\"" + row.MachineType + "\" data-machine-code=\"" + row.MachineCode + "\" data-due-date=\"" + row.DueDate + "\" class=\"fa fa-check\" acronym title=\"Sudah diperpanjang\"></i>";
									}
									else option = "";
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

							grid.find(".fa-check").on("click", function(e)
							{
								ExtensionDone($(this).data("row-id"), $(this).data("machine-type"), $(this).data("machine-code"), $(this).data("due-date"));
							});
						});
			});

			function ExtensionDone(ID, MachineType, MachineCode, DueDate) {
				$("#hdnLicenseExtensionID").val(ID);
				$("#MasaBerlaku").html(DueDate);
				$("#PlatNo").html(MachineCode);
				$("#Tipe").html(MachineType);
				$("#txtExtensionDate").val("");
				$("#txtExtensionCost").val("0.00");
				$("#license-extension").dialog({
					autoOpen: false,
					show: {
						effect: "fade",
						duration: 500
					},
					hide: {
						effect: "fade",
						duration: 500
					},
					close: function() {
						$(this).dialog("destroy");
					},
					resizable: false,
					height: 300,
					width: 400,
					modal: true,
					buttons: {
						"Simpan": function() {
							var PassValidate = 1;
							var FirstFocus = 0;
							if($("#txtExtenstionDate").val() == '') {
								$("#txtExtenstionDate").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $("#txtExtenstionDate").focus();
								PassValidate = 0;
								FirstFocus = 1;
							}
							if($("#txtExtensionCost").val() == '') {
								$("#txtExtensionCost").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $("#txtExtensionCost").focus();
								PassValidate = 0;
								FirstFocus = 1;
							}
							
							if(PassValidate == 0) return false;
							$("#loading").show();
							
							$.ajax({
								url: "./Transaction/LicenseExtension/UpdateCost.php",
								type: "POST",
								data: $("#LicenseExtensionForm").serialize(),
								dataType: "json",
								success: function(data) {
									$("#loading").hide();
									if(data.FailedFlag == '0') {
										$.notify(data.Message, "success");
										$("#license-extension").dialog("destroy");
										Reload();
									}
									else {
										$("#loading").hide();
										$.notify(data.Message, "error");					
									}
									
								},
								error: function(data) {
									$.notify("Koneksi gagal, Cek koneksi internet!", "error");
									$("#loading").hide();
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
