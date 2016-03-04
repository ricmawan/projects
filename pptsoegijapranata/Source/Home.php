<?php
	include "DBConfig.php";
	include "GetSession.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Pusat Psikologi Terapan Unika Soegijapranata</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
		<link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
		
		<link href="assets/css/custom.css" rel="stylesheet" />
		<link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
		<link rel="stylesheet" href="assets/css/bootstrap-multiselect.css" type="text/css"/>
		<!--<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />-->
		<link rel="shortcut icon" type="image/png" href="./assets/img/favicon.png"/>
	</head>
	<body>
		<div id="wrapper">
			<nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#"><?php echo $_SESSION['Jabatan']; ?></a> 
				</div>
				<span id="Clock" style="color: white; padding: 15px 50px 5px 20px; float: left; font-size: 16px;"></span>
				<div style="color: white; padding: 15px 50px 5px 50px; float: right; font-size: 16px;"> 
					 Selamat Datang, <?php echo $_SESSION['Nama']; ?>! &nbsp;&nbsp;&nbsp;<a href="#" class="menu" link="./Logout.php"><img src="./assets/img/logout.png" width="20px" border="0" acronym title="Logout" /></a>
				</div>
			</nav>   
			<!-- /. NAV TOP  -->
			<nav class="navbar-default navbar-side" role="navigation">
				<div class="sidebar-collapse">
					<ul class="nav" id="main-menu">
						<li class="text-center">
							<img src="assets/img/find_user.png" class="user-image img-responsive"/>
						</li>
						<li>
							<a href="#" onclick="Reload();"><i class="fa fa-refresh fa-3x"></i> Reload</a>
						</li>
						<li>
							<a class="menu active-menu" href="#" link="./Home.php"><i class="fa fa-home fa-3x "></i> Home</a>
						</li>
						<li>
							<a href="#"><i class="fa fa-database fa-3x"></i>&nbsp; Master Data<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li>
									<a href="#" link="./Master/User/" class="menu" >User</a>
								</li>
								<li>
									<a href="#" link="./Master/Parameter/" class="menu" >Parameter</a>
								</li>
								<li>
									<a href="#" link="./Master/Alat/" class="menu" >Alat</a>
								</li>
								<li>
									<a href="#" link="./Master/Layanan/" class="menu" >Layanan</a>
								</li>
								<!--<li>
									<a href="#" link="./Master/Jabatan/" class="menu" >Jabatan</a>
								</li>-->
								<li>
									<a href="#" link="./Master/Konsultan/" class="menu" >Konsultan</a>
								</li>
								<li>
									<a href="#" link="./Master/Supervisor/" class="menu" >Supervisor</a>
								</li>
								<li>
									<a href="#" link="./Master/Terapis/" class="menu" >Terapis</a>
								</li>
								<li>
									<a href="#" link="./Master/Asisten/" class="menu" >Asisten</a>
								</li>
								<li>
									<a href="#" link="./Master/JobdeskAsisten/" class="menu" >Jobdesk Asisten</a>
								</li>
							</ul>
						</li> 
						<li>
							<a href="#"><i class="fa fa-sitemap fa-3x"></i> Data Klien<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li>
									<a href="#" link="./Klien/PerusahaanIndustri/" class="menu" >Perusahaan/Industri</a>
								</li>
								<li>
									<a href="#" link="./Klien/AnakRemaja/" class="menu" >Anak & Remaja </a>
								</li>
								<li>
									<a href="#" link="./Klien/Dewasa/" class="menu" >Dewasa</a>
								</li>
								<li>
									<a href="#" link="./Klien/Pendidikan/" class="menu" >Pendidikan</a>
								</li>
							</ul>
						</li>  
						<li>
							<a href="#" ><i class="fa fa-laptop fa-3x"></i>Customer Service<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li>
									<a href="#" link="./CustomerService/CustomerService/" class="menu" >Transaksi</a>
								</li>
								<li>
									<a href="#" link="./CustomerService/Absen/" class="menu">Absen Peserta</a>
								</li>
							</ul>
						</li>
						<li>
							<a href="#"><i class="fa fa-table fa-3x"></i> Operasional<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li>
								<a href="#" link="./Keuangan/HonoriumAsisten/" class="menu" >Honorium Asisten</a>
								</li>
								<li>
								<a href="#" link="./Keuangan/InventarisMasuk/" class="menu" >Inventaris Masuk</a>
								</li>
								<li>
								<a href="#" link="./Keuangan/InventarisKeluar/" class="menu" >Inventaris Keluar</a>
								</li>
								<li>
								<a href="#" link="./Keuangan/Kas/" class="menu" >Kas Keluar</a>
								</li>
								<li>
								<a href="#" link="./Keuangan/PiketKonsultan/" class="menu" >Piket Konsultan</a>
								</li>
								<li>
								<a href="#" link="./Keuangan/PiketAsisten/" class="menu" >Piket Asisten</a>
								</li>
								<li>
								<a href="#" link="./Keuangan/FeeSpv/" class="menu" >Fee SPV</a>
								</li>
							</ul>
						</li>
						<li>
							<a href="#"><i class="fa fa-edit fa-3x"></i>Laporan<span class="fa arrow"></span></a>
								<ul class="nav nav-second-level">
								<li>
									<a href="#" link="./Laporan/HonoriumKonsultan/" class="menu" >Honorium Konsultan</a>
								</li>
								<li>
									<a href="#" link="./Laporan/HonoriumAsisten/" class="menu" >Honorium Asisten</a>
								</li>
								<li>
									<a href="#" link="./Laporan/Psikotes/" class="menu" >Psikotes</a>
								</li>
								<li>
									<a href="#" link="./Laporan/Keuangan/" class="menu" >Keuangan</a>
								</li>
								<li>
									<a href="#" link="./Laporan/Kas/" class="menu" >Kas Keluar</a>
								</li>
								<li>
									<a href="#" link="./Laporan/FeeSpv/" class="menu" >Fee SPV</a>
								</li>
								</ul>
						</li>  
					</ul>
				</div>
			</nav>  
			<!-- /. NAV SIDE  -->
			
			<div id="page-wrapper">
				<div id="page-inner">
					
				</div>
				<!-- /. PAGE INNER  -->
			</div>
			<!-- /. PAGE WRAPPER  -->
		</div>
		<!-- /. WRAPPER  -->
		<!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
		<!-- JQUERY SCRIPTS -->
		<script src="assets/js/jquery-1.10.2.js"></script>
		<script src="assets/js/jquery-ui-1.10.3.custom.js"></script>
		<!-- BOOTSTRAP SCRIPTS -->
		<script src="assets/js/bootstrap.min.js"></script>
		<!-- METISMENU SCRIPTS -->
		<script src="assets/js/jquery.metisMenu.js"></script>
		<!-- CUSTOM SCRIPTS -->
		<script src="assets/js/custom.js"></script>
		<script src="assets/js/global.js"></script>
		<script src="assets/js/notify.js"></script>
		<script src="assets/js/dataTables/jquery.dataTables.js"></script>
		<script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
		<script src="assets/js/bootstrap-multiselect.js"></script>
		<a href="#Top" class="scrollup" onclick="return false;">Scroll</a>
		<a href="#Bottom" class="scrolldown" onclick="return false;">Scroll</a>
		<div id="loading"></div>
		<iframe id='excelDownload' src='' style='display:none'></iframe>
	</body>
</html>
