@echo off
:start
php.exe -f console-cli-thread.php > "logs/console.log" 2>&1
IF %ERRORLEVEL% GTR 0 GOTO start
:end