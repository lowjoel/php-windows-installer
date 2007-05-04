<?php

error_reporting(E_ALL);

function genGUID() {
   //e.g. output: 372472a2-d557-4630-bc7d-bae54c934da1
   //word*2-, word-, (w)ord-, (w)ord-, word*3
   $guidstr = "";
   for ($i=1;$i<=16;$i++) {
      $b = (int)rand(0,0xff);
      if ($i == 7) { $b &= 0x0f; $b |= 0x40; } // version 4 (random)
      if ($i == 9) { $b &= 0x3f; $b |= 0x80; } // variant
      $guidstr .= sprintf("%02s", base_convert($b,10,16));
      if ($i == 4 || $i == 6 || $i == 8 || $i == 10) { $guidstr .= '-'; }
   }
   return strtoupper($guidstr);
}   
function getshortname($filename) {
	$fso = new COM("Scripting.FileSystemObject");
	$file = $fso->GetFile($filename);
	
	return str_replace('~','_',$file->ShortName);
}

$xmlsource = <<<XML
<?xml version='1.0' encoding='iso-8859-1'?>
<Wix xmlns='http://schemas.microsoft.com/wix/2003/01/wi'>
	<Fragment Id='FragmentExtensions'>
		<DirectoryRef Id='INSTALLDIR'>
			<Component Id="fdftkDLL" DiskId="1" Guid="C54716D6-F1C6-4366-AD1E-8C2DB1686801">
				<File Id="filefdftkDLL" Name="fdftk.dll" Source="Files\fdftk.dll" />
			</Component>
			<Component Id="gds32DLL" DiskId="1" Guid="D70180EF-1AE2-45FF-B79F-DF16056F972D">
				<File Id="filegds32DLL" Name="gds32.dll" Source="Files\gds32.dll" />
			</Component>
			<Component Id="libeay32DLL" DiskId="1" Guid="1BF3C9B6-6495-476E-85B8-2843B5F5458D">
				<File Id="filelibeay32DLL" Name="libeay32.dll" Source="Files\libeay32.dll" />
			</Component>
			<Component Id="libmcryptDLL" DiskId="1" Guid="34402BFF-5D45-4A07-89EF-C71F4DF3D9BF">
				<File Id="filelibmcryptDLL" Name="LIBMCR_1.DLL" LongName="libmcrypt.dll" Source="Files\libmcrypt.dll" />
			</Component>
			<Component Id="libmhashDLL" DiskId="1" Guid="D19BD1F8-8041-4D0A-809C-57BB69619683">
				<File Id="filelibmhashDLL" Name="libmhash.dll" Source="Files\libmhash.dll" />
			</Component>
			<Component Id="libmysqlDLL" DiskId="1" Guid="4726DE50-BC55-46AC-9622-DEA04AE46E46">
				<File Id="filelibmysqlDLL" Name="libmysql.dll" Source="Files\libmysql.dll" />
			</Component>
			<Component Id="ntwdblibDLL" DiskId="1" Guid="7FA2C1EB-5166-4D16-AD36-DEF92A491D8B">
				<File Id="filentwdblibDLL" Name="ntwdblib.dll" Source="Files\\ntwdblib.dll" />
			</Component>
			<Component Id="msqlDLL" DiskId="1" Guid="C37BB17E-8C9D-4FB6-AD40-100BDDFDADD5">
				<File Id="filemsqlDLL" Name="msql.dll" Source="Files\msql.dll" />
			</Component>
			<Component Id="ssleay32DLL" DiskId="1" Guid="D7E5F122-9764-4165-BDDA-922D60408C2F">
				<File Id="filessleay32DLL" Name="ssleay32.dll" Source="Files\ssleay32.dll" />
			</Component>
			<Component Id="yazDLL" DiskId="1" Guid="4C0CE69A-DBE4-4294-88CC-F6E82A3CFCD1">
				<File Id="fileyazSLL" Name="yaz.dll" Source="Files\yaz.dll" />
			</Component>
			<Component Id="libswisheDLL" DiskId="1" Guid="4F1FD404-B722-4A1E-997A-474B6F001420">
				<File Id="filelibswisheDLL" Name="LIBSWI_1.DLL" LongName="libswish-e.dll" Source="Files\libswish-e.dll" />
			</Component>
			<Component Id="fribidiDLL" DiskId="1" Guid="D08A7C40-924B-45B2-8053-C6A5117846B2">
				<File Id="filefribidiDLL" Name="fribidi.dll" Source="Files\fribidi.dll" />
			</Component>
			<Directory Id="extdirectory" Name="ext">
				<Component Id="extdir" DiskId="1" Guid="3AB11270-4135-4C8C-9578-B034CEF2659F">
					<IniFile Id="extdirINI" Action="addLine" 
							Key="extension_dir" Directory="INSTALLDIR" Name="php.ini"
							Section="PHP" Value="&quot;[INSTALLDIR]ext&quot;" />
				</Component>
			</Directory>
			<Directory Id="extrasdirectory" Name="extras">
				<Component Id="magicMIME" DiskId="1" Guid="C29559A6-DE77-4951-A8F8-6266B790DE20">
					<File Id="filemagicMIME" Name="MAGIC_1.MIM" LongName="magic.mime" Source="Files\extras\magic.mime" />
				</Component>
				<Directory Id="openssldirectory" Name="openssl">
					<Component Id="opensslCNF" DiskId="1" Guid="0460291A-A0BA-4662-946E-3832BAB70881">
						<File Id="fileopensslCNF" Name="openssl.cnf" Source="Files\extras\openssl\openssl.cnf" />
					</Component>
					<Component Id="READMESSLTXT" DiskId="1" Guid="2E97C140-AFDA-4235-BF37-5F9FD6C52F57">
						<File Id="fileREADMESSLTXT" Name="README_1.TXT" LongName="README-SSL.txt" Source="Files\extras\openssl\README-SSL.txt" />
					</Component>
				</Directory>
				<Directory Id="pdfrelateddirectory" Name="PDF-RE_1" LongName="pdf-related">
					<Component Id="PDFSupport" DiskId="1" Guid="47C794BB-250C-4235-89A8-B192C994B98B">
						<File Id="filePDFSupport1" Name="COPYRI_1.TXT" LongName="copyright.txt" Source="Files\extras\pdf-related\copyright.txt" />
						<File Id="filePDFSupport2" Name="COURIE_1.AFM" LongName="Courier-Bold.afm" Source="Files\extras\pdf-related\Courier-Bold.afm" />
						<File Id="filePDFSupport3" Name="COURIE_2.AFM" LongName="Courier-BoldOblique.afm" Source="Files\extras\pdf-related\Courier-BoldOblique.afm" />
						<File Id="filePDFSupport4" Name="COURIE_3.AFM" LongName="Courier-Oblique.afm" Source="Files\extras\pdf-related\Courier-Oblique.afm" />
						<File Id="filePDFSupport5" Name="Courier.afm" Source="Files\extras\pdf-related\Courier.afm" />
						<File Id="filePDFSupport6" Name="cp1250.cpg" Source="Files\extras\pdf-related\cp1250.cpg" />
						<File Id="filePDFSupport7" Name="cp1250.enc" Source="Files\extras\pdf-related\cp1250.enc" />
						<File Id="filePDFSupport8" Name="cp1251.cpg" Source="Files\extras\pdf-related\cp1251.cpg" />
						<File Id="filePDFSupport9" Name="cp1252.cpg" Source="Files\extras\pdf-related\cp1252.cpg" />
						<File Id="filePDFSupport10" Name="cp1253.cpg" Source="Files\extras\pdf-related\cp1253.cpg" />
						<File Id="filePDFSupport11" Name="cp1254.cpg" Source="Files\extras\pdf-related\cp1254.cpg" />
						<File Id="filePDFSupport12" Name="cp1255.cpg" Source="Files\extras\pdf-related\cp1255.cpg" />
						<File Id="filePDFSupport13" Name="cp1256.cpg" Source="Files\extras\pdf-related\cp1256.cpg" />
						<File Id="filePDFSupport14" Name="cp1257.cpg" Source="Files\extras\pdf-related\cp1257.cpg" />
						<File Id="filePDFSupport15" Name="cp1258.cpg" Source="Files\extras\pdf-related\cp1258.cpg" />
						<File Id="filePDFSupport16" Name="HELVET_1.AFM" LongName="Helvetica-Bold.afm" Source="Files\extras\pdf-related\Helvetica-Bold.afm" />
						<File Id="filePDFSupport17" Name="HELVET_2.AFM" LongName="Helvetica-BoldOblique.afm" Source="Files\extras\pdf-related\Helvetica-BoldOblique.afm" />
						<File Id="filePDFSupport18" Name="HELVET_3.AFM" LongName="Helvetica-Oblique.afm" Source="Files\extras\pdf-related\Helvetica-Oblique.afm" />
						<File Id="filePDFSupport19" Name="HELVET_4.AFM" LongName="Helvetica.afm" Source="Files\extras\pdf-related\Helvetica.afm" />
						<File Id="filePDFSupport20" Name="ISO885_1.CPG" LongName="iso8859-10.cpg" Source="Files\extras\pdf-related\iso8859-10.cpg" />
						<File Id="filePDFSupport21" Name="ISO885_2.CPG" LongName="iso8859-13.cpg" Source="Files\extras\pdf-related\iso8859-13.cpg" />
						<File Id="filePDFSupport22" Name="ISO885_3.CPG" LongName="iso8859-14.cpg" Source="Files\extras\pdf-related\iso8859-14.cpg" />
						<File Id="filePDFSupport23" Name="ISO885_4.CPG" LongName="iso8859-15.cpg" Source="Files\extras\pdf-related\iso8859-15.cpg" />
						<File Id="filePDFSupport24" Name="ISO885_1.ENC" LongName="iso8859-15.enc" Source="Files\extras\pdf-related\iso8859-15.enc" />
						<File Id="filePDFSupport25" Name="ISEA77_1.CPG" LongName="iso8859-16.cpg" Source="Files\extras\pdf-related\iso8859-16.cpg" />
						<File Id="filePDFSupport26" Name="ISB090_1.CPG" LongName="iso8859-2.cpg" Source="Files\extras\pdf-related\iso8859-2.cpg" />
						<File Id="filePDFSupport27" Name="ISO885_2.ENC" LongName="iso8859-2.enc" Source="Files\extras\pdf-related\iso8859-2.enc" />
						<File Id="filePDFSupport28" Name="ISB490_1.CPG" LongName="iso8859-3.cpg" Source="Files\extras\pdf-related\iso8859-3.cpg" />
						<File Id="filePDFSupport29" Name="ISB890_1.CPG" LongName="iso8859-4.cpg" Source="Files\extras\pdf-related\iso8859-4.cpg" />
						<File Id="filePDFSupport30" Name="ISBC90_1.CPG" LongName="iso8859-5.cpg" Source="Files\extras\pdf-related\iso8859-5.cpg" />
						<File Id="filePDFSupport31" Name="ISB0A0_1.CPG" LongName="iso8859-6.cpg" Source="Files\extras\pdf-related\iso8859-6.cpg" />
						<File Id="filePDFSupport32" Name="ISB4A0_1.CPG" LongName="iso8859-7.cpg" Source="Files\extras\pdf-related\iso8859-7.cpg" />
						<File Id="filePDFSupport33" Name="ISB8A0_1.CPG" LongName="iso8859-8.cpg" Source="Files\extras\pdf-related\iso8859-8.cpg" />
						<File Id="filePDFSupport34" Name="ISBCA0_1.CPG" LongName="iso8859-9.cpg" Source="Files\extras\pdf-related\iso8859-9.cpg" />
						<File Id="filePDFSupport35" Name="ISO885_3.ENC" LongName="iso8859-9.enc" Source="Files\extras\pdf-related\iso8859-9.enc" />
						<File Id="filePDFSupport36" Name="lcdxsr.afm" Source="Files\extras\pdf-related\lcdxsr.afm" />
						<File Id="filePDFSupport37" Name="lcdxsr.pfa" Source="Files\extras\pdf-related\lcdxsr.pfa" />
						<File Id="filePDFSupport38" Name="pdflib.upr" Source="Files\extras\pdf-related\pdflib.upr" />
						<File Id="filePDFSupport39" Name="PRINT__1.PS" LongName="print_glyphs.ps" Source="Files\extras\pdf-related\print_glyphs.ps" />
						<File Id="filePDFSupport40" Name="Symbol.afm" Source="Files\extras\pdf-related\Symbol.afm" />
						<File Id="filePDFSupport41" Name="TIMES-_1.AFM" LongName="Times-Bold.afm" Source="Files\extras\pdf-related\Times-Bold.afm" />
						<File Id="filePDFSupport42" Name="TIMES-_2.AFM" LongName="Times-BoldItalic.afm" Source="Files\extras\pdf-related\Times-BoldItalic.afm" />
						<File Id="filePDFSupport43" Name="TIMES-_3.AFM" LongName="Times-Italic.afm" Source="Files\extras\pdf-related\Times-Italic.afm" />
						<File Id="filePDFSupport44" Name="TIMES-_4.AFM" LongName="Times-Roman.afm" Source="Files\extras\pdf-related\Times-Roman.afm" />
						<File Id="filePDFSupport45" Name="ZAPFDI_1.AFM" LongName="ZapfDingbats.afm" Source="Files\extras\pdf-related\ZapfDingbats.afm" />
					</Component>
				</Directory>
			</Directory>
		</DirectoryRef>
	</Fragment>
</Wix>
XML;
?>
<?php
$ExtensionsWXS = new DOMDocument;
$ExtensionsWXS->preserveWhiteSpace = false;
$ExtensionsWXS->loadXML($xmlsource);
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
