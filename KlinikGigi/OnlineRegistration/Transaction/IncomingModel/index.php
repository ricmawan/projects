<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../DBConfig.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<style>
			th[data-column-id="TransactionDate"] {
			    width: 85px !important;
			}

			th[data-column-id="ReceivedDate"] {
			    width: 140px !important;
			}

			th[data-column-id="IncomingReceiptNumber"] {
			    width: 140px !important;
			}

			.ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
				float: left;
			}

			#ui-datepicker-div {
				z-index: 10 !important;
			}

			.form-control-custom {
				height: 17px !important;
			}
		</style>
		<link href="../../assets/css/bootstrap.css" rel="stylesheet" />
		<link href="../../assets/css/font-awesome.css" rel="stylesheet" />
		<link href="../../assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
		<link href="../../assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
		<link href="../../assets/css/custom.css" rel="stylesheet" />
		<link href="../../assets/css/jquery.bootgrid.css" rel="stylesheet" />
	</head>
	<body style="overflow: hidden !important;" >
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Terima Model</h5>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<?php
								echo '<input id="hdnUserTypeID" name="hdnUserTypeID" type="hidden" value="'.$_SESSION['UserTypeID'].'" />';
							?>
							<form class="col-md-12" id="PostForm" method="POST" action="" >
								<table id="grid-data" class="table table-striped table-bordered table-hover" >
									<thead>				
										<tr>
											<th data-column-id="OutgoingModelDetailsID" data-visible="false" data-type="numeric" data-identifier="true">OutgoingModelDetailsID</th>
											<th data-column-id="TransactionDate">Tgl Kirim</th>
											<th data-column-id="ReceiptNumber" >No Resi Kirim</th>
											<th data-column-id="DoctorName" >Dokter</th>
											<th data-column-id="PatientName" >Pasien</th>
											<th data-column-id="ExaminationName" >Tindakan</th>
											<th data-column-id="Remarks" data-visible="false" >Keterangan</th>
											<th data-column-id="ReceivedDate" >Tanggal Terima</th>
											<th data-column-id="IncomingReceiptNumber" data-type="numeric">No Resi Terima</th>
										</tr>
									</thead>
								</table>
							</form>
						</div>
						<!--<button class="btn btn-primary" onclick="LoadOutgoingModelDetails(0);"><i class="fa fa-plus "></i> Simpan</button>&nbsp;-->
						<button class="btn btn-primary" onclick="SaveData();" ><i class="fa fa-save"></i> Simpan</button>
					</div>
				</div>
			</div>
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
		</div>
		<script src="../../assets/js/jquery-1.10.2.js"></script>
		<script src="../../assets/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="../../assets/js/bootstrap.min.js"></script>
		<script src="../../assets/js/jquery.metisMenu.js"></script>
		<script src="../../assets/js/custom.js"></script>
		<script src="../../assets/js/notify.js"></script>
		<script src="../../assets/js/global.js"></script>
		<script src="../../assets/js/jquery.bootgrid.js"></script>
		<script type="text/javascript" src="../../assets/js/jquery.fancybox.js"></script>
		<script>
			function SaveData() {
				var OutgoingModelDetailsID = new Array();
				var ReceivedDate = new Array();
				var IncomingReceiptNumber = new Array();
				var FormattedDate;
				var transactionDate;

				var passValidation = 1;
				$("input:checkbox[name=select]:checked").each(function() {
					if($(this).val() != 'all') {
						if($("#txtIncomingReceiptNumber" + $(this).val()).val() == "") {
							$("#txtIncomingReceiptNumber" + $(this).val()).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							passValidation = 0;
						}
					}

				});

				if($("input:checkbox[name=select]:checked").length == 0) {
					$.notify("Silahkan pilih data yang ingin disimpan!", "error");
					passValidation = 0;
				}

				if(passValidation == 1) {
					$("input:checkbox[name=select]:checked").each(function() {
						transactionDate = $("#txtReceivedDate" + $(this).val()).datepicker('getDate');
						FormattedDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);

						OutgoingModelDetailsID.push($(this).val());
						ReceivedDate.push(FormattedDate);
						IncomingReceiptNumber.push($("#txtIncomingReceiptNumber" + $(this).val()).val());
					});

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
						buttons: {
							"Ya": function() {
								$(this).dialog("close");
								$("#loading").show();
								$.ajax({
									url: "./Insert.php",
									type: "POST",
									data: { OutgoingModelDetailsID : OutgoingModelDetailsID, ReceivedDate : ReceivedDate, IncomingReceiptNumber : IncomingReceiptNumber },
									dataType: "html",
									success: function(data) {
										$("#loading").hide();
										var datadelete = data.split("+");
										var berhasil = datadelete[0];
										var gagal = datadelete [1];
										if(berhasil!="") {
											$.notify(berhasil, "success");
											$("#grid-data").bootgrid("reload");
										}
										if(gagal!="") $.notify(gagal, "error");
									},
									error: function(data) {
										$.notify("Koneksi gagal, Cek koneksi internet!", "error");
										$("#loading").hide();
									}
										
								});
							},
							"Tidak": function() {
								$(this).dialog("close");
								return false;
							}
						}
					}).dialog("open");
				}
				
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
							url: "./DataSource.php",
							selection: true,
							multiSelect: true,
							rowSelect: true,
							keepSelection: false
						}).on("loaded.rs.jquery.bootgrid", function() {
					       $(".ReceivedDate").datepicker({
								dateFormat: 'dd M yy',
								dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu" ],
								monthNames: [ "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember" ],
								monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des" ],
								maxDate : "+0D",
								showOn: "button",
								buttonImage: "../../assets/img/calendar.gif",
								buttonImageOnly: true,
								buttonText: "Pilih Tanggal"
							}).datepicker("setDate", new Date());
						});

			});
		</script>
	</body>
</html>