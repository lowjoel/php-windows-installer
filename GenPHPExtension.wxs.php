<?php
require_once "GenFunctions.php";

$extname = $argv[1];
$extver = $argv[2];
$buildtype = $argv[3];

$ExtensionsGUIDXML = new DOMDocument;
$ExtensionsGUIDXML->preserveWhiteSpace = false;
$ExtensionsGUIDXML->load('ExtensionsGUID.xml');
$ExtensionsGUIDXML->formatOutput = true;
$xp = new DomXPath($ExtensionsGUIDXML);
$res = $xp->query("//Extension[@Name = '$extname']");
if ( $res->item(0) != null ) {
    $guidId = $res2->item(0)->getAttribute('Guid');
    if ( $res2->item(0)->hasAttribute('UpgradeCode') )
        $guidUpgrade = $res2->item(0)->getAttribute('UpgradeCode');
    else {
        $guidUpgrade = genGUID();
        $Extension->setAttribute('UpgradeCode',$guidUpgrade);
        $ExtensionsGUIDXML->save('extensionsGUID.xml');
    }
}
else {
    $xp2 = new DomXPath($ExtensionsGUIDXML);
    $res2 = $xp2->query("//Extensions");
    $Extensions = $res2->item(0);
    $guidId = genGUID();
    $guidUpgrade = genGUID();
    $Extension = $ExtensionsGUIDXML->createElement('Extension');
    $Extension = $Extensions->appendChild($Extension);
    $Extension->setAttribute('Name',$cid);
    $Extension->setAttribute('Guid',$guidId);
    $Extension->setAttribute('UpgradeCode',$guidUpgrade);
    $ExtensionsGUIDXML->save('extensionsGUID.xml');
}

$PHPExtensionBaseWXS = new DOMDocument;
$PHPExtensionBaseWXS->preserveWhiteSpace = false;
$PHPExtensionBaseWXS->load($basefile);
$PHPExtensionBaseWXS->formatOutput = true;

// Update the Package info to be specific for this build
$PHPExtensionBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Name","PHP $version");
$PHPExtensionBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Version","$version");
$PHPExtensionBaseWXS->getElementsByTagName('Product')->item(0)->setAttribute("Id",genGUID());
$PHPExtensionBaseWXS->getElementsByTagName('Package')->item(0)->setAttribute("Description","PHP $version Installer");

// check for presence of certain file; if not there then remove the feature from the installer
$files = array(
    'readme-redist-bins.txt' => 'readmedistbinsTXT',
    );

foreach ( $files as $filename => $component ) {
    if ( !file_exists("Files/$filename") ) {
        $Components = $PHPInstallerBaseWXS->getElementsByTagName('Component');
        foreach ( $Components as $Component ) {
            if ( $Component->getAttribute('Id') == $component ) {
                $Component->parentNode->removeChild($Component);
                break;
            }
        }
        $ComponentRefs = $PHPInstallerBaseWXS->getElementsByTagName('ComponentRef');
        foreach ( $ComponentRefs as $ComponentRef ) {
            if ( $ComponentRef->getAttribute('Id') == $component ) {
                $ComponentRef->parentNode->removeChild($ComponentRef);
                break;
            }
        }
    }
}

$PHPInstallerBaseWXS->save("PHPInstaller$version.wxs");

exit;
?>
