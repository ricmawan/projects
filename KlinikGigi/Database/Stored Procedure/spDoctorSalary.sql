/* Contoh Pengguanaan
	CALL spDoctorSalary(3, 2018);
*/
DROP PROCEDURE IF EXISTS spDoctorSalary;

DELIMITER $$
CREATE PROCEDURE spDoctorSalary (
	BusinessMonth		INT,
	BusinessYear		INT
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
SET State = 1;

	SELECT
		MU.UserName Dokter,
		DATE_FORMAT(TM.TransactionDate, '%d-%m-%Y') Tanggal,
		ME.ExaminationName Tindakan,
		TMD.Quantity Jumlah,
		TMD.Price Harga,
		TMD.Quantity * TMD.Price AS SubTotal,
		CONCAT(TDC.CommisionPercentage, '%') AS PresentaseKomisi,
		(TMD.Quantity * TMD.Price * TDC.CommisionPercentage / 100) Komisi,
		TDC.ToolsFee,
		((STM.KomisiBulanan * TDC.CommisionPercentage / 100) - TDC.ToolsFee) KomisiBulanan
	FROM
		transaction_medication TM
		JOIN transaction_medicationdetails TMD
			ON TM.MedicationID = TMD.MedicationID
		JOIN master_examination ME
			ON ME.ExaminationID = TMD.ExaminationID
		JOIN master_user MU
			ON MU.UserID = TMD.DoctorID
		LEFT JOIN transaction_doctorcommision TDC
			ON TDC.DoctorID = TMD.DoctorID
			AND TDC.BusinessMonth = BusinessMonth
			AND TDC.BusinessYear = BusinessYear
		LEFT JOIN
		(
			SELECT
				TMD.DoctorID,
				SUM(TMD.Quantity * TMD.Price) KomisiBulanan
			FROM
				transaction_medication TM
				JOIN transaction_medicationdetails TMD
					ON TM.MedicationID = TMD.MedicationID
			WHERE
				MONTH(TM.TransactionDate) = BusinessMonth
				AND YEAR(TM.TransactionDate) = BusinessYear
				AND TM.IsCancelled = 0
			GROUP BY
				TMD.DoctorID
		)STM
			ON STM.DoctorID = TMD.DoctorID
	WHERE
		MONTH(TM.TransactionDate) = BusinessMonth
		AND YEAR(TM.TransactionDate) = BusinessYear
		AND MU.UserTypeID = 2
		AND TM.IsCancelled = 0
	GROUP BY
		MU.UserName,
		TM.TransactionDate,
		ME.ExaminationName,
		TMD.Quantity,
		TMD.Price,
		TDC.CommisionPercentage,
		TDC.ToolsFee
	ORDER BY	
		MU.UserName ASC;
        
END;
$$
DELIMITER ;
