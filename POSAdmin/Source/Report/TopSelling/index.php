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
						 <h5>Barang Terlaris</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-1 labelColumn">
								Kategori:
							</div>
							<div class="col-md-2">
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
								Tanggal :
							</div>
							<div class="col-md-2">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtFromDate" name="txtFromDate" type="text" class="form-control-custom" style="background-color: #FFF;cursor: text;" placeholder="Dari Tanggal" readonly />
								</div>
							</div>
							<div style="float:left;" class="labelColumn">
								-
							</div>
							<div class="col-md-2">
								<div class="ui-widget" style="width: 100%;">
									<input id="txtToDate" name="txtToDate" type="text" class="form-control-custom" style="background-color: #FFF;cursor: text;" placeholder="Sampai Tanggal" readonly />
								</div>
							</div>
							<div class="col-md-3">
								<button class="btn btn-info" id="btnView" onclick="Preview();" style="padding-top: 1px;padding-bottom: 1px;" ><i class="fa fa-list"></i> Lihat</button>
							</div>
						</div>
						<hr style="margin: 10px 0;" />
						<div class="chart-container" style="position: relative; height:70vh; width:80vw">
						    <canvas id="myChart"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var myChart;
			var ctx = document.getElementById("myChart").getContext('2d');
			var chartFlag = 0;
			function Preview() {
				var CategoryID = $("#ddlCategory").val();
				var txtFromDate = $("#txtFromDate").val();
				var txtToDate = $("#txtToDate").val();
				var PassValidate = 1;
				var FirstFocus = 0;
			
				if(txtFromDate != "" && txtToDate != "") {
					var FromDate = txtFromDate.split("-");
					FromDate = new Date(FromDate[1] + "-" + FromDate[0] + "-" + FromDate[2]);
					var ToDate = txtToDate.split("-");
					ToDate = new Date(ToDate[1] + "-" + ToDate[0] + "-" + ToDate[2]);
					if(FromDate > ToDate) {
						$("#txtToDate").notify("Tanggal Akhir harus lebih besar dari tanggal mulai!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						PassValidate = 0;
						if(FirstFocus == 0) $("#txtToDate").focus();
						FirstFocus = 1;
					}
				}

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
					$.ajax({
						url: "./Report/TopSelling/DataSource.php",
						type: "POST",
						data: { CategoryID : CategoryID, txtFromDate : txtFromDate, txtToDate : txtToDate },
						dataType: "json",
						success: function(Data) {
							if(Data.FailedFlag == '0') {
								if(chartFlag == 1) myChart.destroy();
								myChart = new Chart(ctx, {
									type: 'bar',
									data: {
										labels: Data.xData,
										datasets: [{
											label: 'Terjual',
											data: Data.yData,
											backgroundColor: [
												'rgba(255, 99, 132, 0.2)',
												'rgba(54, 162, 235, 0.2)',
												'rgba(255, 206, 86, 0.2)',
												'rgba(75, 192, 192, 0.2)',
												'rgba(153, 102, 255, 0.2)',
												'rgba(255, 99, 132, 0.2)',
												'rgba(54, 162, 235, 0.2)',
												'rgba(255, 206, 86, 0.2)',
												'rgba(75, 192, 192, 0.2)',
												'rgba(153, 102, 255, 0.2)'
											],
											borderColor: [
												'rgba(255,99,132,1)',
												'rgba(54, 162, 235, 1)',
												'rgba(255, 206, 86, 1)',
												'rgba(75, 192, 192, 1)',
												'rgba(153, 102, 255, 1)',
												'rgba(255,99,132,1)',
												'rgba(54, 162, 235, 1)',
												'rgba(255, 206, 86, 1)',
												'rgba(75, 192, 192, 1)',
												'rgba(153, 102, 255, 1)'
											],
											borderWidth: 1
										}]
									},
									options: {
										scales: {
											yAxes: [{
												ticks: {
													beginAtZero:true,
													callback: function (value) {
														return returnRupiah(value.toString());
													}
												}
											}]
										},
										tooltips: {
											callbacks: {
												label: function(tooltipItem, chart){
													var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
													return datasetLabel + ': ' + returnRupiah(tooltipItem.yLabel.toString());
												}
											}
										},
										legend: {
											display: false
										},
										maintainAspectRatio: false
									}
								});
								chartFlag = 1;
								$("#loading").hide();
							}
							else {
								var counter = 0;
								Lobibox.alert("error",
								{
									msg: "Gagal memuat data",
									width: 480,
									beforeClose: function() {
										if(counter == 0) {
											setTimeout(function() {
												//$("#txtItemCode").focus();
											}, 0);
											counter = 1;
										}
									}
								});
								return 0;
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$("#loading").hide();
							var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
							LogEvent(errorMessage, "/Transaction/Booking/index.php");
							Lobibox.alert("error",
							{
								msg: errorMessage,
								width: 480
							});
							return 0;
						}
					});
				}
			}
			$(document).ready(function () {				
				$("#ddlCategory").combobox();
				$("#ddlCategory").next().find("input").click(function() {
					$(this).val("");
				});

				$("#txtToDate, #txtFromDate").datepicker({
					dateFormat: 'dd-mm-yy',
					maxDate : "+0D"
				});
				
			});

		</script>
	</body>
</html>
