<?php
	include "./DBConfig.php";
	$sql = "SELECT
				MR.RoomID,
				MR.RoomNumber,
				CASE
					WHEN MR.StatusID = 1 AND BO.BookingFlag > 0
					THEN 2
					ELSE MR.StatusID
				END StatusID,
				CASE
					WHEN MR.StatusID = 1 AND BO.BookingFlag > 0
					THEN 'booked'
					ELSE MS.StatusName
				END StatusName
			FROM
				master_room MR
				JOIN master_status MS
					ON MS.StatusID = MR.StatusID
				LEFT JOIN
				(
					SELECT
						BO.RoomID,
						COUNT(1) BookingFlag
					FROM
						transaction_booking BO
					WHERE
						BO.IsCancelled = 0
						AND BO.CheckInFlag = 0
					GROUP BY
						BO.RoomID
				)BO
					ON BO.RoomID = MR.RoomID
			ORDER BY
				MR.RoomNumber ASC";
	if(!$result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	while($row = mysql_fetch_array($result)) {
		echo "<span acronym title='".$row['StatusName']."' class='room ".$row['StatusName']." dropdown' roomid=".$row['RoomID']." >".$row['RoomNumber']."
				<span class='dropbtn'></span>
				<div class='dropdown-content'>";
		if($row['StatusID'] == 1 || $row['StatusID'] == 2) echo "<a href='#' onclick='CheckIn(".$row['RoomID'].");' >Check-In</a>";
		if($row['StatusID'] == 2) echo "<a href='#' onclick='CheckInFromBooking(".$row['RoomID'].");' >Check-In dari Booking</a>";
		if($row['StatusID'] == 2) echo "<a href='#' onclick='Cancellation(".$row['RoomID'].");' >Pembatalan</a>";
		if($row['StatusID'] == 3) echo "<a href='#' onclick='CheckOut(".$row['RoomID'].");' >Check-Out</a>";
		echo "<a href='#' onclick='Booking(".$row['RoomID'].");'>Booking</a></div></span>";
	}
?>