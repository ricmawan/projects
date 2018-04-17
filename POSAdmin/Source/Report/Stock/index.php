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
						 <h5>Stok</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Kategori:
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<select id="ddlCategory" name="ddlCategory" tabindex=7 class="form-control-custom" placeholder="-- Semua Kategori --" >
										<option value=0 selected>-- Semua Kategori --</option>
										<?php
											$sql = "CALL spSelDDLCategory('".$_SESSION['UserLogin']."')";
											if (! $result = mysqli_query($dbh, $sql)) {
												logEvent(mysqli_error($dbh), '/Report/Stock/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
												return 0;
											}
											while($row = mysqli_fetch_array($result)) {
												echo "<option value='".$row['CategoryID']."' >".$row['CategoryCode']." - ".$row['CategoryName']."</option>";
											}
											mysqli_free_result($result);
											mysqli_next_result($dbh);
										?>
									</select>
								</div>
							</div>
							<div class="col-md-1 labelColumn">
								Cabang:
							</div>
							<div class="col-md-3">
								<select id="ddlBranch" name="ddlBranch" tabindex=8 class="form-control-custom" placeholder="Pilih Cabang" >
									<?php
										$sql = "CALL spSelDDLBranch('".$_SESSION['UserLogin']."')";
										if (! $result = mysqli_query($dbh, $sql)) {
											logEvent(mysqli_error($dbh), '/Report/Stock/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
										<th>Kode</th>
										<th>Nama</th>
										<th>Kategori</th>
										<th>Cabang</th>
										<th>Stok</th>
									</tr>
								</thead>
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
				var CategoryID = $("#ddlCategory").val();
				var BranchID = $("#ddlBranch").val();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(CategoryID == "") {
					$("#ddlCategory").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlCategory").next().find("input").focus();
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
					table.ajax.reload();
					table.columns.adjust();
					$("#loading").hide();
				}
			}
			function ExportExcel() {
				var CategoryID = $("#ddlCategory").val();
				var BranchID = $("#ddlBranch").val();
				var BranchName = $("#ddlBranch option:selected").text();
				var CategoryName = $("#ddlCategory option:selected").text();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(CategoryID == "") {
					$("#ddlCategory").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlCategory").next().find("input").focus();
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
					$("#excelDownload").attr("src", "Report/Stock/ExportExcel.php?BranchID=" + BranchID + "&BranchName=" + BranchName + "&CategoryID=" + CategoryID + "&CategoryName=" + CategoryName);
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
				$("#ddlCategory").combobox();
				$("#ddlCategory").next().find("input").click(function() {
					$(this).val("");
				});

				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "330px",
								"scrollCollapse": true,
								"order": [2, "asc"],
								"columns": [
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ "orderable": false, className: "dt-head-center dt-body-right" }						
								],
								"processing": true,
								"serverSide": true,
								"ajax": {
									"url": "./Report/Stock/DataSource.php",
									"data": function ( d ) {
										d.BranchID = $("#ddlBranch").val(),
										d.CategoryID = $("#ddlCategory").val(),
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
								}
							});
			});
		</script>
	</body>
</html>
