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
	$Merge->setAttribute('SourceFile',"Microsoft_VC90_CRT_{$includemsm}.msm");
	$Merge->setAttribute('DiskId','1');
	$Merge->setAttribute('Language','0');
	$Merge = $PHPInstallerBaseWXS->createElement('Merge');
	$Merge = $TargetDir->appendChild($Merge);
	$Merge->setAttribute('Id','VCRedist_policy');
	$Merge->setAttribute('SourceFile',"policy_9_0_Microsoft_VC90_CRT_{$includemsm}.msm");
	$Merge->setAttribute('DiskId','1');
	$Merge->setAttribute('Language','0');
    
	$MainFeature = $PHPInstallerBaseWXS->getElementsByTagName('Feature')->item(0);
	$MergeRef = $PHPInstallerBaseWXS->createElement('MergeRef');
	$MergeRef = $MainFeature->appendChild($MergeRef);
	$MergeRef->setAttribute('Id','VCRedist');
    $MergeRef = $PHPInstallerBaseWXS->createElement('MergeRef');
	$MergeRef = $MainFeature->appendChild($MergeRef);
	$MergeRef->setAttribute('Id','VCRedist_policy');
}

$PHPInstallerBaseWXS->save("PHPInstaller$version.wxs");

// remove extension info from php.ini-production
$infile = fopen("Files/php.ini-production",'r');
$outfile = fopen("Files/php.ini",'w');
if (!$outfile || !$infile) {
	echo "Cannot open php.ini or php.ini-production\n";
	exit(1);
}
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
