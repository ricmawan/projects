<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	
	$PatientID = mysql_real_escape_string($_POST['ddlPatient']);
	$Category = mysql_real_escape_string($_POST['ddlCategory']);
	
	$sql = "SELECT
				CONCAT(PatientNumber, '_', REPLACE(PatientName, ' ', '_')) FolderName
			FROM
				master_patient
			WHERE
				PatientID = $PatientID";
				
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	
	$row = mysql_fetch_array($result);
	$RootFolder = '../../assets/photos/';
	$TargetFolder = $RootFolder . $row['FolderName'] . "/" . $Category . "/*";
	//echo $TargetFolder;
	
	$ArrFolder = glob($TargetFolder, GLOB_ONLYDIR);
	natsort($ArrFolder);
	$i = 0;
	foreach($ArrFolder as $dir) {
		//$dirname = basename($dir);
		//echo $dir . "<br />";
		$Images = glob($dir . "/*.{jpg,gif,png}",GLOB_BRACE);
		natsort($Images);
		if(count($Images) > 0) {
			if($i== 0) echo '<div class="item active">';
			else echo '<div class="item">';
			echo '<div class="col-xs-4">';
			foreach($Images as $photos) {
				echo '<div ><a class="imageGallery" rel="group1" href="'.str_replace("../../", "./", $photos).'"><img src="'.str_replace("../../", "./", $photos).'" class="img-responsive" style="width:300px;"></a></div><br /><br />';
			}
			echo '</div>';
			echo '</div>';
			$i++;
		}
	}
	if($i == 1) echo '<div class="item">
					   <div class="col-xs-4">
						  <div ><a href="#1"></a></div>
					   </div>
					</div>
					<div class="item">
					   <div class="col-xs-4">
						  <div ><a href="#1"></a></div>
					   </div>
					</div>';
	else if($i==2) echo '<div class="item">
						   <div class="col-xs-4">
							  <div ><a href="#1"></a></div>
						   </div>
						</div>';
?>