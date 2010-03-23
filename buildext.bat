@echo off

set extname=%1
set extver=%2

set suffix=
set extrants=
set extrasnaps=
set buildtype="VC6-x86"
set includevc9msm=

if (%3)==() goto build
if %3==nts set extrants="nts-"
if %3==nts set suffix="NTS"
if %3==vc9 set buildtype="VC9-x86"
if %3==vc9 set includevc9msm="x86"
if %3==x64 set buildtype="VC9-x64"
if %3==x64 set includevc9msm="x86_x64"
if %3==snapshot set extrasnaps="-latest"

if (%4)==() goto build
if %4==nts set extrants="nts-"
if %4==nts set suffix="NTS"
if %4==vc9 set buildtype="VC9-x86"
if %4==vc9 set includevc9msm="x86"
if %4==x64 set buildtype="VC9-x64"
if %4==x64 set includevc9msm="x86_x64"
if %4==snapshot set extrasnaps="-latest"

if (%5)==() goto build
if %5==nts set extrants="nts-"
if %5==nts set suffix="NTS"
if %5==vc9 set buildtype="VC9-x86"
if %5==vc9 set includevc9msm="x86"
if %5==x64 set buildtype="VC9-x64"
if %5==x64 set includevc9msm="x86_x64"
if %5==snapshot set extrasnaps="-latest"

:build
set msiname="php-%extname%-%extver%-%extrants%win32-%buildtype%-installer%extrasnaps%.msi"

echo Building PHPExtension%extname%.wxs
Files\php.exe -n GenPHPExtension.wxs.php "%extname%" "%extver%" "%extrants%%buildtype%"

echo Compiling Installer....
Wix\candle.exe PHPExtension%extname%.wxs

echo Linking Installer....
Wix\light.exe -out "%msiname%" PHPExtension%extname%.wixobj Wix\wixui.wixlib -loc WixUI_en-us.wxl
