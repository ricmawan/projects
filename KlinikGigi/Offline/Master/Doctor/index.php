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
						 <h5>Master Data Dokter</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="UserIDName" data-visible="false" data-type="numeric" data-identifier="true">UserID</th>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="UserName">Nama</th>
										<th data-column-id="UserLogin">Username</th>
										<th data-column-id="Commission">Komisi</th>
										<th data-column-id="ToolsFee">Biaya Alat</th>
										<!--<th data-column-id="UserTypeName">Jabatan</th>-->
										<th data-column-id="Status">Status</th>
										<?php if($EditFlag == true) echo '<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>'; ?>
									</tr>
								</thead>
							</table>
						</div>
						<button class="btn btn-primary menu" link="./Master/Doctor/Detail.php?ID=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
						<?php if($DeleteFlag == true) echo '<button class="btn btn-danger" onclick="DeleteData(\'./Master/Doctor/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>'; ?>
					</div>
				</div>
			</div>
			<div id="dialog-fee" title="Komisi dan biaya alat" style="display: none;">
				<form class="col-md-12" id="PostForm" method="POST" action="" >
					<input type="hidden" name="hdnDoctorID" id="hdnDoctorID" value=0 />
					<div class="row">
						<div class="col-md-2 labelColumn">
							Dokter :
						</div>
						<div class="col-md-8">
							<span id="doctorName" style="font-weight: bold; font-size: 15px; color: red;"></span>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-md-2 labelColumn">
							Tahun :
						</div>
						<div class="col-md-2">
							<select id="ddlYear" name="ddlYear" class="form-control-custom" style="width:auto;font-size:12px;">
								<?php
									$EndYear = (int)date("Y");
									for($StartYear = 2016;$StartYear <= $EndYear;$StartYear++) {
										echo "<option value=$StartYear>$StartYear</option>";
									}
								?>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>No</th>
										<th>Bulan</th>
										<th>Biaya Alat</th>
										<th>Presentase Komisi</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>1</td>
										<td>Januari</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee1" name="fee1" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision1" name="commision1" /></td>
									</tr>
									<tr>
										<td>2</td>
										<td>Februari</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee2" name="fee2" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision2" name="commision2" /></td>
									</tr>
									<tr>
										<td>3</td>
										<td>Maret</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee3" name="fee3" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision3" name="commision3" /></td>
									</tr>
									<tr>
										<td>4</td>
										<td>April</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee4" name="fee4" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision4" name="commision4" /></td>
									</tr>
									<tr>
										<td>5</td>
										<td>Mei</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee5" name="fee5" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)"  id="commision5" name="commision5" /></td>
									</tr>
									<tr>
										<td>6</td>
										<td>Juni</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee6" name="fee6" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision6" name="commision6" /></td>
									</tr>
									<tr>
										<td>7</td>
										<td>Juli</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee7" name="fee7" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision7" name="commision7" /></td>
									</tr>
									<tr>
										<td>8</td>
										<td>Agustus</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee8" name="fee8" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision8" name="commision8" /></td>
									</tr>
									<tr>
										<td>9</td>
										<td>September</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee9" name="fee9" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision9" name="commision9" /></td>
									</tr>
									<tr>
										<td>10</td>
										<td>Oktober</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee10" name="fee10" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision10" name="commision10" /></td>
									</tr>
									<tr>
										<td>11</td>
										<td>November</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee11" name="fee11" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision11" name="commision11" /></td>
									</tr>
									<tr>
										<td>12</td>
										<td>Desember</td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" id="fee12" name="fee12" /></td>
										<td><input type="text" class="form-control-custom" style="text-align:right;" onchange="this.value = minmax(this.value, 0, 100)" onkeypress="return isNumberKey(event, this.id, this.value)" id="commision12" name="commision12" /></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</form>
			</div>
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
		</div>
		<script>
			function LoadDetails() {
				$("#loading").show();
				$(".form-control-custom:text").val("");
				var ddlYear = $("#ddlYear").val();
				var DoctorID = $("#hdnDoctorID").val();
				$.ajax({
					url: "./Master/Doctor/FeeDetails.php",
					type: "POST",
					data: { DoctorID : DoctorID, Year : ddlYear },
					dataType: "json",
					success: function(data) {
						$("#loading").hide();
						for(var i=0;i<data.length;i++) {
							$("#commision" + data[i].BusinessMonth).val(data[i].CommisionPercentage);
							$("#fee" + data[i].BusinessMonth).val(returnRupiah(data[i].ToolsFee));
						}
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
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
							url: "./Master/Doctor/DataSource.php",
							selection: true,
							multiSelect: true,
							rowSelect: true,
							keepSelection: true,
							formatters: {
								"commands": function(column, row)
								{
									return "<i style='cursor:pointer;' data-row-id=\"" + row.UserID + "\" class=\"fa fa-edit\" data-link=\"./Master/Doctor/Detail.php?ID=" + row.UserID + "\" acronym title=\"Ubah Data\"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i style='cursor:pointer;' data-username=\"" + row.UserName + "\" data-row-id=\"" + row.UserID + "\" class='fa fa-money' style='cursor:pointer;' acronym title='Komisi dan biaya alat' ></i>";
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							grid.find(".fa-edit").on("click", function(e)
							{
								Redirect($(this).data("link"));
							});
							
							grid.find(".fa-money").on("click", function(e)
							{
								$("#hdnDoctorID").val($(this).data("row-id"));
								$("#doctorName").html($(this).data("username"));
								LoadDetails();
								$("#dialog-fee").dialog({
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
									width: 600,
									modal: true,
									close: function() {
										$(this).dialog("destroy");
									},
									buttons: {
										"Simpan": function() {
											//$(this).dialog("close");
											$("#dialog-confirm").dialog({
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
												modal: true,
												close: function() {
													$(this).dialog("destroy");
												},
												buttons: {
													"Ya": function() {
														$(this).dialog("destroy");
														$("#loading").show();
														$.ajax({
															url: "./Master/Doctor/SaveFee.php",
															type: "POST",
															data: $("#PostForm").serialize(),
															dataType: "json",
															success: function(data) {
																$("#loading").hide();
																if(data.FailedFlag == '0') {
																	$.notify(data.Message, "success");
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
													"Tidak": function() {
														$(this).dialog("destroy");
														return false;
													}
												}
											}).dialog("open");
										},
										"Tutup": function() {
											$(this).dialog("destroy");
										}
									}
								}).dialog("open");
							});
						});
			});
		</script>
	</body>
</html>
