<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$OutgoingInventoryID = mysql_real_escape_string($_GET['ID']);
		$TransactionDate = "";
		$Remarks = "";
		$IsEdit = 0;
		$rowCount = 0;
		$Data = "";
		if($OutgoingInventoryID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						OI.OutgoingInventoryID,
						DATE_FORMAT(OI.TransactionDate, '%d-%m-%Y') AS TransactionDate,
						OI.Remarks
					FROM
						transaction_outgoinginventory OI
					WHERE
						OI.OutgoingInventoryID = $OutgoingInventoryID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$TransactionDate = $row['TransactionDate'];
			$Remarks = $row['Remarks'];
			
			$sql = "SELECT
						OID.OutgoingInventoryDetailsID,
						OID.InventoryID,
						MI.InventoryName,
						OID.Quantity,
						OID.Quantity + IFNULL(II.Quantity, 0) - IFNULL(OI.Quantity, 0) Stock,
						OID.Remarks
					FROM
						transaction_outgoinginventorydetails OID
						JOIN master_inventory MI
							ON MI.InventoryID = OID.InventoryID
						LEFT JOIN
						(
							SELECT
								IID.InventoryID
								SUM(IID.Quantity) Quantity
							FROM
								transaction_incominginventorydetails IID
							GROUP BY
								IID.InventoryID
 						)II
							ON II.InventoryID = MI.InventoryID
						LEFT JOIN
						(
							SELECT
								OID.InventoryID,
								SUM(OID.Quantity) Quantity
							FROM
								transaction_outgoinginventorydetails OID
							GROUP BY
								OID.InventoryID
						)OI
							ON OI.InventoryID = MI.InventoryID
					WHERE
						OID.OutgoingInventoryID = $OutgoingInventoryID";
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
					array_push($Data, "'".$row['OutgoingInventoryDetailsID']."', '".$row['InventoryID']."', '".$row['InventoryName']."', '".$row['Quantity']."', '".$row['Remarks']."', '".$row['Stock']."'");
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Pemakaian Inventaris</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-1 labelColumn">
									Tanggal :
									<input id="hdnOutgoingInventoryID" name="hdnOutgoingInventoryID" type="hidden" <?php echo 'value="'.$OutgoingInventoryID.'"'; ?> />
									<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowCount.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
								<div class="col-md-1 labelColumn">
									Barang :
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlInventory" id="ddlInventory" class="form-control ddlInventory" placeholder="Pilih Barang" >
											<option value="" selected> </option>
											<?php
												$sql = "SELECT InventoryID, InventoryName FROM master_inventory";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													echo "<option value='".$row['InventoryID']."' >".$row['InventoryName']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<table class="table" style="width:auto;" id="datainput">
										<thead style="background-color: black;color:white;height:25px;width:500px;display:block;">
											<td align="center" style="width:30px;">No</td>
											<td align="center" style="width:180px;">Inventaris</td>
											<td align="center" style="width:75px;">Qty</td>
											<td align="center" style="width:180px;">Keterangan</td>
											<td style="width: 26px"></td>
										</thead>
										<tbody style="display:block;max-height:282px;height:100%;overflow-y:auto;">
											<tr id='' style='display:none;' class="num">
												<td id='nota' name='nota' class='nota' style="width:30px;vertical-align:middle;"></td>
												<td style="width:180px;">
													<input type="text" id="txtInventoryName" readonly name="txtInventoryName" class="txtInventoryName form-control-custom placeholder" />
													<input type="hidden" id="hdnInventoryID" name="hdnInventoryID" class="hdnInventoryID" />
													<input type="hidden" id="hdnStock" name="hdnStock" class="hdnStock" value="" />
													<input type="hidden" id="hdnOutgoingInventoryDetailsID" class="hdnOutgoingInventoryDetailsID" name="hdnOutgoingInventoryDetailsID" value="0" />
												</td>
												<td style="width:75px;">
													<input type="text" row="" value=1 id="txtQuantity" style="text-align:right;" name="txtQuantity" onkeypress="return isNumberKey(event)" onchange="ValidateQty();" class="form-control-custom txtQuantity" placeholder="QTY" />
												</td>
												<td  style="width:180px;">
													<input type="text" id="txtRemarksDetail" name="txtRemarksDetail" class="form-control-custom txtRemarksDetail" placeholder="Keterangan" />
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
								<div class="col-md-1">
									Keterangan :
								</div>
								<div class="col-md-3">
									<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" placeholder="Catatan"><?php echo $Remarks; ?></textarea>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<button class="btn btn-primary" id="btnAdd" type="button"><i class="fa fa-plus"></i> Tambah</button>&nbsp;&nbsp;
									<button class="btn btn-default" id="btnSave" type="button" onclick="SubmitForm('./Transaction/OutgoingInventory/Insert.php');" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
								</div>
							</div>
						</form>
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
			
			function ValidateQty() {
				
			}
			
			function FillInventoryID() {
				var AddFlag = 1;
				var CurrentInventoryID = $("#ddlInventory").val();
				var CurrentInventoryName = $("#ddlInventory option:selected").text();
				var rows = $("#recordnew").val();
				//QTY + 1 if selected item already exists
				for(var i=1;i<=rows;i++) {
					if($("#hdnInventoryID" + i).val() == CurrentInventoryID) {
						$("#txtQuantity" + i).val((parseInt($("#txtQuantity" + i).val()) + 1));
						AddFlag = 0;
					}
				}
				
				if(AddFlag == 1) {
					$("#btnAdd").click();
					var rows = $("#recordnew").val();
					$("#hdnInventoryID" + rows).val(CurrentInventoryID);
					$("#txtInventoryName" + rows).val(CurrentInventoryName);
				}
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
				$(".hdnOutgoingInventoryDetailsID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnOutgoingInventoryDetailsID" + i);
						$(this).attr("name", "hdnOutgoingInventoryDetailsID" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnStock").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnStock" + i);
						$(this).attr("name", "hdnStock" + i);
					}
					i++;
				});
				i = 0;
				$(".txtInventoryName").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtInventoryName" + i);
						$(this).attr("name", "txtInventoryName" + i);
						$(this).attr("row", i);
					}
					i++;
				});
				i = 0;
				$(".txtRemarksDetail").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtRemarksDetail" + i);
						$(this).attr("name", "txtRemarksDetail" + i);
						$(this).attr("row", i);
					}
					i++;
				});
				i = 0;
				$(".txtQuantity").each(function() {
					if(i != 0) {
						$(this).attr("id", "txtQuantity" + i);
						$(this).attr("name", "txtQuantity" + i);
					}
					i++;
				});
				i = 0;
				$(".hdnInventoryID").each(function() {
					if(i != 0) {
						$(this).attr("id", "hdnInventoryID" + i);
						$(this).attr("name", "hdnInventoryID" + i);
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
				$("#ddlInventory").combobox({
					select: function( event, ui ) {
						FillInventoryID();
						setTimeout(function() {
							$("#ddlInventory").next().find("input").val("");
							$("#ddlInventory").val("");
						}, 0);
					}
				});
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
					$("#txtRemarksDetail" + count).removeAttr("required");
					$("#recordnew").val(count);
					$("#datainput tbody").animate({
						scrollTop: (25 * count)
					}, "slow");
				});
				
				if(parseInt($("#hdnRow").val()) > 0) {
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
						$("#hdnOutgoingInventoryDetailsID" + count).val(d[0].replace("'", ""));
						$("#hdnInventoryID" + count).val(d[1].replace("'", ""));
						$("#txtInventoryName" + count).val(d[2].replace("'", ""));
						$("#txtQuantity" + count).val(d[3].replace("'", ""));
						$("#txtRemarksDetail" + count).val(d[4].replace("'", ""));
						$("#hdnStock" + count).val(d[5].replace("'", ""));
						$("#record").val(count);
						$("#recordnew").val(count);
					}
				}
			});
		</script>
	</body>
</html>
