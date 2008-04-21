rem set APPPATH=C:\Program Files\S3mer\
rem @echo off

set UTILPATH=%APPPATH%\assets\Windows
set STARTUPPATH=%USERPROFILE%
cd "%UTILPATH%"

powercfg /I s3mer
powercfg /S s3mer

rem qres /x:1024

nircmdc.exe setsysvolume 65535
nircmdc.exe mutesysvolume 0
nircmdc.exe regsetval sz "HKCU\control panel\desktop" "ScreenSaveActive" 0

at 04:00 /every:M,T,W,Th,F,S,Su "shutdown -r -t 0"


rem nircmdc.exe shortcut "%APPPATH%\S3mer.exe" "~$folder.startup$" "S3mer"


rem call "%UTILPATH%\doall.bat"

echo '' > "%STARTUPPATH%\S3merSetupDone"

del "%STARTUPPATH%\Desktop\S3mer Config.lnk"
del "%STARTUPPATH%\firstrun.bat"
