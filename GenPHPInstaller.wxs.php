<?php
require_once "GenFunctions.php";

$basefile = $argv[1];
$version = $argv[2];

$PHPInstallerBaseWXS = new DOMDocument;
$PHPInstallerBaseWXS->preserveWhiteSpace = false;
$PHPInstallerBaseWXS->load($basefile);
$PHPInstallerBaseWXS->formatOutput = true;
$PHPInstallerBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Name","PHP $version");
$PHPInstallerBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Version","$version");
$PHPInstallerBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Id",genGUID());
$PHPInstallerBaseWXS->getElementsByTagName('Package')->item(0)->setAttribute("Description","PHP $version Installer");
$PHPInstallerBaseWXS->save("PHPInstaller$version.wxs");

// remove extension info from php.ini-recommended
$infile = fopen("Files/php.ini-recommended",'r');
$outfile = fopen("Files/php.ini",'w');
while (!feof($infile)) {
    $buffer = fgets($infile);
    if ( stristr($buffer,';extension') === FALSE ) {
        fwrite($outfile,$buffer);
    }
}
fclose($infile);
fclose($outfile);

exit;
?>
