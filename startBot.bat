@echo off
:start
php.exe -f shell.class.php %1
IF %ERRORLEVEL% GTR 0 GOTO start
:end