<?php
$basefile = $argv[1];
$version = $argv[2];

$PHPInstallerBaseWXS = DOMDocument::load($basefile);
$PHPInstallerBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Name","PHP $version");
$PHPInstallerBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Version","$version");
$PHPInstallerBaseWXS->save("PHPInstaller$version.wxs");
exit;
?>
