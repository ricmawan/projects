<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$OperationalID = mysql_real_escape_string($_GET['ID']);
		$TransactionDate = "";
		$Remarks = "";
		$IsEdit = 0;
		$rowCount = 0;
		$Data = "";
		if($OperationalID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						OP.OperationalID,
						OP.Remarks,
						DATE_FORMAT(OP.TransactionDate, '%d-%m-%Y') AS TransactionDate
					FROM
						transaction_operational OP
					WHERE
						OP.OperationalID = $OperationalID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$OperationalID = $row['OperationalID'];
			$Remarks = $row['Remarks'];
			$TransactionDate = $row['TransactionDate'];
			
			$sql = "SELECT
						OPD.OperationalDetailsID,
						OPD.Remarks,
						OPD.Amount
					FROM
						transaction_operationaldetails OPD
					WHERE
						OPD.OperationalID = $OperationalID";
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
					array_push($Data, "'".$row['OperationalDetailsID']."', '".$row['Amount']."', '".$row['Remarks']."'");
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
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Operasional</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-1 labelColumn">
									Tanggal :
									<input id="hdnOperationalID" name="hdnOperationalID" type="hidden" <?php echo 'value="'.$OperationalID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<table class="table" style="width:auto;" id="datainput">
										<thead style="background-color: black;color:white;height:25px;width:706px;display:block;">
											<td align="center" style="width:30px;">No</td>
											<td align="center" style="width:200px;">Keterangan</td>
											<td align="center" style="width:170px;">Jumlah</td>
											<td style="width: 26px"></td>
										</thead>
										<tbody style="display:block;max-height:172px;height:100%;overflow-y:auto;">
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota' style="width:30px;vertical-align:middle;"></td>
												<td  style="width:400px;">
													<input type="hidden" id="hdnOperationalDetailsID" class="hdnOperationalDetailsID" name="hdnOperationalDetailsID" value="0" />
													<input type="text" id="txtRemarksDetail" name="txtRemarksDetail" class="form-control-custom txtRemarksDetail" placeholder="Keterangan" maxlength=50 />
												</td>
												<td style="width:250px;">
													<input type="text" id="txtAmount" value="0.00" name="txtAmount" style="text-align:right;" onclick="this.select();" class="form-control-custom txtAmount" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" placeholder="Total"/>
												</td>
												<td style="vertical-align:middle;">
													<i class="fa fa-close btnDelete" style="cursor:pointer;" acronym title="Hapus Data" onclick="DeleteRow(this.getAttribute('row'))"></i>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<input type="hidden" id="record" name="record" value=0 />
							<input type="hidden" id="recordnew" name="recordnew" value=0 />
							<br />
							<div class="row">
								<div class="col-md-2">
									Grand Total :
								</div>
								<div class="col-md-3">
									<input type="text" id="txtGrandTotal" style="text-align:right;" value="0.00" name="txtGrandTotal" class="form-control-custom" readonly />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">
									Catatan :
								</div>
								<div class="col-md-4">
									<textarea id="txtRemarks" name="txtRemarks" maxlength=70 class="form-control-custom" placeholder="Catatan"><?php echo $Remarks; ?></textarea>
								</div>
							</div>
							<br />
						</form>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-default" id="btnAdd" ><i class="fa fa-plus "></i> Tambah</button>&nbsp;&nbsp;								
								<button class="btn btn-default" id="btnSave"  onclick="SubmitValidate();" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
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
				$(".hdnOperationalDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnOperationalDetailsID" + i);
						$(this).attr("name", "hdnOperationalDetailsID" + i);
					}
					i++;
				});
				i = 0;
				$(".txtAmount").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtAmount" + i);
						$(this).attr("name", "txtAmount" + i);
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
				i = 0;
				$(".txtRemarksDetail").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtRemarksDetail" + i);
						$(this).attr("name", "txtRemarksDetail" + i);
					}
					i++;
				});
			}
			
			function Calculate() {
				var Total = 0;
				GrandTotal = 0;
				var i = 0;
				$(".txtAmount").each(function() {
					if(i != 0) {
						amount = $(this).val().replace(/\,/g, "");
						GrandTotal += parseFloat(amount);
					}
					i++;
				});
				$("#txtGrandTotal").val(returnRupiah(GrandTotal.toFixed(2).toString()));
			}
			
			$(document).ready(function () {
				$("#btnAdd").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					count++;
					//if(count <= 10) {
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
						if($("#hdnIsEdit").val() == 0 ) {
							$("#datainput tbody").animate({
								scrollTop: (25 * count)
							}, "slow");
						}						
					//}
					//else {
						//$.notify("Jumlah barang melebihi maksimal!", "error");
					//}
				});
				$("#btnDelete").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					$('#datainput tr:last').remove();
					$("#recordnew").val(count-1);
					$("#btnAdd").attr("disabled", false)
					Calculate();
				});
				
				if(parseInt($("#hdnRow").val()) > 0) {
					$("#txtTransactionDate").attr("readonly", "readonly");
					var data = $("#hdnData").val();
					var type = data.split("|");
					var row = type.length;
					var count = 0;
					$('#datainput tbody:last > tr:not(:first)').remove();
					for(var i=0; i<row; i++) {
						$("#btnAdd").click();
						count++;
						//set values
						var d = type[i].split("', '");
						$("#nota").text(count);
						$("#hdnOperationalDetailsID" + count).val(d[0].replace("'", ""));
						$("#txtAmount" + count).val(returnRupiah(d[1].replace("'", "")));
						$("#txtRemarksDetail" + count).val(d[2].replace("'", ""));
						
						$("#record").val(count);
						$("#recordnew").val(count);
					}
					Calculate();
				}
			});
			
			var counterInsert = 0;
			function SubmitValidate() {
				if(counterInsert == 0) {
					counterInsert = 1;
					if($("#recordnew").val() > 0) {
						var PassValidate = 1;
						var FirstFocus = 0;
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
						
						if(PassValidate == 0) {
							$("html, body").animate({
								scrollTop: 0
							}, "slow");
							return false;
						}
						else {
							SubmitForm("./Transaction/Operational/Insert.php");
						}
					}
				}
				setTimeout(function() {
					counterInsert = 0;
				}, 1000);
			}
		</script>
	</body>
</html>
