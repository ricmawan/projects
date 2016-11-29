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
						 <h5>Pemesanan</h5>
					</div>
					<div class="panel-body">
						<?php
							include "../../DBConfig.php";
							$sql = "SELECT
										MT.TableID,
										MT.TableNumber,
										MT.TableStatusID,
										MTS.TableStatusName,
										IFNULL(TS.SaleID, 0) SaleID
									FROM
										master_table MT
										JOIN master_tablestatus MTS
											ON MTS.TableStatusID = MT.TableStatusID
										LEFT JOIN
										(
											SELECT
												TS.TableID,
												MAX(TS.SaleID) SaleID
											FROM
												transaction_sale TS
											WHERE
												TS.IsDone = 0
												AND TS.IsCancelled = 0
											GROUP BY
												TS.TableID
										)TS
											ON MT.TableID = TS.TableID
									ORDER BY
										MT.TableNumber ASC";
							if(!$result = mysql_query($sql, $dbh)) {
								echo mysql_error();
								return 0;
							}
							while($row = mysql_fetch_array($result)) {
								echo "<span acronym title='".$row['TableStatusName']."' class='tables ".$row['TableStatusName']." dropdown' tableid=".$row['TableID']." >".$row['TableNumber']."
										<span class='dropbtn'></span>
										<div class='dropdown-content'>";
								if($row['TableStatusID'] == 1) echo "<a href='#' onclick='OrderMenu(".$row['TableID'].", \"".$row['TableNumber']."\");' >Pemesanan</a>";
								if($row['TableStatusID'] == 2) echo "<a href='#' onclick='ChangeOrder(".$row['SaleID'].");' >Ubah Pesanan</a>";
								if($row['TableStatusID'] == 2) echo "<a href='#' onclick='Cancellation(".$row['SaleID'].", ".$row['TableID'].", \"".$row['TableNumber']."\");' >Pembatalan</a>";
								if($row['TableStatusID'] == 2) echo "<a href='#' onclick='Payment(".$row['SaleID'].", \"".$row['TableNumber']."\");' >Pembayaran</a>";
								echo "</div></span>";
							}
						?>
					</div>
				</div>
			</div>
			<div id="cancel-confirm" title="Konfirmasi Pembatalan" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin ingin membatalkan pesanan meja nomor <span style="font-weight: bold; font-size: 24px; color: red;" id="TableNumber"></span>?</span>
			</div>
			<div id="payment-confirm" title="Konfirmasi Pembayaran" style="display: none;">
				<div class="row">
					<div class="col-md-2 labelColumn">
						Nomor Meja :
					</div>
					<div class="col-md-4 labelColumn">
						<span style="font-weight: bold; font-size: 24px; color: red;" id="TableNumber2"></span>
					</div>
					<div class="col-md-2 labelColumn">
						Tanggal :
					</div>
					<div class="col-md-4">
						<?php echo date('d')."-".date('m')."-".date('Y'); ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<table class="table" style="width:auto;" id="datainput">
							<thead style="background-color: black;color:white;height:35px;width:979px;display:block;">
								<tr>
									<td align="center" style="width:34px;">No</td>
									<td align="center" style="width:310px;">Nama Menu</td>
									<td align="center" style="width:80px;">QTY</td>
									<td align="center" style="width:170px;">Harga Jual</td>
									<td align="center" style="width:195px;">Diskon</td>
									<td align="center" style="width:190px;">Total</td>
								</tr>
							</thead>
							<tbody id="tableBody" style="display:block;max-height:256px;height:100%;overflow-y:auto;" >
							
							</tbody>
						</table>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-2">
						Sub Total :
					</div>
					<div class="col-md-3">
						<span id="discountTotal" style="text-align:right;" ></span>
					</div>
					<div class="col-md-2">
						Pembayaran :
					</div>
					<div class="col-md-3">
						<input type="text" id="txtPayment" value="0.00" autocomplete=off name="txtPrice" style="text-align:right;border:none;" class="form-control-custom txtPrice" onClick="this.select();" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Pembayaran"/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						Diskon :
					</div>
					<div class="col-md-3">
						<span id="discountTotal" style="text-align:right;" ></span>
					</div>
					<div class="col-md-2">
						Kembali :
					</div>
					<div class="col-md-3">
						<span id="GrandTotal" style="text-align:right;" ></span>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						Grand Total :
					</div>
					<div class="col-md-3">
						<span id="GrandTotal" style="text-align:right;" ></span>
					</div>
				</div>
			</div>
		</div>
		<script>
			function OrderMenu(TableID, TableNumber) {
				$("#page-inner").html("");
				//PrevMenu = CurrentMenu;
				//CurrentMenu = "./Transaction/Order/Order.php";
				$.ajax({
					url: "./Transaction/Order/Order.php",
					type: "POST",
					data: { TableID : TableID, TableNumber : TableNumber },
					dataType: "html",
					success: function(data) {
						$("#page-inner").html(data);
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
			
			function ChangeOrder(SaleID) {
				$("#page-inner").html("");
				$.ajax({
					url: "./Transaction/Order/ChangeOrder.php",
					type: "POST",
					data: { SaleID : SaleID },
					dataType: "html",
					success: function(data) {
						$("#page-inner").html(data);
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
			
			function Cancellation(SaleID, TableID, TableNumber) {
				$("#TableNumber").html(TableNumber);
				$("#cancel-confirm").dialog({
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
						"Ya": function() {
							$("#loading").show();
							$.ajax({
								url: "./Transaction/Order/Cancellation.php",
								type: "POST",
								data: { SaleID : SaleID, TableID : TableID },
								dataType: "json",
								success: function(data) {
									if(data.FailedFlag == '0') {
										$.notify(data.Message, "success");
										$("html, body").animate({
											scrollTop: 0
										}, "slow");
										Reload();
										$("#cancel-confirm").dialog("destroy");
										$("#loading").hide();
									}
									else {
										$("#loading").hide();
										$.notify(data.Message, "error");					
									}
								},
								error: function(data) {
									$("#loading").hide();
									$.notify("Koneksi gagal", "error");
								}
							});
						},
						"Tidak": function() {
							$(this).dialog("destroy");
						}
					}
				}).dialog("open");
			}
			
			function Payment(SaleID, TableNumber) {
				$("#TableNumber2").html(TableNumber);
				$.ajax({
					url: "./Transaction/Order/Detail.php",
					type: "POST",
					data: { SaleID : SaleID },
					dataType: "html",
					success: function(data) {
						$("#tableBody").html(data);
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
				$("#payment-confirm").dialog({
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
					height: 680,
					width: 1200,
					modal: true,
					close: function() {
						$(this).dialog("destroy");
					},
					buttons: {
						"Ya": function() {
							console.log("ya");
						},
						"Tidak": function() {
							$(this).dialog("destroy");
						}
					}
				}).dialog("open");
			}
		</script>
	</body>
</html>
