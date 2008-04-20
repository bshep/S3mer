rem set APPPATH=C:\Program Files\S3mer\
rem @echo off

set UTILPATH=%APPPATH%\assets\Windows
set STARTUPPATH=%USERPROFILE%\Start Menu\Programs\Startup

cd "%UTILPATH%"

powercfg /I s3mer
powercfg /S s3mer

rem qres /x:1024

nircmdc.exe setsysvolume 65535
nircmdc.exe mutesysvolume 0
nircmdc.exe regsetval sz "HKCU\control panel\desktop" "ScreenSaveActive" 0

nircmdc.exe shortcut "%APPPATH%\S3mer.exe" "~$folder.startup$" "S3mer"


rem call "%UTILPATH%\doall.bat"

del "%STARTUPPATH%\firstrun.bat"