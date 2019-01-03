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
						<h5>Upload Barang</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Kategori:
							</div>
							<div class="col-md-3">
								<div class="ui-widget" style="width: 100%;">
									<select id="ddlCategory" name="ddlCategory" tabindex=7 class="form-control-custom" placeholder="-- Pilih Kategori --" >
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
							<button id="btnDownload" name="btnDownload" onclick="ExportExcel();" class="btn btn-primary" style="padding:0 10px;" >Download Data</button>
						</div>
						<br />
						<div class="row">
							<form action="./Master/ItemUpload/upload.php" method="post" id="formUpload" name="formUpload" enctype="multipart/form-data" target="upload_target" >
								<div class="col-md-1 labelColumn">
									File:
								</div>
								<div class="col-md-3">
							    	<input id="myfile" name="myfile" type="file" accept=".xls,.xlsx" />
							    </div>
								<input type="button" name="submitBtn" onclick="startUpload(['xls', 'xlsx']);" value="Upload" class="btn btn-primary" style="padding:0 10px;" />
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="itemList-dialog" title="Daftar Barang" style="display: none;">
			<div class="row col-md-12" >
				<div id="divTableItem" class="table-responsive" style="overflow-x:hidden;">
					<table id="grid-item" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
						<thead>
							<tr>
								<th>ID</th>
								<th>Kode</th>
								<th>Nama</th>
								<th>Kategori</th>
								<th>Harga Beli</th>
								<th>Harga Ecer</th>
								<th>Harga 1</th>
								<th>Qty 1</th>
								<th>Harga 2</th>
								<th>Qty 2</th>
								<th>Berat</th>
								<th>Stok Min</th>
								<th>Keterangan</th>
							</tr>
						</thead>
						<tbody id="tblUpload" name="tblUpload" >
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<iframe id="upload_target" name="upload_target" src="#" style="display:none;"></iframe>
		<script>
			var table;
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

			function startUpload(fileTypes){
				var fileName = $("#myfile").val();
				if (!fileName) return;

				dots = fileName.split(".")
				//get the part AFTER the LAST period.
				fileType = "." + dots[dots.length-1];

				if(fileTypes.join(".").indexOf(fileType) != -1) {
					$("#loading").show();
					$("#formUpload").submit();
				}
				else {
					Lobibox.alert("error",
					{
						msg: "Mohon upload file dengan tipe: \n\n" + (fileTypes.join(" .")),
						width: 560
					});
					return false;
				}
			}

			function stopUpload(success, message, contents){
				$("#loading").hide();
				if (success == 1){
					//success
					$("#tblUpload").html(contents);
					$("#myfile").val("");
					openPopUp();
				}
				else {
					Lobibox.alert("error",
					{
						msg: message,
						width: 480
					});
				}
				return true;   
			}

			function openPopUp() {
				$("#itemList-dialog").dialog({
					autoOpen: false,
					open: function() {
						table = $("#grid-item").DataTable({
									"destroy": true,
									"keys": false,
									"scrollY": "280px",
									"scrollX": true,
									"scrollCollapse": false,
									"paging": false,
									"searching": false,
									"order": [],
									"columns": [
										{ "width": "3%", "orderable": false, className: "dt-head-center" },
										{ "width": "7%", "orderable": false, className: "dt-head-center" },
										{ "width": "15%", "orderable": false, className: "dt-head-center" },
										{ "width": "10%", "orderable": false, className: "dt-head-center" },
										{ "width": "8%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "8%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "8%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "7%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "3%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "7%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "14%", "orderable": false, className: "dt-head-center" }
									],
									"processing": true,
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
											adjustColumns();
										}, 0);
									}
								});
					},
					
					close: function() {
						$(this).dialog("destroy");
						table.destroy();
					},
					resizable: false,
					height: 500,
					width: 1280,
					modal: true,
					buttons: [
					{
						text: "Tutup",
						tabindex: 13,
						id: "btnCancelPickItem",
						click: function() {
							$(this).dialog("destroy");
							table.destroy();
							return false;
						}
					}]
				}).dialog("open");
			}

			function adjustColumns() {
				//kolom ga cukup dan harus scroll horizontal
				if($(".dataTables_scrollHeadInner").find("table").width() > $(".dataTables_scrollHead").width()) {
					var headerWidth = $(".dataTables_scrollHead").find("table").width() + 2;
					$(".dataTables_scrollBody").find("table").css({
						"width" : headerWidth + "px"
					});

					var tableWidth = $(".dataTables_scrollBody").find("table").width();
					var barWidth = table.settings()[0].oScroll.iBarWidth;
					var newWidth = tableWidth + barWidth + 2;
					setTimeout(function() {
						$(".dataTables_scrollHeadInner").css({
							"width" : newWidth
						});
					}, 0);
				}
				//kalo full 1 layar ga ada scroll horizontal tp ada scroll vertical
				else if($(".dataTables_scrollBody").find("table").height() > 330 && $("#wrapper").width() <= 1280) {
					var headerDiv = $(".dataTables_scrollHead").width();
					var headerWidth = $(".dataTables_scrollHead").find("table").width() + 2;

					$(".dataTables_scrollBody").find("table").css({
						"width" : headerWidth + "px"
					});

					var tableWidth = $(".dataTables_scrollBody").find("table").width();
					var barWidth = table.settings()[0].oScroll.iBarWidth;
					var newWidth = tableWidth - barWidth + 2;
					setTimeout(function() {
						$(".dataTables_scrollHeadInner").css({
							"width" : (headerDiv - barWidth)
						});

						$(".dataTables_scrollHeadInner").find("table").css({
							"width" : (headerDiv - barWidth)
						});

						$(".dataTables_scrollBody").find("table").css({
							"width" : (headerDiv - barWidth)
						});
					}, 0);
				}
				else {
					var headerDiv = $(".dataTables_scrollHead").width();
					var headerWidth = $(".dataTables_scrollHead").find("table").width() + 2;

					$(".dataTables_scrollBody").find("table").css({
						"width" : headerWidth + "px"
					});

					var tableWidth = $(".dataTables_scrollBody").find("table").width();
					var barWidth = table.settings()[0].oScroll.iBarWidth;
					var newWidth = tableWidth + barWidth + 2;
					setTimeout(function() {
						$(".dataTables_scrollHeadInner").css({
							"width" : headerDiv
						});

						$(".dataTables_scrollHeadInner").find("table").css({
							"width" : headerDiv
						});

						$(".dataTables_scrollBody").find("table").css({
							"width" : headerDiv
						});

						$(".dataTables_scrollHeadInner").css({
							"width" : newWidth
						});
					}, 0);
				}
			}

			function ExportExcel() {
				var CategoryID = $("#ddlCategory").val();
				var CategoryName = $("#ddlCategory option:selected").text();
				var PassValidate = 1;
				var FirstFocus = 0;
				if(CategoryID == null) {
					$("#ddlCategory").next().find("input").notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
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
					$("#excelDownload").attr("src", "Master/ItemUpload/ExportExcel.php?CategoryID=" + CategoryID + "&CategoryName=" + CategoryName);
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
				$("#ddlCategory").combobox();
				$("#ddlCategory").next().find("input").click(function() {
					$(this).val("");
				});
			});
		</script>
	</body>
</html>
