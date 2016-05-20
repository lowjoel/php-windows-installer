@echo off
setlocal enabledelayedexpansion

set phpver=%1
set phpver=!phpver:~0,3!
set phpver=!phpver:.=!

set suffix=
set extrants=
set extrasnaps=
set vcver=VC6
set vcarch=x86
set buildtype="VC6-x86"

if (%2)==() goto build
if %2==nts set extrants="nts-"
if %2==nts set suffix="NTS"
if %2==vc9 set vcver=VC9
if %2==vc11 set vcver=VC11
if %2==x64 set vcarch=x64
if %2==snapshot set extrasnaps="-latest"

if (%3)==() goto build
if %3==nts set extrants="nts-"
if %3==nts set suffix="NTS"
if %3==vc9 set vcver=VC9
if %3==vc11 set vcver=VC11
if %3==x64 set vcarch=x64
if %3==snapshot set extrasnaps="-latest"

if (%4)==() goto build
if %4==nts set extrants="nts-"
if %4==nts set suffix="NTS"
if %4==vc9 set vcver=VC9
if %4==vc11 set vcver=VC11
if %4==x64 set vcarch=x64
if %4==snapshot set extrasnaps="-latest"

:build
set buildtype="!vcver!-!vcarch!"

set msiname="php-%1-%extrants%win32-%buildtype%-installer%extrasnaps%.msi"

echo Building ExtensionsFeatures.wxs
copy ExtensionsFeatures%phpver%.wxs ExtensionsFeatures.wxs

echo Building ExtensionsComponents.wxs
Files\php.exe -n GenExtensionsComponents.wxs.php "%phpver%" "%buildtype%"

echo Building PHPInstaller%1.wxs
Files\php.exe -n GenPHPInstaller.wxs.php "PHPInstallerBase%phpver%%suffix%.wxs" "%1" "!vcver!" "!vcarch!"

echo Building WebServerConfig%1.wxs
copy WebServerConfig%phpver%%suffix%.wxs WebServerConfig%1.wxs

echo Compiling UI....
candle.exe -out PHPInstallerCommon.wixobj PHPInstallerCommon%phpver%%suffix%.wxs

echo Building UI....
lit.exe -out PHPInstallerCommon.wixlib PHPInstallerCommon.wixobj 

echo Compiling Installer....
candle.exe -dPlatform="!vcarch!" ExtensionsComponents.wxs ExtensionsFeaturesBuild.wxs WebServerConfig%1.wxs PHPInstaller%1.wxs 

echo Linking Installer....
light.exe -out "%msiname%" ExtensionsComponents.wixobj ExtensionsFeaturesBuild.wixobj WebServerConfig%1.wixobj PHPInstaller%1.wixobj PHPInstallerCommon.wixlib -loc WixUI_en-us.wxl
