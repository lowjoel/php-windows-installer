<?php
require_once "GenFunctions.php";

$ExtensionsWXS = new DOMDocument;
$ExtensionsWXS->preserveWhiteSpace = false;
$ExtensionsWXS->load('ExtensionsFragment.wxs');
$ExtensionsWXS->formatOutput = true;
$ExtensionsGUIDXML = new DOMDocument;
$ExtensionsGUIDXML->preserveWhiteSpace = false;
$ExtensionsGUIDXML->load('ExtensionsGUID.xml');
$ExtensionsGUIDXML->formatOutput = true;

$xp = new DomXPath($ExtensionsWXS);
$res = $xp->query("//*[@Id = 'extdirectory']");
$Directory_extdirectory = $res->item(0);

$it = new DirectoryIterator('Files\ext');
foreach ( $it as $filename ) {
	if ( $filename->isDot() || $filename == "CVS" ) continue;
    /* hack to remove some problematic extensions 
       until a better solution comes along */
    if ( $filename == "php_threads.dll"
        || $filename == "php_iisfunc.dll"
        || $filename == "php_sam.dll"
        || $filename == "php_win32scheduler.dll"
        || $filename == "php_win32service.dll" ) continue;
    list($basename,$ext) = explode('.',$filename);
	$cid = $basename . ( $ext != 'dll' ? strtoupper($ext) : "");
    $Component = $ExtensionsWXS->createElement('Component');
	$Component = $Directory_extdirectory->appendChild($Component);
	$Component->setAttribute('Id',$cid);
	$Component->setAttribute('DiskId',"1");
	
	$xp2 = new DomXPath($ExtensionsGUIDXML);
	$res2 = $xp2->query("//Extension[@Name = '$cid']");
    if ( $res2->item(0) != null ) {
		$guid = $res2->item(0)->getAttribute('Guid');
	}
	else {
		$xp3 = new DomXPath($ExtensionsGUIDXML);
        $res3 = $xp3->query("//Extensions");
        $Extensions = $res3->item(0);
        $guid = genGUID();
        $Extension = $ExtensionsGUIDXML->createElement('Extension');
		$Extension = $Extensions->appendChild($Extension);
		$Extension->setAttribute('Name',$cid);
		$Extension->setAttribute('Guid',$guid);
		$ExtensionsGUIDXML->save('extensionsGUID.xml');
	}
	$Component->setAttribute('Guid',$guid);
	
	$File = $ExtensionsWXS->createElement('File');
	$File = $Component->appendChild($File);
	$File->setAttribute('Id',"file{$basename}" . strtoupper($ext));
	if ( strlen($basename) > 8 ) {
		$File->setAttribute('Name',getshortname("Files\\ext\\$filename"));
		$File->setAttribute('LongName',$filename);
	}
	else {	
		$File->setAttribute('Name',$filename);
	}
	$File->setAttribute('Source',"Files\ext\\{$filename}");
	
	if ($ext == 'dll' && stristr($basename,'php_') !== FALSE ) {
		$IniFile = $ExtensionsWXS->createElement('IniFile');
		$IniFile = $Component->appendChild($IniFile);
		$IniFile->setAttribute('Id',"{$basename}INI");
		$IniFile->setAttribute('Action',"createLine");
		$IniFile->setAttribute('Key',"extension");
		$IniFile->setAttribute('Directory',"INSTALLDIR");
		$IniFile->setAttribute('Name',"php.ini");
		$IniFile->setAttribute('Section',strtoupper($basename));
		$IniFile->setAttribute('Value',$filename);
	}
}

