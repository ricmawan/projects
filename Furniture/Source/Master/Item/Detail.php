<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$ItemID = mysql_real_escape_string($_GET['ID']);
		$ItemName = "";
		$CategoryID = 0;
		$UnitID = "";
		$ReminderCount = 0;
		$Price = 0.00;
		$IsEdit = 0;
		
		if($ItemID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
					ItemID,
					ItemName,
					CategoryID,
					UnitID,
					ReminderCount,
					Price
				FROM
					master_item
				WHERE
					ItemID = $ItemID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$ItemId = $row['ItemID'];
			$ItemName = $row['ItemName'];
			$CategoryID = $row['CategoryID'];
			$UnitID = $row['UnitID'];
			$ReminderCount = $row['ReminderCount'];
			$Price = $row['Price'];
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
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Barang</h2>  
					</div>
					<div class="panel-body">
						<form class="col-md-5" id="PostForm" method="POST" action="" >
							Nama Barang:<br />
							<input id="hdnItemID" name="hdnItemID" type="hidden" <?php echo 'value="'.$ItemID.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="hdnUnitID" name="hdnUnitID" type="hidden" <?php echo 'value="'.$UnitID.'"'; ?> />
							<input id="hdnCategoryID" name="hdnCategoryID" type="hidden" <?php echo 'value="'.$CategoryID.'"'; ?> />
							<input id="txtItemName" name="txtItemName" type="text" class="form-control" placeholder="Nama " required <?php echo 'value="'.$ItemName.'"'; ?> />
							<br />
							Kategori Barang:<br />
							<div class="ui-widget" style="width: 100%;">
								<select name="ddlCategory" id="ddlCategory" class="form-control" placeholder="Pilih Kategori Barang" >
									<option value="" selected> </option>
									<?php
										$sql = "SELECT CategoryID, CategoryName FROM master_category";
										if(!$result = mysql_query($sql, $dbh)) {
											echo mysql_error();
											return 0;
										}
										while($row = mysql_fetch_array($result)) {
											echo "<option value='".$row['CategoryID']."' >".$row['CategoryName']."</option>";
										}
									?>
								</select>
							</div>
							<br />
							Satuan:<br />
							<select name="ddlUnit" id="ddlUnit" class="form-control" required >
								<option value="" selected>-pilih satuan-</option>
								<?php
									$sql = "SELECT UnitID, UnitName FROM master_unit";
									if(!$result = mysql_query($sql, $dbh)) {
										echo mysql_error();
										return 0;
									}
									while($row = mysql_fetch_array($result)) {
										echo "<option value='".$row['UnitID']."' >".$row['UnitName']."</option>";
									}
								?>
							</select>
							<br />
							Notifikasi Stok:<br />
							<input id="txtReminderCount" name="txtReminderCount" type="text" class="form-control" placeholder="Notifikasi Stok" required <?php echo 'value="'.$ReminderCount.'"'; ?> />
							<br />
							Harga:<br />
							<input id="txtPrice" name="txtPrice" type="text" class="form-control" placeholder="Harga " style="text-align:right;" readonly <?php echo 'value="'.number_format($Price,2,".",",").'"'; ?> />
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitValidate(this.form);" ><i class="fa fa-save"></i> Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				$("#ddlCategory").combobox();
				var IsEdit = $("#hdnIsEdit").val();
				if(IsEdit == 1) {
					$("#ddlCategory option[value='" + $("#hdnCategoryID").val() + "']").attr('selected', 'selected');
					$("#ddlUnit option[value='" + $("#hdnUnitID").val() + "']").attr('selected', 'selected');
					$("#ddlCategory").combobox("destroy");
					$("#ddlCategory").combobox();
				}
			});
			function SubmitValidate(form) {
				var PassValidate = 1;
				var FirstFocus = 0;
				$(".form-control").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
				
				if($("#ddlCategory").val() == "") {
					PassValidate = 0;
					$(".custom-combobox-input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $(".custom-combobox-input").focus();
					FirstFocus = 1;
				}
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else SubmitForm("./Master/Item/Insert.php");
			}
		</script>
	</body>
</html>
