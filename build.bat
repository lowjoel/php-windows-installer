@echo off

set phpver=%1
set phpver=%phpver:~0,3%
set phpver=%phpver:.=%

echo Building ExtensionsFeatures.wxs
copy ExtensionsFeatures%phpver%.wxs ExtensionsFeatures.wxs

set suffix=
set extrants=
set extrasnaps=
set buildtype="VC6-x86"
set includevc9msm=

if (%2)==() goto build
if %2==nts set extrants="nts-"
if %2==nts set suffix="NTS"
if %2==vc9 set buildtype="VC9-x86"
if %2==vc9 set includevc9msm="x86"
if %2==x64 set buildtype="VC9-x64"
if %2==x64 set includevc9msm="x86_x64"
if %2==snapshot set extrasnaps="-latest"

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

:build
set msiname="php-%1-%extrants%win32-%buildtype%-installer%extrasnaps%.msi"

echo Building ExtensionsComponents.wxs
Files\php.exe -n GenExtensionsComponents.wxs.php "%phpver%"

echo Building PHPInstaller%1.wxs
Files\php.exe -n GenPHPInstaller.wxs.php "PHPInstallerBase%phpver%%suffix%.wxs" "%1" "%includevc9msm%"

echo Building WebServerConfig%1.wxs
copy WebServerConfig%phpver%%suffix%.wxs WebServerConfig%1.wxs

echo Compiling UI....
Wix\candle.exe -out PHPInstallerCommon.wixobj PHPInstallerCommon%suffix%.wxs

echo Building UI....
Wix\lit.exe -out PHPInstallerCommon.wixlib PHPInstallerCommon.wixobj 

echo Compiling Installer....
Wix\candle.exe ExtensionsComponents.wxs ExtensionsFeaturesBuild.wxs WebServerConfig%1.wxs PHPInstaller%1.wxs 

echo Linking Installer....
Wix\light.exe -out "%msiname%" ExtensionsComponents.wixobj ExtensionsFeaturesBuild.wixobj WebServerConfig%1.wixobj PHPInstaller%1.wixobj PHPInstallerCommon.wixlib -loc WixUI_en-us.wxl
