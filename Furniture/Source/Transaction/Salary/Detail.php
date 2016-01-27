<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		date_default_timezone_set("Asia/Jakarta");
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$SalaryID = mysql_real_escape_string($_GET['ID']);
		$PeriodID = 0;
		$SalaryDate = "";		
		$IsEdit = 0;
		$rowCount = 0;
		$Date =  date("d-m-Y");
		$Data = "";
		if($SalaryID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						SalaryID,
						PeriodID,
						DATE_FORMAT(SalaryDate, '%d-%m-%Y') AS SalaryDate
					FROM
						transaction_salary
					WHERE
						SalaryID = $SalaryID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$SalaryId = $row['SalaryID'];
			$PeriodID = $row['PeriodID'];
			$SalaryDate = $row['SalaryDate'];
			
			$sql = "SELECT
						SD.SalaryDetailsID,
						SD.ProjectID,
						SD.EmployeeID,
						SD.Remarks,
						SD.DailySalary,
						SD.Days
					FROM
						transaction_salarydetails SD
					WHERE
						SD.SalaryID = $SalaryID";
			if(!$result = mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$rowCount = mysql_num_rows($result);
			if($rowCount > 0) {
				//$DetailID = array();
				$Data = array();
				while($row = mysql_fetch_array($result)) {
					//array_push($DetailID, $row[0]);
					array_push($Data, "'".$row['SalaryDetailsID']."', '".$row['ProjectID']."', '".$row['EmployeeID']."', '".$row['Remarks']."', '".$row['DailySalary']."', '".$row['Days']."'");
				}
				//$DetailID = implode(",", $DetailID);
				$Data = implode("|", $Data);
			}
			else {
				//$DetailID = "";
				$Data = "";
			}
		}
	}
