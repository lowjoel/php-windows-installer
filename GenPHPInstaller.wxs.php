<?php
require_once "GenFunctions.php";

$basefile = $argv[1];
$version = $argv[2];
$includemsm = $argv[3];

$PHPInstallerBaseWXS = new DOMDocument;
$PHPInstallerBaseWXS->preserveWhiteSpace = false;
$PHPInstallerBaseWXS->load($basefile);
$PHPInstallerBaseWXS->formatOutput = true;

// Update the Package info to be specific for this build
$PHPInstallerBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Name","PHP $version");
$PHPInstallerBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Version","$version");
$PHPInstallerBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Id",genGUID());
$PHPInstallerBaseWXS->getElementsByTagName('Package')->item(0)->setAttribute("Description","PHP $version Installer");

// Add in the VC9 MSM if we are building that installer
if ( !empty($includemsm) ) {
	$TargetDir = $PHPInstallerBaseWXS->getElementsByTagName('Directory')->item(0);
	$Merge = $PHPInstallerBaseWXS->createElement('Merge');
	$Merge = $TargetDir->appendChild($Merge);
	$Merge->setAttribute('Id','VCRedist');
	$Merge->setAttribute('SourceFile','Microsoft_VC90_CRT_x86.msm');
	$Merge->setAttribute('DiskId','1');
	$Merge->setAttribute('Language','0');
	
	$MainFeature = $PHPInstallerBaseWXS->getElementsByTagName('Feature')->item(0);
	$Feature = $PHPInstallerBaseWXS->createElement('Feature');
	$Feature = $MainFeature->appendChild($Feature);
	$Feature->setAttribute('Id','VCRedist');
	$Feature->setAttribute('Title','Visual C++ 9.0 Runtime');
	$Feature->setAttribute('AllowAdvertise','no');
	$Feature->setAttribute('Display','hidden');
	$Feature->setAttribute('Level','1');
	
	$MergeRef = $PHPInstallerBaseWXS->createElement('MergeRef');
	$MergeRef = $Feature->appendChild($MergeRef);
	$MergeRef->setAttribute('Id','VCRedist');
}

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
