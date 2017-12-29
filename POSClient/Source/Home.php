<?php
	include __DIR__ . "/DBConfig.php";
	include __DIR__ . "/GetSession.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>POS</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
		<link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
		<link href="assets/css/custom.css" rel="stylesheet" />
		<link href="assets/css/jquery.bootgrid.css" rel="stylesheet" />
		<link href="assets/css/bootstrap-float-label.min.css" rel="stylesheet" />
		
		<!--<link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />-->
		<!--<link rel="stylesheet" href="assets/css/bootstrap-multiselect.css" type="text/css"/>-->
		
		<link rel="shortcut icon" href="./assets/img/logo.ico">
		<link rel="apple-touch-icon" sizes="57x57" href="./assets/img/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="./assets/img/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="./assets/img/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="./assets/img/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="./assets/img/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="./assets/img/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="./assets/img/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="./assets/img/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="./assets/img/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="./assets/img/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="./assets/img/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="./assets/img/favicon-16x16.png">
		<link rel="manifest" href="./assets/img/manifest.json">
		<meta name="msapplication-config" content="./assets/img/browserconfig.xml">
		<meta name="msapplication-TileImage" content="./assets/img/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">
		<style>
			.navbar {
				min-height : 0px;
			}
			@media (min-width: 992px) {
				.col-md-12 {
				   float: none;
				}
			}
			.panel-heading {
				padding: 1px 15px;
			}
			.panel {
				margin-bottom : 0px;
			}
			.panel-body {
				margin-bottom : 0px;
				padding : 5px;
			}
			h1, .h1, h2, .h2, h3, .h3 {
				margin-top: 5px;
				margin-bottom: 5px;
			}
			.panel-default {
				height : device-height;
			}
			.bootgrid-footer {
				margin: 10px 0;
			}
			.actionBar, #grid-data-footer, #grid-data-header {
				display: none;
			}
			.fa-2x {
				font-size: 1.2em;
			}
		</style>
	</head>
	<body> 
		<div id="wrapper">
			<nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0; min-height : 35px;">
				<!--<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<!--<a class="navbar-brand" href="#"></a>
				</div>-->
				<span id="Clock" style="color: white; padding: 5px 20px 0px 20px; float: left; font-size: 16px;"></span>
				<div style="color: white; padding: 5px 20px 5px 50px; float: right; font-size: 16px;"> 
					 Selamat Datang, <a href="#" style="color: white;font-size: 16px;" onclick="UpdatePassword();"><?php echo $_SESSION['Nama']; ?>!</a> &nbsp;&nbsp;&nbsp;<a href="#" class="menu" link="./Logout.php"><img src="./assets/img/logout.png" width="20px" border="0" acronym title="Logout" /></a>
				</div>
			</nav>   
			<!-- /. NAV TOP  -->
			
			<div id="page-wrapper">
				<div id="page-inner">
					<div class="panel panel-default">
						<div class="panel-heading">
							<span style="width:50%;display:inline-block;">
								<h5>Transaksi Penjualan Eceran</h5>
							</span>
							<span style="width:49%;display:inline-block;text-align:right;">
								<b>No Invoice: 00000001</b>
							</span>
						</div>
						<div class="panel-body">
							<div id="leftSide" class="col-md-12" style="display:inline-block;width:25%;border-right:3px double black;float:left;" >
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<input tabindex=1 id="txtCustomer" name="txtCustomer" type="text" class="form-control-custom" placeholder="Pelanggan" />
										<label for="txtCustomer" style="font-family:'Open Sans', sans-serif;font-size: 14px;font-weight:400;font-stretch:100%;">Pelanggan</label>
									</div>
								</div>
								<br />
								
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<input tabindex=2 id="txtItemCode" name="txtItemCode" type="text" class="form-control-custom" placeholder="Kode Barang" />
										<label for="txtItemCode" style="font-family:'Open Sans', sans-serif;font-size: 14px;font-weight:400;font-stretch:100%;">Kode Barang</label>
									</div>
								</div>
								<br />
								
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<input disabled id="txtItemName" name="txtItemName" type="text" class="form-control-custom" placeholder="Nama Barang" />
										<label for="txtItemName" style="font-family:'Open Sans', sans-serif;font-size: 14px;font-weight:400;font-stretch:100%;">Nama Barang</label>
									</div>
								</div>
								<br />
								
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<input tabindex=3 id="txtQTY" onfocus="this.select();" name="txtQTY" type="number" class="form-control-custom" placeholder="QTY" style="border: 1px solid #ccc !important;margin: 0;" value=1 />
										<label for="txtQTY" style="font-family:'Open Sans', sans-serif;font-size: 14px;font-weight:400;font-stretch:100%;">Qty</label>
									</div>
								</div>
								<br />
								
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<input readonly id="txtSalePrice" name="txtSalePrice" type="number" class="form-control-custom" placeholder="Harga" />
										<label for="txtSalePrice" style="font-family:'Open Sans', sans-serif;font-size: 14px;font-weight:400;font-stretch:100%;">Harga</label>
									</div>
								</div>
								<br />
								
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<input tabindex=4 id="txtDiscount" name="txtDiscount" type="number" class="form-control-custom" placeholder="Diskon" />
										<label for="txtDiscount" style="font-family:'Open Sans', sans-serif;font-size: 14px;font-weight:400;font-stretch:100%;">Diskon</label>
									</div>
								</div>
								<br />
								<div class="row" >
									<div class="col-md-12" >
										<input type="checkbox" name="chkInventory" id="chkInventory" />
										<span style="vertical-align: text-bottom;">Gudang</span>
									</div>
								</div>
								<br />
								<button type="button" style="height:55px;width:49%;" class="btn btn-default" value="Simpan" onclick="AppMode();" ><i class="fa fa-save fa-2x"></i> Simpan</button>
								<button type="button" style="height:55px;width:49%;" class="btn btn-default" value="Cetak" onclick="SubmitValidate(this.form);" ><i class="fa fa-print fa-2x"></i> Cetak<br /> Surat Jalan</button>
								<br />
								<button type="button" style="height:55px;width:49%;" class="btn btn-default" value="Cetak" onclick="SubmitValidate(this.form);" ><i class="fa fa-list fa-2x"></i> Daftar<br /> Barang</button>
								<button type="button" style="height:55px;width:49%;" class="btn btn-default" value="Cetak" onclick="SubmitValidate(this.form);" ><i class="fa fa-window-restore fa-2x"></i> Nota <br />Baru</button>
								<br />
								<button type="button" style="height:55px;width:49%;" class="btn btn-default" value="Cancel" onclick="SubmitValidate(this.form);" ><i class="fa fa-arrow-circle-left fa-2x"></i> Retur</button>
								<button type="button" style="height:55px;width:49%;" class="btn btn-default" value="Cetak" onclick="SubmitValidate(this.form);" ><i class="fa fa-cart-arrow-down fa-2x"></i> Grosir</button>
							</div>
							<div class="col-md-12" style="display:inline-block;width:75%;float:left;" >
								<div class="row" style="max-height: 400px;overflow-y:auto;" >
									<div class="col-md-12" >
										<div class="table-responsive">
											<table id="grid-data" class="table table-striped table-bordered table-hover" >
												<thead>				
													<tr>
														<th data-column-id="RowNumber" >No</th>
														<th data-column-id="ItemCode" >Kode</th>
														<th data-column-id="ItemName">Nama</th>
														<th data-column-id="QTY" data-align="right" >Qty</th>
														<th data-column-id="SalePrice" data-align="right">Harga</th>
														<th data-column-id="Discount" data-align="right">Diskon</th>
														<th data-column-id="Total" data-align="right">Total</th>
														<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>1</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>2</td>
														<td>000000000000</td>
														<td>Semen Tiga Roda Kuat Sekaliiii</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>55.050.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>3</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>4</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>5</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>6</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>7</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>100.000</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>8</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>1.055.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>9</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>10</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>11</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>12</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>13</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>14</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>15</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>16</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													<tr>
														<td>17</td>
														<td>000000000000</td>
														<td>Semen TR</td>
														<td>10</td>
														<td>55.0000</td>
														<td>0</td>
														<td>550.000</td>
														<td>
															<span style="background-color:blue;padding:3px 0 1px 5px;margin-right:2px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-edit fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
															<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;">
																<i style='cursor:pointer;' class="fa fa-trash fa-2x" acronym title="Ubah Data" onclick="alert();"></i>
															</span>
														</td>
													</tr>
													
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<br />
								<div class="row" >
									<div class="col-md-12" style="color:#42f4f1;width: calc(70% - 20px);display:inline-block;float:left;min-width:240px;height:5%;min-height:45px;margin:10px 0 0 auto;text-align:left;font-size:32px;font-weight:bold;">
										<h3>Berat: 1.100 (KG)</h3>
									</div>
									<div style="width: 30%;display:inline-block;float:left;height:5%;min-height:45px;margin:10px 20px 0 auto;text-align:right;background-color:black;color:white;font-size:32px;font-weight:bold;">5.500.000</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="delete-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin ingin menghapusnya?</p>
		</div>
		<div id="save-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
		</div>
		<div id="update-password" title="Ganti Password" style="display: none;">
			<form class="col-md-12" id="UpdatePasswordForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-5 labelColumn">
						Password Lama :
					</div>
					<div class="col-md-6">
						<input id="txtCurrentPassword" name="txtCurrentPassword" type="password" class="form-control-custom" placeholder="Password Lama" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Password Baru :
					</div>
					<div class="col-md-6">
						<input id="txtNewPassword" name="txtNewPassword" type="password" class="form-control-custom" placeholder="Password Baru" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Konfirmasi Password :
					</div>
					<div class="col-md-6">
						<input id="txtConfirmNewPassword" name="txtConfirmNewPassword" type="password" class="form-control-custom" placeholder="Konfirmasi Password" />
					</div>
				</div>
			</form>
		</div>
		<script src="assets/js/jquery-1.10.2.js"></script>
		<script src="assets/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/jquery.metisMenu.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="assets/js/notify.js"></script>
		<script src="assets/js/global.js"></script>
		<script src="assets/js/jquery.bootgrid.js"></script>
		<!--<script src="assets/js/dataTables/jquery.dataTables.js"></script>
		<script src="assets/js/dataTables/dataTables.bootstrap.js"></script>-->
		<script src="assets/js/bootstrap-multiselect.js"></script>
		<a href="#Top" class="scrollup" onclick="return false;">Scroll</a>
		<!--<a href="#Bottom" class="scrolldown" onclick="return false;">Scroll</a>-->
		<div id="loading"></div>
		<iframe id='excelDownload' src='' style='display:none'></iframe>
		<script type="text/javascript" >
			$(document).ready(function() {
				$("#txtQTY").spinner();
				var windowHeight = $( window ).height() - 45;
				if(windowHeight < 540) {
					windowHeight = 540;
				}
				$("#page-inner").css ({
					"min-height" : windowHeight,
					"max-height" : windowHeight,
					"overflow-x" : "hidden",
					"overflow-y" : "auto"
				});
				
				$("#page-wrapper").css ({
					"min-height" : windowHeight
				});
				
				$("#leftSide").css ({
					"min-height" : windowHeight - 55
				});
				
				$("head").append("<style> .panel-default { min-height : " + windowHeight + "px } </style>");
				$(".panel-default").css ({
					"min-height" : windowHeight
				});
				
				$("#tableContent").width($("#dvTableContent").width());
				
				$(window).resize(function() {
					$("#tableContent").width($("#dvTableContent").width());
				});
				/*$.ajax({
					url: "./Master/Notification/",
					type: "POST",
					data: { },
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
				});*/
			});
			var grid = $("#grid-data").bootgrid({
							ajax: true,
							rowCount: -1,
							sorting: false,
							columnSelection: false,
							post: function ()
							{
								/* To accumulate custom parameter with the request object */
								return {
									id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
								};
							},
							 templates: {
								search: ""
							},
							css: {
								iconRefresh: "none"
							},
							labels: {
								all: "Semua Data",
								infos: "Menampilkan {{ctx.start}} sampai {{ctx.end}} dari {{ctx.total}} data",
								loading: "Loading...",
								noResults: "Tidak Ada Data Yang Ditemukan!",
								refresh: "Refresh",
								search: "Cari"
							},
							selection: true,
							multiSelect: true,
							rowSelect: true,
							keepSelection: true,
							formatters: {
								"commands": function(column, row)
								{
									var option = "<i style='cursor:pointer;' class=\"fa fa-delete\" acronym title=\"Ubah Data\"></i>&nbsp;&nbsp;&nbsp;";
									return option;
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							
						});
			
			//alert($( window ).height());
		</script>
	</body>
</html>