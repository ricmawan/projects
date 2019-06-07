<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Email</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Tanggal :
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtFromDate" name="txtFromDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Dari Tanggal" />
								</div>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-info" id="btnView" onclick="Preview();" ><i class="fa fa-list"></i> Lihat</button>&nbsp;&nbsp;
							</div>
						</div>
						<br />
						<div class="table-responsive" id="dvTable" style="display: none;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric" >No</th>
										<th data-column-id="ScheduledDate" >Tanggal</th>
										<th data-column-id="PatientName" >Nama Pasien</th>
										<th data-column-id="Email" >Email</th>
										<th data-column-id="EmailStatus">Keterangan</th>
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false" >Opsi</th>
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
				var txtFromDate = $("#txtFromDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(txtFromDate == "" ) {
					$("#txtFromDate").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#txtFromDate").focus();
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
							//infos: "Menampilkan {{ctx.start}} sampai {{ctx.end}} dari {{ctx.total}} data",
							infos: "Menampilkan {{ctx.total}} data",
							loading: "Loading...",
							noResults: "Tidak Ada Data Yang Ditemukan!",
							refresh: "Refresh",
							search: "Cari"
						},
						url: "Report/Email/DataSource.php?txtFromDate=" + txtFromDate,
						selection: true,
						multiSelect: true,
						rowSelect: true,
						keepSelection: true,
						formatters: {
							"commands": function(column, row)
							{
								return "<i style='cursor:pointer;' data-row-id=\"" + row.CheckScheduleID + "\" class=\"fa fa-envelope\" acronym title=\"Kirim Email\"></i>";
							}
						}
					}).on("loaded.rs.jquery.bootgrid", function()
					{
						/* Executes after data is loaded and rendered */
						grid.find(".fa-envelope").on("click", function(e)
						{
							var CheckScheduleID = $(this).data("row-id");
							$("#loading").show();
							$.ajax({
								url: "./Report/Email/Resend.php",
								type: "POST",
								data: { CheckScheduleID : CheckScheduleID },
								dataType: "json",
								success: function(data) {
									$("#loading").hide();
									if(data.FailedFlag == '0') {
										$.notify(data.Message, "success");
										$('#grid-data').bootgrid('reload');
									}
									else {
										$.notify(data.Message, "error");
										$('#grid-data').bootgrid('reload');				
									}
								},
								error: function(data) {
									$("#loading").hide();
									$.notify("Terjadi kesalahan sistem!", "error");
								}
							});
						});
					});
					$("#dvTable").show();
					$("#loading").hide();
				}
			}
		</script>
	</body>
</html>