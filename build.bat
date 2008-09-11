@echo off

set phpver=%1
set phpver=%phpver:~0,3%
set phpver=%phpver:.=%

echo Building ExtensionsFeatures.wxs
copy ExtensionsFeatures%phpver%.wxs ExtensionsFeatures.wxs

set suffix=
set extrants=
set extrabuildtype=
set includevc9msm=

if (%2)==() goto build
if %2==nts set extrants="nts-"
if %2==nts set suffix="NTS"
if %2==vc9 set extrabuildtype="vc9-"
if %2==vc9 set includevc9msm="x86"

if (%3)==() goto build
if %3==nts set extrants="nts-"
if %3==nts set suffix="NTS"
if %3==vc9 set extrabuildtype="vc9-"
if %3==vc9 set includevc9msm="x86"

:build
set msiname="php-%1-%extrants%win32-%extrabuildtype%installer.msi"

echo Building ExtensionsComponents.wxs
Files\php.exe GenExtensionsComponents.wxs.php "%phpver%"

echo Building PHPInstaller%1.wxs
Files\php.exe GenPHPInstaller.wxs.php "PHPInstallerBase%phpver%%suffix%.wxs" "%1" "%includevc9msm%"

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
