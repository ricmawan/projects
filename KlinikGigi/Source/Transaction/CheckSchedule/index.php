<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<link href='./assets/css/fullcalendar.min.css' rel='stylesheet' />
		<link href='./assets/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />
		<script src='./assets/js/moment.min.js'></script>
		<script src='./assets/js/fullcalendar.min.js'></script>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h5>Jadwal Periksa</h5>
					</div>
					<div class="panel-body">
						<div class="row col-md-12" style="height:100%;">
								<div class="col-md-1 labelColumn" >
								Cabang:
							</div>
							<div class="col-md-2" >
								<select id="ddlBranch" name="ddlBranch" class="form-control-custom" onchange="reloadEvent();">
									<?php
										$sql = "SELECT BranchID, BranchName, StartHour, EndHour FROM master_branch";
										if(!$result = mysql_query($sql, $dbh)) {
											echo mysql_error();
											return 0;
										}
										while($row = mysql_fetch_array($result)) {
											echo "<option value='".$row['BranchID']."' startHour=".$row['StartHour']." endHour=".$row['EndHour']." >".$row['BranchName']."</option>";
										}
									?>
								</select>
							</div>
						</div>
						<br />
						<div class="row col-md-12" style="height:100%;">
							<div id='calendar'></div>
						</div>
					</div>
				</div>
			</div>
			<div id="dialog-schedule" title="Pendaftaran Periksa Gigi" style="display: none;">
				<form class="col-md-12" id="ScheduleForm" method="POST" action="" >
					<input type="hidden" id="hdnStartDate" name="hdnStartDate" value=0 autofocus="autofocus" />
					<input type="hidden" id="hdnDDLBranch" name="hdnDDLBranch" value=1 autofocus="autofocus" />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Tanggal:
						</div>
						<div class="col-md-5" >
							<span style="font-weight: bold; font-size: 18px; color: red;" id="lblStartDate"></span>
						</div>
						<div class="col-md-4" >
							<select id="ddlTime" name="ddlTime" class="form-control-custom" >
								
							</select>
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-3 labelColumn">
							Nama:
						</div>
						<div class="col-md-9">
							<input id="hdnPatientID" name="hdnPatientID" type="hidden" value="0" />
							<div class="ui-widget" style="width: 100%;" id="dvPatient">
								<select name="ddlPatient" id="ddlPatient" class="form-control-custom" placeholder="Pilih Pasien" >
									<option value="" ></option>
									<?php
										$sql = "SELECT PatientID, PatientName, PatientNumber, Address, Telephone, Email FROM master_patient";
										if(!$result = mysql_query($sql, $dbh)) {
											echo mysql_error();
											return 0;
										}
										while($row = mysql_fetch_array($result)) {
											echo "<option value='".$row['PatientID']."' patientname='".$row['PatientName']."' patientnumber='".$row['PatientNumber']."' telephone='".$row['Telephone']."' email='".$row['Email']."' >".$row['PatientName']." - ".$row['Address']."</option>";
										}
									?>
								</select>
							</div>
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							No HP:
						</div>
						<div class="col-md-9">
							<input type="text" placeholder="No HP" required readonly id="txtPhone" name="txtPhone" class="form-control-custom" />
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Email:
						</div>
						<div class="col-md-9">
							<input type="text" placeholder="Email" id="txtEmail" readonly name="txtEmail" class="form-control-custom" />
						</div>
					</div>
					<br />
				</form>
			</div>
			
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;" class="col-md-12" >
				<div class="row col-md-12" >
					<div class="col-md-3 labelColumn" >
						Nama:
					</div>
					<div class="col-md-9">
						<span id="lblPatientName" style="font-weight: bold; font-size: 15px; color: red;"></span>
					</div>
				</div>
				<div class="row col-md-12" >
					<div class="col-md-3 labelColumn" >
						No HP:
					</div>
					<div class="col-md-9">
						<span id="lblPhoneNumber" style="font-weight: bold; font-size: 15px; color: red;"></span>
					</div>
				</div>
				<div class="row col-md-12" >
					<div class="col-md-3 labelColumn" >
						Email:
					</div>
					<div class="col-md-9">
						<span id="lblEmail" style="font-weight: bold; font-size: 15px; color: red;"></span>
					</div>
				</div>
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
			
			<div id="dialog-schedule-list" title="Jadwal Periksa Pasien" style="display: none;">
				<table class="table table-striped table-bordered table-hover" style="width:auto;padding-right:17px;" id="datainput">
					<thead style="background-color: black;color:white;height:25px;display:block;width:1085px;">
						<td align="center" style="width:35px;">No</td>
						<td align="center" style="width: 200px;" >Nama</td>
						<td align="center" style="width: 150px;" >No HP</td>
						<td align="center" style="width: 250px;" >Email</td>
						<td align="center" style="width: 200px;" >Jadwal</td>
						<td align="center" style="width: 200px;" >Cabang</td>
						<td align="center" style="width: 50px;" >Opsi</td>
					</thead>
					<tbody style="display:block;max-height:200px;height:100%;overflow-y:auto;" id="tableContent">
					</tbody>
				</table>
			</div>
		</div>
		<script>
			var businessHours;
			var startDateGlobal;
			function hideUnavailableTime(dayOfWeek) {
				var startDate = $("#hdnStartDate").val();
				$("#ddlTime option").each(function() {
					$(this).show();
				});
				$.ajax({
					url: "./Transaction/CheckSchedule/UnavailableTime.php",
					type: "POST",
					data: { dayOfWeek : dayOfWeek, BranchID : $("#ddlBranch").val(), StartDate : startDate },
					dataType: "json",
					success: function(data) {
						$("#loading").hide();
						for(var i=0;i<data.length;i++) {
							$("#ddlTime option[value='" + data[i].unavailableTime + "']").hide();
						}
						$("#ddlTime option").each(function() {
							if($(this).css("display") == "block") {
								$(this).prop("selected", true);
								return false;
							}
						});
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
			
			function loadSchedule(startDate) {
				$("#loading").show();
				$.ajax({
					url: "./Transaction/CheckSchedule/Detail.php",
					type: "POST",
					data: { StartDate : startDate },
					dataType: "json",
					success: function(data) {
						$("#loading").hide();
						if(data.FailedFlag == '0') {
							if(data.ScheduleDetails == "") {
								$.notify("Jadwal tidak ditemukan!", "error");
							}
							else {
								$("#tableContent").html(data.ScheduleDetails);
								$("#dialog-schedule-list").dialog({
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
									width: 1140,
									modal: true,
									close: function() {
										$(this).dialog("destroy");
									}
								}).dialog("open");
							}
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
			}
			function reloadEvent() {
				$("#hdnDDLBranch").val($("#ddlBranch").val());
				$('#calendar').fullCalendar('refetchEvents');
				getDayOfWeek();
			}

			function getDayOfWeek() {
				$.ajax({
					url: "./Transaction/CheckSchedule/GetDayOfWeek.php",
					type: "POST",
					data: { BranchID : $("#ddlBranch").val() },
					dataType: "json",
					success: function(data) {
						$("#loading").hide();
						$('#calendar').fullCalendar('option', {
							businessHours: {
								dow: JSON.parse("[" + data[0].dow + "]")
							}
						});

						businessHours = JSON.parse("[" + data[0].dow + "]");
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
			
			function ddlTime(dayOfWeek) {
				$("#ddlTime option").each(function() {
					$(this).remove();
				});
				
				$.ajax({
					url: "./Transaction/CheckSchedule/GetTime.php",
					type: "POST",
					data: { dayOfWeek : dayOfWeek, BranchID : $("#ddlBranch").val() },
					dataType: "json",
					success: function(data) {
						$("#loading").hide();
						for(var i=0;i<data.length;i++) {
							for(var j=parseInt(data[i].StartHour);j<=parseInt(data[i].EndHour);j++) {
								$("#ddlTime").append("<option value='" + j + ":00' >" + j + ":00</option>");
								if(j!=data[i].EndHour) {
									$("#ddlTime").append("<option value='" + j + ":15' >" + j + ":15</option>");
									$("#ddlTime").append("<option value='" + j + ":30' >" + j + ":30</option>");
									$("#ddlTime").append("<option value='" + j + ":45' >" + j + ":45</option>");
								}
							}
						}
						hideUnavailableTime(dayOfWeek);
					},
					error: function(data) {
						$("#loading").hide();
						$.notify("Terjadi kesalahan sistem!", "error");
					}
				});
			}
			
			function dialogSchedule() {
				//hideUnavailableTime();
				$("#dialog-schedule").dialog({
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
						"Simpan": function() {
							//$(this).dialog("close");
							$("#lblPatientName").html($("#ddlPatient option:selected").attr("patientname"));
							$("#lblPhoneNumber").html($("#txtPhone").val());
							$("#lblEmail").html($("#txtEmail").val());
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
										var PassValidate = 1;
										$(".form-control-custom").each(function() {
											if($(this).hasAttr('required')) {
												if($(this).val() == "") {
													PassValidate = 0;
													$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
													if(FirstFocus == 0) $(this).focus();
													FirstFocus = 1;
												}
											}
										});
										if(PassValidate == 0) return false;
										else {
											$("#loading").show();
											$.ajax({
												url: "./Transaction/CheckSchedule/Insert.php",
												type: "POST",
												data: $("#ScheduleForm").serialize(),
												dataType: "json",
												success: function(data) {
													$("#loading").hide();
													if(data.FailedFlag == '0') {
														$.notify(data.Message, "success");
														$("#ddlPatient").val("");
														$("#ddlPatient").next().find("input").val("");
														$("#txtPhone").val("");
														$("#txtEmail").val("");
														$("#dialog-schedule").dialog("destroy");
														$('#calendar').fullCalendar('refetchEvents');
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
										}
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
			}

			function deleteSchedule(ScheduleID, OnlineFlag) {
				$("#delete-confirm").dialog({
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
								url: "./Transaction/CheckSchedule/Delete.php",
								type: "POST",
								data: { ID : ScheduleID, OnlineFlag : OnlineFlag },
								dataType: "html",
								success: function(data) {
									$("#loading").hide();
									var datadelete = data.split("+");
									var berhasil = datadelete[0];
									var gagal = datadelete [1];
									if(berhasil!="") $.notify(berhasil, "success");
									if(gagal!="") $.notify(gagal, "error");
									loadSchedule(startDateGlobal);
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
			
			$(document).ready(function() {
				//ddlTime();
				$("#ddlPatient").combobox({
					select: function( event, ui ) {
						var Telephone = $("#ddlPatient option:selected").attr("telephone");
						var Email = $("#ddlPatient option:selected").attr("email");
						$("#txtEmail").val(Email);
						$("#txtPhone").val(Telephone);
					}
				});
				var currentDate = new Date();
				var currentMonth;
				currentDate.setMonth(currentDate.getMonth() + 6);
				var endMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
				if((currentDate.getMonth() + 1) <= 9) {
					currentMonth = "0" + (currentDate.getMonth() + 1).toString();
				}
				else currentMonth = (currentDate.getMonth() + 1).toString();
				var endDate = new Date(currentDate.getFullYear().toString() + "-" + currentMonth + "-" + endMonth.toString());
				endDate.setDate(endDate.getDate() + 1);
				currentDate = new Date();
				currentDate.setDate(currentDate.getDate() - 1);
				$('#calendar').fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'prevYear, nextYear'
					},
					eventClick: function(calEvent, jsEvent, view) {

						/*alert('Event: ' + calEvent.patientid);
						alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
						alert('View: ' + view.name);*/

						// change the border color just for fun
						//$(this).css('border-color', 'red');
						var date = calEvent.start;
						var count = 0;
						if(businessHours.indexOf(parseInt(date.format('e')) ) >= 0) {
							$('#calendar').fullCalendar('clientEvents', function(event) {
								if(moment(date).format('YYYY-MM-DD') == moment(event.start._i).format('YYYY-MM-DD') && count == 0) {
									count++;
									if(event.isavailable[0] == "0") {
										$.notify("Jadwal Penuh!", "error");
										return false;
									}
									else {
										var startDate = new Date(date);
										var scheduledDate = startDate.getFullYear().toString() + "-" + (startDate.getMonth() + 1).toString() + "-" + startDate.getDate().toString();
										$("#lblStartDate").html(startDate.getDate().toString() + "-" + (startDate.getMonth() + 1).toString() + "-" + startDate.getFullYear().toString());
										$("#hdnStartDate").val(scheduledDate);
										dialogSchedule();
										ddlTime(parseInt(date.format('e')));
									}
								}
							});
							if(count == 0) {
								var startDate = new Date(date);
								var scheduledDate = startDate.getFullYear().toString() + "-" + (startDate.getMonth() + 1).toString() + "-" + startDate.getDate().toString();
								$("#lblStartDate").html(startDate.getDate().toString() + "-" + (startDate.getMonth() + 1).toString() + "-" + startDate.getFullYear().toString());
								$("#hdnStartDate").val(scheduledDate);
								dialogSchedule();
								ddlTime(parseInt(date.format('e')));
							}
						}

					},
					navLinks: true, // can click day/week names to navigate views
					selectable: true,
					selectHelper: true,
					fixedWeekCount: false,
					height: 500,
					eventStartEditable: false,
					validRange: function() {
						return {
							start: currentDate,
							end: endDate
						};
					},
					dayClick: function(date, allDay, jsEvent, view) {
						/*var title = prompt('Event Title:');
						var eventData;
						if (title) {
							eventData = {
								title: title,
								start: start,
								end: end
							};
							$('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
						}
						$('#calendar').fullCalendar('unselect');*/
						var count = 0;
						if(businessHours.indexOf(parseInt(date.format('e')) ) >= 0) {
							$('#calendar').fullCalendar('clientEvents', function(event) {
								if(moment(date).format('YYYY-MM-DD') == moment(event.start._i).format('YYYY-MM-DD') && count == 0) {
									count++;
									if(event.isavailable[0] == "0") {
										$.notify("Jadwal Penuh!", "error");
										return false;
									}
									else {
										var startDate = new Date(date);
										var scheduledDate = startDate.getFullYear().toString() + "-" + (startDate.getMonth() + 1).toString() + "-" + startDate.getDate().toString();
										$("#lblStartDate").html(startDate.getDate().toString() + "-" + (startDate.getMonth() + 1).toString() + "-" + startDate.getFullYear().toString());
										$("#hdnStartDate").val(scheduledDate);
										dialogSchedule();
										ddlTime(parseInt(date.format('e')));
									}
								}
							});
							if(count == 0) {
								var startDate = new Date(date);
								var scheduledDate = startDate.getFullYear().toString() + "-" + (startDate.getMonth() + 1).toString() + "-" + startDate.getDate().toString();
								$("#lblStartDate").html(startDate.getDate().toString() + "-" + (startDate.getMonth() + 1).toString() + "-" + startDate.getFullYear().toString());
								$("#hdnStartDate").val(scheduledDate);
								dialogSchedule();
								ddlTime(parseInt(date.format('e')));
							}
						}
					},
					editable: true,
					eventLimit: true, // allow "more" link when too many events
					events: {
						url: "./Transaction/CheckSchedule/DataSource.php",
						 data: function () { // a function that returns an object
							return {
								ddlBranch: $('#ddlBranch').val(),
							};

						},
						error: function() {
							$('#script-warning').show();
						}
					},
					timeFormat: 'H:mm',
					selectConstraint: "businessHours",
					businessHours: {
						dow : [ 0, 1, 2, 3, 4, 5, 6]
					},
					eventLimitClick: function( cellInfo, jsEvent ) {
						loadSchedule(moment(cellInfo.date).format('YYYY-MM-DD'));
						startDateGlobal = moment(cellInfo.date).format('YYYY-MM-DD');
					},
					navLinkDayClick: function(date, jsEvent) {
						loadSchedule(date.format('YYYY-MM-DD'));
						startDateGlobal = date.format('YYYY-MM-DD');
					}
				});
				getDayOfWeek();
			});
		</script>
	</body>
</html>