$it = new DirectoryIterator('Files\PECL');
foreach ( $it as $filename ) {
	if ( $filename->isDot() || $filename == "CVS" ) continue;
    if ( is_file("Files\\ext\\$filename") ) continue;
	list($basename,$ext) = explode('.',$filename);
    $cid = $basename . ( $ext != 'dll' ? strtoupper($ext) : "");
    $Component = $ExtensionsWXS->createElement('Component');
	$Component = $Directory_extdirectory->appendChild($Component);
	$Component->setAttribute('Id',$cid);
    $Component->setIdAttribute('Id',true);
	$Component->setAttribute('DiskId',"1");
	
	$xp2 = new DomXPath($ExtensionsGUIDXML);
	$res2 = $xp2->query("//Extension[@Name = '$cid']");
	if ( $res2->item(0) != null ) {
		$guid = $res2->item(0)->getAttribute('Guid');
	}
	else {
		$xp4 = new DomXPath($ExtensionsGUIDXML);
        $res4 = $xp4->query("//Extensions");
        $Extensions = $res4->item(0);
        $guid = genGUID();
        $Extension = $ExtensionsGUIDXML->createElement('Extension');
		$Extension = $Extensions->appendChild($Extension);
		$Extension->setAttribute('Name',$cid);
		$Extension->setAttribute('Guid',$guid);
		$ExtensionsGUIDXML->save('extensionsGUID.xml');
	}
	$Component->setAttribute('Guid',$guid);
	
	$File = $ExtensionsWXS->createElement('File');
	$File = $Component->appendChild($File);
	$File->setAttribute('Id',"file{$basename}" . strtoupper($ext));
	if ( strlen($basename) > 8 ) {
		$File->setAttribute('Name',getshortname("Files\\PECL\\$filename"));
		$File->setAttribute('LongName',$filename);
	}
	else {	
		$File->setAttribute('Name',$filename);
	}
	$File->setAttribute('Source',"Files\PECL\\{$filename}");
	
	if ($ext == 'dll' && stristr($basename,'php_') !== FALSE ) {
		$IniFile = $ExtensionsWXS->createElement('IniFile');
		$IniFile = $Component->appendChild($IniFile);
		$IniFile->setAttribute('Id',"{$basename}INI");
		$IniFile->setAttribute('Action',"createLine");
		$IniFile->setAttribute('Key',"extension");
		$IniFile->setAttribute('Directory',"INSTALLDIR");
		$IniFile->setAttribute('Name',"php.ini");
		$IniFile->setAttribute('Section',strtoupper($basename));
		$IniFile->setAttribute('Value',$filename);
	}
}

// Remove Files defined that don't exist
$files = $ExtensionsWXS->getElementsByTagName("File");
$i = 0;
$hasSeenMbString = false;
while ( $i < $files->length ) {
    if ( !is_file( realpath($files->item($i)->getAttribute('Source')) ) ) {
        $nodetoremove = $files->item($i)->parentNode;
        echo "Removing File " . $nodetoremove->getAttribute('Id') . " because " . realpath($files->item($i)->getAttribute('Source')) . " doesn't exist\n";
        $files->item($i)->parentNode->parentNode->removeChild($nodetoremove);
        $i--;
    }
    else {
        /* ugly hack to fix Bug #43970 - make sure mbstring ext loads before exif ext */
        if ( $files->item($i)->getAttribute('Id') == 'filephp_mbstringDLL' ) 
            $hasSeenMbString = true;
        if ( $files->item($i)->getAttribute('Id') == 'filephp_exifDLL' && !$hasSeenMbString ) {
            $ExifExtensionNode = $files->item($i)->parentNode;
            $files->item($i)->parentNode->parentNode->removeChild($ExifExtensionNode);
            $Directory_extdirectory->appendChild($ExifExtensionNode);
            $i--;
        }
        else
            $i++;
    }
}

$ExtensionsWXS->save('ExtensionsComponents.wxs');

// Remove features from ExtensionsFeatures.wxs that don't have the base component existing
$ExtensionsFeaturesWXS = new DOMDocument;
$ExtensionsFeaturesWXS->preserveWhiteSpace = false;
$ExtensionsFeaturesWXS->load('ExtensionsFeatures.wxs');
$ExtensionsFeaturesWXS->formatOutput = true;
$componentRefs = $ExtensionsFeaturesWXS->getElementsByTagName("ComponentRef");
$i = 0;
while ( $i < $componentRefs->length ) {
    $componentRefId = $componentRefs->item($i)->getAttribute('Id');
    $components = $ExtensionsWXS->getElementsByTagName("Component");
    for ( $j = 0; $j < $components->length; $j++ ) {
        $componentId = $components->item($j)->getAttribute('Id');
        if ( $componentId == $componentRefId ) {
            $i++;
            continue 2;
        }
    }
    $nodetoremove = $componentRefs->item($i)->parentNode;
    echo "Removing Feature " . $nodetoremove->getAttribute('Id') . " because of missing Component $componentRefId\n";
    $componentRefs->item($i)->parentNode->parentNode->removeChild($nodetoremove);
    $i--;
}
$ExtensionsFeaturesWXS->save('ExtensionsFeaturesBuild.wxs');


exit;
?>
