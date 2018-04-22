@echo off
FOR /F "tokens=* USEBACKQ" %%F IN (`findstr /R /C:" * Version:" ..\woocommerce-warehouse-transactions.php`) DO (
SET versionBeforFilter=%%F
)

SET versionNumber=%versionBeforFilter:~11%

ECHO Creating file for new version %versionNumber%

mkdir version-%versionNumber%
cd version-%versionNumber%


mkdir woocommerce-warehouse-transactions
cd woocommerce-warehouse-transactions

::Create directories
CALL :cloneFolder "admin"
CALL :cloneFolder "cron"
CALL :cloneFolder "images"
CALL :cloneFolder "js"
CALL :cloneFolder "languages"
CALL :cloneFolder "libs"
CALL :cloneFolder "objects"
CALL :cloneFolder "toolbox"
CALL :cloneFolder "updater"

xcopy ..\..\..\*.php
xcopy ..\..\..\*.css
xcopy ..\..\..\*.png

cd ..

7z a -r ../versions/version-%versionNumber%.zip *

cd ..

rmdir /Q /S version-%versionNumber%

git tag -d v%versionNumber%
git tag -a v%versionNumber% -m "Version %versionNumber%"

:: function which copies the folder and it's content
:cloneFolder
mkdir %~1
xcopy ..\..\..\%~1\* %~1 /s /e
goto:EOF
