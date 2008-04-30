@echo off

set phpver=%1
set phpver=%phpver:~0,3%
set phpver=%phpver:.=%
if %phpver%==53 set phpver=52

set msiname="php-%1-win32-installer.msi"
if %2==nts set msiname="php-%1-nts-win32-installer.msi"

set suffix=""
if %2==nts set suffix="NTS"

echo Building ExtensionsComponents.wxs
Files\php.exe GenExtensionsComponents.wxs.php

echo Building PHPInstaller%1.wxs
Files\php.exe GenPHPInstaller.wxs.php "PHPInstallerBase%phpver%%suffix%.wxs" "%1"

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
set phpver=
