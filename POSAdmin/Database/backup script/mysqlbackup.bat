:: Auto MySQL Backup For Windows Servers By Matt Moeller  v.1.5
:: RED OLIVE INC.  - www.redolive.com
:: URL: http://www.redolive.com/automated-mysql-backup-for-windows/

:: Follow us on twitter for updates to this script  twitter.com/redolivedesign
:: coming soon:  email admin a synopsis of the backup with total file size(s) and time it took to execute

:: FILE HISTORY ----------------------------------------------
:: UPDATE 3.25.2015  Added features and pre requisite section and changed path to 7zip exe file
:: UPDATE 11.7.2012  Added setup all folder paths into variables at the top of the script to ease deployment
:: UPDATE 7.16.2012  Added --routines, fix for dashes in filename, and fix for regional time settings
:: UPDATE 3.30.2012  Added error logging to help troubleshoot databases backup errors.   --log-error="c:\MySQLBackups\backupfiles\dumperrors.txt"
:: UPDATE 12.29.2011 Added time bug fix and remote FTP options - Thanks to Kamil Tomas
:: UPDATE 5.09.2011  v 1.0

:: FEATURES --------------------------------------------------
:: - Backup all MySQl databases, including all newly created ones automatically
:: - Create an individual .sql file for each database (God send when restoring)
:: - ZIP all the .sql files into one zip file and date/timestamp the file name to save space
:: - Automatically delete MySQL backups older than n days (set to however many days you like)
:: - FTP your backup zip to a remote location
:: - Highly suggest you also setup a scheduled task to backup your MySQL directory and your new backup folder to an off site location

:: PRE REQUISITE ---------------------------------------------
:: - MySQL server installed
:: - 7zip installed

:: If the time is less than two digits insert a zero so there is no space to break the filename

:: If you have any regional date/time issues call this include: getdate.cmd  credit: Simon Sheppard for this cmd - untested
::call getdate.cmd

set year=%DATE:~6,4%
set day=%DATE:~0,2%
set mnt=%DATE:~3,2%
set hr=%TIME:~0,2%
set min=%TIME:~3,2%

IF %day% LSS 10 SET day=0%day:~1,1%
IF %mnt% LSS 10 SET mnt=0%mnt:~1,1%
IF %hr% LSS 10 SET hr=0%hr:~1,1%
IF %min% LSS 10 SET min=0%min:~1,1%

set backuptime=%year%-%day%-%mnt%-%hr%-%min%
echo %backuptime%

:: SETTINGS AND PATHS
:: Note: Do not put spaces before the equal signs or variables will fail

:: Name of the database user with rights to all tables
set dbuser=root

:: Name of the database
set dbname="pos"

:: Password for the database user
set dbpass=

:: Error log path - Important in debugging your issues
set errorLogPath="C:\MySQLBackups\backupfiles\dumperrors.txt"

:: MySQL EXE Path
set mysqldumpexe="C:\xampp\mysql\bin\mysqldump.exe"

:: Error log path
set backupfldr="C:\MySQLBackups\backupfiles\"

:: Path to data folder which may differ from install dir
set datafldr="C:\xampp\mysql\data"

:: Path to zip executable
set zipper="C:\Program Files\7-Zip\7z.exe"

:: Number of days to retain .zip backup files
set retaindays=-120

:: DONE WITH SETTINGS

:: GO FORTH AND BACKUP EVERYTHING!

:: Switch to the data directory to enumerate the folders
pushd %datafldr%

echo "Pass each name to mysqldump.exe and output an individual .sql file for each"

:: Thanks to Radek Dolezel for adding the support for dashes in the db name
:: Added --routines thanks for the suggestion Angel

:: turn on if you are debugging
@echo off

	%mysqldumpexe% --user=%dbuser% --password=%dbpass% --databases --routines --log-error=%errorLogPath% %dbname% > "%backupfldr%%dbname%.%backuptime%.sql"

echo "Zipping all files ending in .sql in the folder"

:: .zip option clean but not as compressed
%zipper% a -tzip "%backupfldr%FullBackup.%backuptime%.zip" "%backupfldr%*.sql"

echo "Deleting all the files ending in .sql only"

del "%backupfldr%*.sql"

:: Set the number of days to keep backups, using the win program "Forfiles" for this, mine is set to 30 days  "-30"
echo "Deleting zip files older than 30 days now"
Forfiles -p %backupfldr% -s -m *.* -d -%retaindays% -c "cmd /c del /q @path"


::FOR THOSE WHO WISH TO FTP YOUR FILE UNCOMMENT THESE LINES AND UPDATE - Thanks Kamil for this addition!

::cd\[path to directory where your file is saved]
::@echo off
::echo user [here comes your ftp username]>ftpup.dat
::echo [here comes ftp password]>>ftpup.dat
::echo [optional line; you can put "cd" command to navigate through the folders on the ftp server; eg. cd\folder1\folder2]>>ftpup.dat
::echo binary>>ftpup.dat
::echo put [file name comes here; eg. FullBackup.%backuptime%.zip]>>ftpup.dat
::echo quit>>ftpup.dat
::ftp -n -s:ftpup.dat [insert ftp server here; eg. myserver.com]
::del ftpup.dat

echo "done"

::return to the main script dir on end
popd
