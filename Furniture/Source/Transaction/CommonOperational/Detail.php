<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$CommonOperationalID = mysql_real_escape_string($_GET['ID']);
		$SupplierID = "";
		$TransactionDate = "";
		$IsEdit = 0;
		$rowCount = 0;
		$Data = "";
		if($CommonOperationalID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						CO.CommonOperationalID,
						DATE_FORMAT(CO.CommonOperationalDate, '%d-%m-%Y') AS TransactionDate
					FROM
						transaction_commonoperational CO
					WHERE
						CO.CommonOperationalID = $CommonOperationalID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$CommonOperationalID = $row['CommonOperationalID'];
			$TransactionDate = $row['TransactionDate'];
			
			$sql = "SELECT
						COD.CommonOperationalDetailsID,
						COD.Remarks,
						COD.Amount
					FROM
						transaction_commonoperationaldetails COD
					WHERE
						COD.CommonOperationalID = $CommonOperationalID";
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
					array_push($Data, "'".$row['CommonOperationalDetailsID']."', '".$row['Remarks']."', '".$row['Amount']."'" );
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
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Barang Masuk</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-5">
									Tanggal:<br />
									<input id="hdnCommonOperationalID" name="hdnCommonOperationalID" type="hidden" <?php echo 'value="'.$CommonOperationalID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
									<input id="txtTransactionDate" style="height:34px;" name="txtTransactionDate" type="text" class="form-control DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
							</div>
							<br />
							<div style="max-height: 335px !important; height:100%; overflow-y: auto;">
								<div class="col-md-10">					
									<table class="table" id="datainput">
										<thead>
											<td>No</td>
											<td>Keterangan</td>
											<td>Total</td>
										</thead>
										<tbody>
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota'></td>
												<td>
													<input type="text" id="txtRemarks" name="txtRemarks" class="form-control txtRemarks" placeholder="Keterangan" />
													<input type="hidden" id="hdnCommonOperationalDetailsID" class="hdnCommonOperationalDetailsID" name="hdnCommonOperationalDetailsID" value="0" />
												</td>
												<td>
													<input type="text" id="txtTotal" name="txtTotal" class="form-control txtTotal" onchange="Calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="text-align:right;" value="0.00" placeholder="Total" />
												</td>
												<td style="vertical-align:middle;">
													<i class="fa fa-close btnDelete" style="cursor:pointer;" acronym title="Hapus Data" onclick="DeleteRow(this.getAttribute('row'))"></i>
												</td>
											</tr>
											<tr id="num1" name="num1" class="num">
												<td id='nota1' name='nota1' class='nota'>1</td>
												<td>
													<input type="text" id="txtRemarks1" name="txtRemarks1" class="form-control txtRemarks" placeholder="Keterangan" />
													<input type="hidden" id="hdnCommonOperationalDetailsID1" class="hdnCommonOperationalDetailsID" name="hdnCommonOperationalDetailsID1" value="0" />
												</td>
												<td>
													<input type="text" id="txtTotal1" name="txtTotal1" class="form-control txtTotal" onchange="Calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="text-align:right;" value="0.00" placeholder="Total" />
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
								<button class="btn btn-primary" id="btnAdd"><i class="fa fa-plus "></i> Tambah</button>&nbsp;&nbsp;
								<button class="btn btn-default" id="btnSave"  onclick="SubmitForm('./Transaction/CommonOperational/Insert.php');" ><i class="fa fa-save "></i> Simpan</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function Calculate() {
				var total = 0;
				$(".txtTotal").each(function() {
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
				$(".hdnCommonOperationalDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnCommonOperationalDetailsID" + i);
						$(this).attr("name", "hdnCommonOperationalDetailsID" + i);
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
						$("#hdnCommonOperationalDetailsID" + count).val(d[0].replace("'", ""));
						$("#txtRemarks" + count).val(d[1].replace("'", ""));
						$("#txtTotal" + count).val(returnRupiah(d[2].replace("'", "")));
						$("#record").val(count);
						$("#recordnew").val(count);
					}
					Calculate();
				}
			});
		</script>
	</body>
</html>
