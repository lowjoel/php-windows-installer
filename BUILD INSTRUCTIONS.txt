Pre-requisites:

- .NET Framework 1.1 SP1 Runtime installed
- PHP Windows zip binary distribution
- The pre-compiled PECL extensions for Windows from http://pecl4win.php.net/branch.php
- Copy of PHP Manual in CHM format.
- Local Checked out copy of this directory

1) Unzip the PHP Windows zip binary distribution into the "Files" directory.
2) Unzip the PECL extensions zip file for the branch into the "Files/pecl" directory.
3) Copy PHP Manual CHM File into the "Files" directory.
4) Run the "build.bat" script with the first arguement as the version you are building for.
   The second and third arguments will be your build environment:
   "vc9", "vc11", and architecture "x86", "x64"

	Example:

	build.bat 5.2.0 when building for PHP version 5.2.0
	build.bat 5.4.15 vc9 x64

The script will produce the installer as php-VERSION-win32-install.msi where VERSION is the string passed to the build.bat script.