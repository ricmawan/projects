<?php
	if(isset($_POST['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		//include "../../GetPermission.php";
		include "../../DBConfig.php";
		$RoomID = mysql_real_escape_string($_POST['ID']);
		$RoomNumber = "";
		if($RoomID != 0) {
			//$Content = "Place the content here";
			$sql = "SELECT
						RoomNumber,
						DailyRate,
						HourlyRate,
						RoomInfo
					FROM
						master_room
					WHERE
						RoomID = $RoomID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$RoomNumber = $row['RoomNumber'];
		}
	}
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Daftar pemesanan kamar <?php echo $RoomNumber; ?></h5>
						 <input type="hidden" name="hdnRoomID" id="hdnRoomID" <?php echo 'value="'.$RoomID.'"'; ?> />
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th data-column-id="UserIDName" data-visible="false" data-type="numeric" data-identifier="true">UserID</th>
										<th data-column-id="RowNumber" data-sortable="false" data-type="numeric">No</th>
										<th data-column-id="CustomerName">Nama</th>
										<th data-column-id="Address">Alamat</th>
										<th data-column-id="Phone">No HP</th>
										<th data-column-id="BirthDate">Tanggal Lahir</th>
										<th data-column-id="StartDate">Dari</th>
										<th data-column-id="EndDate">Sampai</th>
										<th data-column-id="Opsi" data-formatter="commands" data-sortable="false">Opsi</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
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
							url: "./Transaction/CheckIn/BookingDataSource.php?RoomID=" + $("#hdnRoomID").val(),
							selection: false,
							multiSelect: false,
							rowSelect: false,
							keepSelection: false,
							formatters: {
								"commands": function(column, row)
								{
									return "<i style='cursor:pointer;' data-row-id=\"" + row.BookingID + "\" data-customer-name=\"" + row.CustomerName + "\" class=\"fa fa-times\" acronym title=\"Check In\"></i>&nbsp;";
								}
							}
						}).on("loaded.rs.jquery.bootgrid", function()
						{
							/* Executes after data is loaded and rendered */
							grid.find(".fa-times").on("click", function(e)
							{
								//Redirect($(this).data("link"));
								var ask=confirm("Apakah anda yakin ingin membatalkan pemesanan dari " + $(this).data("customer-name") + "?");
								if(ask==true) {
									$("#loading").show();
									$.ajax({
										url: "./Transaction/Cancellation/Cancel.php",
										type: "POST",
										data: { ID : $(this).data("row-id") },
										dataType: "json",
										success: function(data) {
											if(data.FailedFlag == '0') {
												$.notify(data.Message, "success");
												$("#loading").hide();
												$("#page-inner-right").html("");
												LoadRoom();
											}
											else {
												$("#loading").hide();
												$.notify(data.Message, "error");					
											}
										},
										error: function(data) {
											$.notify("Koneksi gagal", "error");
											$("#loading").hide();
										}
									});
								}
							});
						});
			});
		</script>
	</body>
</html>
