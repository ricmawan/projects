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
							<div id='calendar'></div>
						</div>
					</div>
				</div>
			</div>
			<div id="dialog-schedule" title="Pendaftaran Periksa Gigi" style="display: none;">
				<form class="col-md-12" id="ScheduleForm" method="POST" action="" >
					<input type="hidden" id="hdnStartDate" name="hdnStartDate" value=0 autofocus="autofocus" />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Tanggal:
						</div>
						<div class="col-md-5" >
							<span style="font-weight: bold; font-size: 18px; color: red;" id="lblStartDate"></span>
						</div>
						<div class="col-md-4" >
							<select id="ddlTime" name="ddlTime" class="form-control-custom" >
								<option value="08:00">08:00</option>
								<option value="09:00">09:00</option>
								<option value="10:00">10:00</option>
								<option value="11:00">11:00</option>
								<option value="12:00">12:00</option>
								<option value="13:00">13:00</option>
								<option value="14:00">14:00</option>
								<option value="15:00">15:00</option>
								<option value="16:00">16:00</option>
								<option value="17:00">17:00</option>
								<option value="18:00">18:00</option>
							</select>
						</div>
					</div>
					<br />
					<div class="row" >
						<div class="col-md-3 labelColumn" >
							Nama:
						</div>
						<div class="col-md-9">
							<input type="text" placeholder="Nama" required id="txtPatientName" name="txtPatientName" class="form-control-custom" />
						</div>
					</div>
					<br /><div class="row" >
						<div class="col-md-3 labelColumn" >
							No HP:
						</div>
						<div class="col-md-9">
							<input type="text" placeholder="No HP" required id="txtPhone" name="txtPhone" class="form-control-custom" />
						</div>
					</div>
					<br />
				</form>
			</div>
			
			<div id="dialog-confirm" title="Konfirmasi" style="display: none;">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
			</div>
		</div>
		<script>
			function dialogSchedule() {
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
												url: "./Transaction/Calendar/Insert.php",
												type: "POST",
												data: $("#ScheduleForm").serialize(),
												dataType: "json",
												success: function(data) {
													$("#loading").hide();
													if(data.FailedFlag == '0') {
														$.notify(data.Message, "success");
														$("#txtPatientName").val("");
														$("#txtPhone").val("");
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
			
			$(document).ready(function() {
				$('#calendar').fullCalendar({
					header: {
						left: 'prev,next today, prevYear nextYear',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},
					eventClick: function(calEvent, jsEvent, view) {

						/*alert('Event: ' + calEvent.patientid);
						alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
						alert('View: ' + view.name);*/

						// change the border color just for fun
						//$(this).css('border-color', 'red');

					},
					navLinks: true, // can click day/week names to navigate views
					selectable: true,
					selectHelper: true,
					fixedWeekCount: false,
					height: 540,
					eventStartEditable: false,
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
						$('#calendar').fullCalendar('clientEvents', function(event) {
							if(moment(date).format('YYYY-MM-DD') == moment(event.start._i).format('YYYY-MM-DD') && count == 0) {
								count++;
								if(event.isavailable[0] == "0") {
									$.notify("Jadwal Penuh!", "error");
									return false;
								}
								else {
									dialogSchedule();
								}
							}
						});
						if(count == 0) dialogSchedule();
						var startDate = new Date(date);
						var scheduledDate = startDate.getFullYear().toString() + "-" + (startDate.getMonth() + 1).toString() + "-" + startDate.getDate().toString();
						$("#lblStartDate").html(startDate.getDate().toString() + "-" + (startDate.getMonth() + 1).toString() + "-" + startDate.getFullYear().toString());
						$("#hdnStartDate").val(scheduledDate);
					},
					editable: true,
					eventLimit: true, // allow "more" link when too many events
					events: {
					url: "./Transaction/Calendar/DataSource.php",
						error: function() {
							$('#script-warning').show();
						}
					},
					timeFormat: 'H(:mm)',
					businessHours: false
				});
			});
		</script>
	</body>
</html>
