@echo off
echo Building ExtensionsComponents.wxs
Files\php.exe GenExtensionsComponents.wxs.php

echo Building PHPInstaller%1.wxs
Files\php.exe GenPHPInstaller.wxs.php "PHPInstallerBase52.wxs" "%1"

echo Compiling UI....
Wix\candle.exe PHPInstallerCommon.wxs

echo Building UI....
Wix\lit.exe -out PHPInstallerCommon.wixlib PHPInstallerCommon.wixobj 

echo Compiling Installer....
Wix\candle.exe ExtensionsComponents.wxs ExtensionsFeaturesBuild.wxs WebServerConfig.wxs PHPInstaller%1.wxs 

echo Linking Installer....
Wix\light.exe -out "php-%1-win32-installer.msi" ExtensionsComponents.wixobj ExtensionsFeaturesBuild.wixobj WebServerConfig.wixobj PHPInstaller%1.wixobj PHPInstallerCommon.wixlib -loc WixUI_en-us.wxl
