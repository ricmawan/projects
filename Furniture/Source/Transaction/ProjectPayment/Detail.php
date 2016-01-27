<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$ProjectID = mysql_real_escape_string($_GET['ID']);
		$ProjectName = "";
		$Remarks = "";
		$IsEdit = 0;
		$rowCount = 0;
		$Data = "";
		if($ProjectID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						ProjectID,
						ProjectName,
						Remarks,
						IsDone					
					FROM
						master_project
					WHERE
						ProjectID = $ProjectID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$ProjectId = $row['ProjectID'];
			$ProjectName = $row['ProjectName'];
			$Remarks = $row['Remarks'];
			$IsDone = $row['IsDone'];
			
			$sql = "SELECT
						ProjectPaymentID,
						DATE_FORMAT(ProjectTransactionDate, '%d-%m-%Y') AS ProjectTransactionDate,
						Remarks,
						Amount
					FROM
						transaction_projectpayment
					WHERE
						ProjectID = $ProjectID";
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
					array_push($Data, "'".$row['ProjectPaymentID']."', '".$row['ProjectTransactionDate']."', '".$row['Remarks']."', '".$row['Amount']."'");
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
			#txtRemarks {
				height: 100px;
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
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2>Pembayaran Proyek</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >	
							<div class="row">
								<div class="col-md-5">
									Nama Proyek: <br />
									<input id="txtProjectName" style="height:34px;" name="txtProjectName" type="text" class="form-control" placeholder="Nama Proyek" readonly <?php echo 'value="'.$ProjectName.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
								</div>
							</div>
							<br />
							<div style="max-height: 335px !important; height:100%; overflow-y: auto;">
								<div class="col-md-10">
									<table class="table" id="datainput">
										<thead>
											<td>No</td>
											<td>Tanggal</td>
											<td>Keterangan</td>
											<td>Jumlah</td>
										</thead>
										<tbody>
											<tr id='' style='display:none;' class="num">
												<td id="nota" name="nota" class="nota"></td>
												<td>
													<input type="hidden" id="hdnProjectPaymentID" class="hdnProjectPaymentID" name="hdnProjectPaymentID" value="0" />
													<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control DatePickerMonthYearGlobal txtTransactionDate" placeholder="Tanggal" />
												</td>
												<td>
													<input type="text" id="txtTransactionRemarks" name="txtTransactionRemarks" class="form-control txtTransactionRemarks" placeholder="Keterangan"/>
												</td>
												<td>
													<input type="text" id="txtTransactionAmount" name="txtTransactionAmount" class="form-control txtTransactionAmount" onchange="Calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="text-align:right;" value="0.00" placeholder="Jumlah" />
												</td>
												<td style="vertical-align:middle;">
													<i class="fa fa-close btnDelete" style="cursor:pointer;" acronym title="Hapus Data" onclick="DeleteRow(this.getAttribute('row'))"></i>
												</td>
											</tr>
											<tr id="num1" name="num1" class="num">
												<td id="nota1" name="nota1" class="nota">1</td>
												<td>
													<input type="hidden" id="hdnProjectPaymentID1" class="hdnProjectPaymentID" name="hdnProjectPaymentID1" value="0" />
													<input id="txtTransactionDate1" name="txtTransactionDate1" type="text" class="form-control DatePickerMonthYearGlobal txtTransactionDate" placeholder="Tanggal" required />
												</td>
												<td>
													<input type="text" id="txtTransactionRemarks1" name="txtTransactionRemarks1" class="form-control txtTransactionRemarks" placeholder="Keterangan" required />
												</td>
												<td>
													<input type="text" id="txtTransactionAmount1" name="txtTransactionAmount1" class="form-control txtTransactionAmount" placeholder="Jumlah" onchange="Calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="text-align:right;" value="0.00" />
												</td>
												<td style="vertical-align:middle;">
													<i class="fa fa-close btnDelete" style="cursor:pointer;" acronym title="Hapus Data" onclick="DeleteRow(this.getAttribute('row'))" row=1></i>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<input type="hidden" id="hdnProjectID" name="hdnProjectID"  <?php echo 'value="'.$ProjectID.'"'; ?> />
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
							<div class="col-md-10">
								<div class="col-md-4">
									<button class="btn btn-primary" id="btnAdd"><i class="fa fa-plus "></i> Tambah</button>&nbsp;&nbsp;
									<button class="btn btn-default" id="btnSave"  onclick="SubmitForm('./Transaction/ProjectPayment/Insert.php');" ><i class="fa fa-save "></i> Simpan</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function Calculate() {
				var total = 0;
				$(".txtTransactionAmount").each(function() {
					total += parseFloat($(this).val().replace(/\,/g, ""));
				});
				$("#txtGrandTotal").val(returnRupiah(total.toString()));
			}
			function DeleteRow(row) {
				var count = $("#datainput tbody tr").length - 1;
				$("#num" + row).remove();
				$("#recordnew").val(count-1);
				RegenerateRowNumber();
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
				$(".hdnProjectPaymentID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnProjectPaymentID" + i);
						$(this).attr("name", "hdnProjectPaymentID" + i);
					}
					i++;
				});
				i = 0;
				$(".txtTransactionDate").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtTransactionDate" + i);
						$(this).attr("name", "txtTransactionDate" + i);
					}
					i++;
				});
				i = 0;
				$(".txtTransactionRemarks").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtTransactionRemarks" + i);
						$(this).attr("name", "txtTransactionRemarks" + i);
					}
					i++;
				});
				i = 0;
				$(".txtTransactionAmount").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtTransactionAmount" + i);
						$(this).attr("name", "txtTransactionAmount" + i);
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
			$(document).ready(function () {
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
						$("#hdnProjectPaymentID" + count).val(d[0].replace("'", ""));
						$("#txtTransactionDate" + count).val(d[1].replace("'", ""));
						$("#txtTransactionRemarks" + count).val(d[2].replace("'", ""));
						$("#txtTransactionAmount" + count).val(returnRupiah(d[3].replace("'", "")));
						$("#record").val(count);
						$("#recordnew").val(count);
					}
					Calculate();
				}
			});
		</script>
	</body>
</html>
