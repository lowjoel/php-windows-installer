# PHP Installer for Windows

This is the PHP Installer for Windows, written in WiX. This was built on top of the PHP Installer
that used to be part of the PHP repository (which is no longer maintained).

## Prerequisites

 - WiX 3.9
 - PHP Windows binary distribution
 - The PHP Manual in CHM format.

## Build instructions

 1. Unzip the PHP Windows zip binary distribution into the "Files" directory.
 2. Copy PHP Manual CHM File into the "Files" directory.
 3. Run `build.bat` script with the following arguments:
    - The first argument is the PHP version being built (e.g. `5.6.10`)
    - The second argument is the compiler (e.g. `vc11`, `vc9`; defaults to `vc6`)
    - The third argument is the build architecture (`x86`, `x64`)

    Example:

    build.bat 5.4.15 vc9 x64
 4. [BUG] `build.bat` will produce a bunch of files:
    - ExtensionsComponents.wxs
    - ExtensionsFeaturesBuild.wxs
    - PHPInstaller{VERSION_NUMBER}.wxs

    These files, as well as PHPInstallerCommon{PHP_MAJOR_MINOR_VERSION}.wxs, and
    WebServerConfig{PHP_MAJOR_MINOR_VERSION}.wxs need to be built and linked together.