?>
<html>
	<head>
		<style>
			.custom-combobox {
				position: relative;
				display: inline-block;
				width: 100%;
			}
			.custom-combobox-input {
				margin: 0;
				padding: 5px 10px;
				display: block;
				width: 100%;
				height: 34px;
				padding: 6px 12px;
				font-size: 14px;
				line-height: 1.42857143;
				color: #555;
				background-color: #fff;
				background-image: none;
				border: 1px solid #ccc;
				border-radius: 4px;
				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
				-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
				transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			}
			.ui-autocomplete {
				font-family: Open Sans, sans-serif; 
				font-size: 14px;
			}
			.caret {
				display: inline-block;
				width: 0;
				height: 0;
				margin-left: 2px;
				vertical-align: middle;
				border-top: 4px solid;
				border-right: 4px solid transparent;
				border-left: 4px solid transparent;
				right: 10px;
				top: 50%;
				position: absolute;
			}
			.table > thead > tr > th,
			.table > tbody > tr > th,
			.table > tfoot > tr > th,
			.table > thead > tr > td,
			.table > tbody > tr > td,
			.table > tfoot > tr > td {
				padding: 2px 8px 2px 8px;
				line-height: 1.42857143;
				vertical-align: top;
				border-top: 1px solid #ddd;
			}			
			.form-control {
				height: 24px;
			}
			.custom-combobox-input {
				height: 24px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2>Gaji Karyawan</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-5">
									Periode: <br />
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlPeriod" id="ddlPeriod" class="form-control" placeholder="Pilih Periode" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT PeriodID, CONCAT(DATE_FORMAT(StartDate, '%d %M %Y'), ' - ', DATE_FORMAT(EndDate, '%d %M %Y')) AS PeriodRange FROM master_period";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													if($PeriodID == $row['PeriodID']) echo "<option selected value='".$row['PeriodID']."' >".$row['PeriodRange']."</option>";
													else echo "<option value='".$row['PeriodID']."' >".$row['PeriodRange']."</option>";
													
												}
											?>
										</select>
									</div>
									<input id="hdnSalaryID" name="hdnSalaryID" type="hidden" <?php echo 'value="'.$SalaryID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-5">
									Tanggal:<br />
									<input id="txtSalaryDate" style="height:34px;" name="txtSalaryDate" type="text" class="form-control DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$SalaryDate.'"'; ?>/>
								</div>
							</div>
							<br />
							<div style="max-height: 320px !important; height:100%; overflow-y: auto;">
								<div class="col-md-12">					
									<table class="table" id="datainput">
										<thead>
											<td>No</td>
											<td>Proyek</td>
											<td>Nama Karyawan</td>
											<td>Gaji/hari</td>
											<td>Hari</td>
											<td>Keterangan</td>
											<td>Total</td>
										</thead>
										<tbody>
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota'></td>
												<td>
													<input type="hidden" id="hdnSalaryDetailsID" class="hdnSalaryDetailsID" name="hdnSalaryDetailsID" value="0" />
													<div class="ui-widget" style="width: 100%;">
														<select name="ddlProject" id="ddlProject" class="form-control ddlProject" placeholder="Pilih Proyek" >
															<option value="" selected> </option>
															<?php
																$sql = "SELECT ProjectID, ProjectName FROM master_project WHERE IsDone = 0";
																if(!$result = mysql_query($sql, $dbh)) {
																	echo mysql_error();
																	return 0;
																}
																while($row = mysql_fetch_array($result)) {
																	echo "<option value='".$row['ProjectID']."' >".$row['ProjectName']."</option>";
																}
															?>
														</select>
													</div>
												</td>
												<td>
													<div class="ui-widget" style="width: 100%;">
														<select name="ddlEmployee" id="ddlEmployee" class="form-control ddlEmployee" placeholder="Pilih Karyawan" onchange="BindSalary()" >
															<option value="" selected> </option>
															<?php
																$sql = "SELECT EmployeeID, EmployeeName, DailySalary FROM master_employee";
																if(!$result = mysql_query($sql, $dbh)) {
																	echo mysql_error();
																	return 0;
																}
																while($row = mysql_fetch_array($result)) {
																	echo "<option value='".$row['EmployeeID']."' salary='".$row['DailySalary']."' >".$row['EmployeeName']."</option>";
																}
															?>
														</select>
													</div>
												</td>
												<td>
													<input type="text" row="" id="txtDailySalary" name="txtDailySalary" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="text-align:right;" value="0.00" class="form-control txtDailySalary" placeholder="Gaji/hari"/>
												</td>
												<td>
													<input type="text" value=1 id="txtDays" style="width:75px;" class="form-control txtDays" name="txtDays" onkeypress="return isNumberKey(event)" onchange="Calculate();" placeholder="Hari" />
												</td>
												<td>
													<input type="text" id="txtRemarks" name="txtRemarks" class="form-control txtRemarks" placeholder="Keterangan"/>
												</td>
												<td>
													<input type="text" id="txtTotal" name="txtTotal" class="form-control txtTotal" style="text-align:right;" value="0.00" placeholder="Jumlah" readonly />
												</td>
												<td style="vertical-align:middle;">
													<i class="fa fa-close btnDelete" style="cursor:pointer;" acronym title="Hapus Data" onclick="DeleteRow(this.getAttribute('row'))"></i>
												</td>
											</tr>
											<tr id="num1" name="num1" class="num">
												<td id='nota1' name='nota1' class='nota'>1</td>
												<td>
													<input type="hidden" id="hdnSalaryDetailsID1" class="hdnSalaryDetailsID" name="hdnSalaryDetailsID1" value="0" />
													<div class="ui-widget" style="width: 100%;">
														<select name="ddlProject1" id="ddlProject1" class="form-control ddlProject" placeholder="Pilih Proyek" >
															<option value="" selected> </option>
															<?php
																$sql = "SELECT ProjectID, ProjectName FROM master_project WHERE IsDone = 0";
																if(!$result = mysql_query($sql, $dbh)) {
																	echo mysql_error();
																	return 0;
																}
																while($row = mysql_fetch_array($result)) {
																	echo "<option value='".$row['ProjectID']."' >".$row['ProjectName']."</option>";
																}
															?>
														</select>
													</div>
												</td>
												<td>
													<div class="ui-widget" style="width: 100%;">
														<select name="ddlEmployee1" id="ddlEmployee1" class="form-control ddlEmployee" placeholder="Pilih Karyawan" onchange="BindSalary()"; >
															<option value="" selected> </option>
															<?php
																$sql = "SELECT EmployeeID, EmployeeName, DailySalary FROM master_employee";
																if(!$result = mysql_query($sql, $dbh)) {
																	echo mysql_error();
																	return 0;
																}
																while($row = mysql_fetch_array($result)) {
																	echo "<option value='".$row['EmployeeID']."' salary='".$row['DailySalary']."' >".$row['EmployeeName']."</option>";
																}
															?>
														</select>
													</div>
												</td>
												<td>
													<input type="text" row="1" id="txtDailySalary1" name="txtDailySalary1" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="text-align:right;" value="0.00" class="form-control txtDailySalary" placeholder="Gaji/hari"/>
												</td>
												<td>
													<input type="text" value=1 id="txtDays1" style="width:75px;" class="form-control txtDays" name="txtDays1" onkeypress="return isNumberKey(event)" onchange="Calculate();" placeholder="Hari" />
												</td>
												<td>
													<input type="text" id="txtRemarks1" name="txtRemarks1" class="form-control txtRemarks" placeholder="Keterangan"/>
												</td>
												<td>
													<input type="text" id="txtTotal1" name="txtTotal1" class="form-control txtTotal" style="text-align:right;" value="0.00" placeholder="Jumlah" readonly />
												</td>
												<td style="vertical-align:middle;">
													<i class="fa fa-close btnDelete" style="cursor:pointer;" acronym title="Hapus Data" onclick="DeleteRow(this.getAttribute('row'))"></i>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<input type="hidden" id="record" name="record" value=1 />
							<input type="hidden" id="recordnew" name="recordnew" value=1 />
						</form>
						<br />
						<div class="row">
							<div class="col-md-2" style="text-align:right;">
								Grand Total :
							</div>
							<div class="col-md-2">
								<input type="text" id="txtGrandTotal" style="text-align:right;" value="0.00" name="txtGrandTotal" class="form-control" readonly />
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-default" id="btnAdd" ><i class="fa fa-save "></i> Add</button>&nbsp;&nbsp;
								<button class="btn btn-default" id="btnSave"  onclick="SubmitValidate('./Transaction/Salary/Insert.php');" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function DeleteRow(row) {
				var count = $("#datainput tbody tr").length - 1;
				$("#num" + row).remove();
				$("#recordnew").val(count-1);
				RegenerateRowNumber();
				Calculate();
			}
			function BindSalary(row) {
				var currentSalary = $("#ddlEmployee" + row + " option:selected").attr("salary");
				$("#txtDailySalary" +  row).val(returnRupiah(currentSalary.toString()));
				Calculate();
			}
			function RegenerateRowNumber() {
				var i = 0;
				$(".nota").each(function() {
					if(i != 0) {
						$(this).html(i);
						$(this).attr("id", "nota" + i);
						$(this).attr("name", "nota" + i);
					}
					i++;
				});
				i = 0;
				$(".num").each(function() {
					if(i != 0) {
						$(this).attr("id", "num" + i);
						$(this).attr("name", "num" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnSalaryDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnSalaryDetailsID" + i);
						$(this).attr("name", "hdnSalaryDetailsID" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnProjectID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnProjectID" + i);
						$(this).attr("name", "hdnProjectID" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnEmployeeID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnEmployeeID" + i);
						$(this).attr("name", "hdnEmployeeID" + i);
					}
					i++;
				});
				i = 0;
				$(".ddlProject").each(function() {
					if(i != 0) {
						$(this).attr("id", "ddlProject" + i);
						$(this).attr("name", "ddlProject" + i);
					}
					i++;
				});
				i = 0;
				$(".ddlEmployee").each(function() {
					if(i != 0) {
						$(this).attr("id", "ddlEmployee" + i);
						$(this).attr("name", "ddlEmployee" + i);
					}
					i++;
				});
				i = 0;
				$(".txtDailySalary").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtDailySalary" + i);
						$(this).attr("name", "txtDailySalary" + i);
						$(this).attr("row", i);
					}
					i++;
				});
				i = 0;
				$(".txtDays").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtDays" + i);
						$(this).attr("name", "txtDays" + i);
					}
					i++;
				});
				i = 0;
				$(".txtRemarks").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtRemarks" + i);
						$(this).attr("name", "txtRemarks" + i);
					}
					i++;
				});
				i = 0;
				$(".txtTotal").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtTotal" + i);
						$(this).attr("name", "txtTotal" + i);
					}
					i++;
				});
				i = 0;
				$(".btnDelete").each(function() {
					if(i != 0) {
						$(this).attr("row", i);
					}
					i++;
				});
			}
			function Calculate() {
				var Total = 0;
				GrandTotal = 0;
				var row = 0;
				var dailySalary = 0;
				var days = 1;
				var i = 0;
				$(".txtDailySalary").each(function() {
					if(i != 0) {
						dailySalary = $(this).val().replace(/\,/g, "");
						row = $(this).attr("row");
						days = $("#txtDays" + row).val();
						GrandTotal += parseFloat(dailySalary) * parseFloat(days);
						Total = parseFloat(dailySalary) * parseFloat(days);
						$("#txtTotal" + row).val(returnRupiah(Total.toString()));
					}
					i++;
				});
				$("#txtGrandTotal").val(returnRupiah(GrandTotal.toString()));
			}
			function SubmitValidate(url) {
				var PassValidate = 1;
				var FirstFocus = 0;
				if($("#ddlPeriod").val() == "") {
					PassValidate = 0;
					$("#ddlPeriod").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#ddlPeriod").next().find("input").focus();
					FirstFocus = 1;
				}
				if($("#recordnew").val() > 0) {
					for(var i=1;i<=$("#recordnew").val();i++) 
					{
						if($("#ddlProject" + i).val() == "") {
							PassValidate = 0;
							$("#ddlProject" + i).next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $("#ddlPeriod").next().find("input").focus();
							FirstFocus = 1;
						}
						
						if($("#ddlEmployee" + i).val() == "") {
							PassValidate = 0;
							$("#ddlEmployee" + i).next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $("#ddlPeriod").next().find("input").focus();
							FirstFocus = 1;
						}
					}
					if(PassValidate == 0) {
						$("html, body").animate({
							scrollTop: 0
						}, "slow");
						return false;
					}
					else SubmitForm(url);
				}
			}
			$(document).ready(function () {
				$("#ddlEmployee1").combobox({
					select: function( event, ui ) {
						BindSalary(1);						
					}
				});
				$("#ddlPeriod").combobox();
				$("#ddlPeriod").next().find("input").css("height", "34px");
				$("#ddlProject1").combobox();
				$("#btnAdd").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					count++;
					var $clone = $("#datainput tbody tr:first").clone();
					$clone.find("#nota").text(count);
					$clone.find("#nota").attr("id", "nota" + count);
					$clone.find("#nota").attr("name", "nota" + count);
					$clone.removeAttr("style");
					$clone.attr({
						id: "num" + count,
						name: "num" + count

					});
					$clone.find("input, select, i").each(function(){
						//var temp = $(this).attr("id") + (count - 1);
						$(this).attr({
							id: $(this).attr("id") + count,
							name: $(this).attr("name") + count,
							row: count,
							required: ""
						});				
						//$(this).val($("#" + temp).val());
					});
					$("#datainput tbody").append($clone);
					$("#txtRemarks" + count).removeAttr("required");
					$("#ddlEmployee" + count).combobox({
						select: function( event, ui ) {
							BindSalary(count);						
						}
					});
					$("#ddlProject" + count).combobox();
					//$("#txtQuantity" + count).addClass("txtQuantity");
					$("#recordnew").val(count);
				});
								
				if(parseInt($("#hdnRow").val()) > 0) {
					var data = $("#hdnData").val();
					var item = data.split("|");
					var row = item.length;
					var count = 0;
					$('#datainput tbody:last > tr:not(:first)').remove();
					for(var i=0; i<row; i++) {
						$("#btnAdd").click();
						count++;
						//set values
						var d = item[i].split("', '");
						$("#nota").text(count);
						$("#hdnSalaryDetailsID" + count).val(d[0].replace("'", ""));
						$("#txtRemarks" + count).val(d[3].replace("'", ""));
						$("#txtDailySalary" + count).val(returnRupiah(d[4].replace("'", "")));
						$("#txtDays" + count).val(d[5].replace("'", ""));
						$("#ddlProject" + count).combobox("destroy");
						$("#ddlProject" + count).val(d[1].replace("'", ""));
						$("#ddlProject" + count).combobox();
						
						$("#ddlEmployee" + count).combobox("destroy");
						$("#ddlEmployee" + count).val(d[2].replace("'", ""));
						$("#ddlEmployee" + count).combobox({
							select: function( event, ui ) {
								BindSalary(count);						
							}
						});
						$("#record").val(count);
						$("#recordnew").val(count);
					}
					Calculate();
				}
			});
		</script>
	</body>
</html>
