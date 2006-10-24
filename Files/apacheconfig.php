<?php
// params APACHEVERSION, APACHEDIR
$path_parts = pathinfo(realpath($_SERVER['SCRIPT_FILENAME']));
$phpdir = $path_parts['dirname'];
$foldername = str_replace(array('"', ';'), array('',''), $argv[2]);
$apacheversion = $argv[1];

print_r($argv);
echo "$phpdir\n";

ini_set("error_log","{$phpdir}\installapache.err");

function windowsPopup($text,$title,$flags) {
	$wscript = new COM("WScript.Shell");
	return $wscript->Popup($text,0,$title,$flags);
}

function windowsGetFolder($title) {
	$folderdialog = new COM("Shell.Application");
	$foldername = $folderdialog->BrowseForFolder(0,$title, 0);
	
	if (is_null($foldername)) {
		return false;
	}
	return $foldername->Items->Item->path;
}

if ( $argc < 3 ) {
	windowsPopup("This is designed to be launched from the PHP Install. Exiting Now.","Apache Config",64);
	exit;
}

if ( windowsPopup("Do you want the installer to configure Apache?","Apache Config",36) == 7 ) {
	exit;
}

if ( empty($foldername) ) {
	$foldername = windowsGetFolder('Locate the Apache conf directory');
}
if ( $foldername === FALSE ) {
	windowsPopup("You will need to manually configure Apache.","Apache Config",16);
	exit;
}

// Build directives to write
$directivetowrite = "\n\n#BEGIN PHP INSTALLER EDITS - REMOVE ONLY ON UNINSTALL\n";
switch ($apacheversion) {
	case "CGI":
		$directivetowrite .= "ScriptAlias /php/ \"{$phpdir}\"\n";
		$directivetowrite .= "Action application/x-httpd-php \"{$phpdir}\php-cgi.exe\"\n";
		break;
	case "MODULE22":
		$directivetowrite .= "PHPIniDir \"{$phpdir}\\\"\n";
		$directivetowrite .= "LoadModule php5_module \"{$phpdir}\php5apache2_2.dll\"\n";
		break;	
	case "MODULE2":
		$directivetowrite .= "PHPIniDir \"{$phpdir}\\\"\n";
		$directivetowrite .= "LoadModule php5_module \"{$phpdir}\php5apache2.dll\"\n";
		break;
	case "MODULE1":
		$directivetowrite .= "PHPIniDir \"{$phpdir}\\\"\n";
		$directivetowrite .= "LoadModule php5_module \"{$phpdir}\php5apache.dll\"\nAddModule mod_php5.c\n";
		break;
	default:
		windowsPopup("Invalid option '{$apacheversion}'","Error editing Apache Config File",16);
}
$directivetowrite .= "#END PHP INSTALLER EDITS - REMOVE ONLY ON UNINSTALL\n";

// Update httpd.conf
$filename = $foldername . "\httpd.conf";
if (!file($filename)) {
	$filename = $foldername . "\conf\httpd.conf";
	if (!file($filename)) {
		windowsPopup("The file '$filename' is not found. You will need to manually configure Apache.","Apache Config",16);	
		exit;
	}
}
if (!is_writable($filename)) {
	windowsPopup("The file '$filename' is not writable. You will need to manually configure Apache.","Apache Config",16);
	exit;
}
elseif (!copy($filename,$foldername . "\httpd.conf.bak")) {
	windowsPopup("Cannot backup '$filename'. You will need to manually configure Apache.","Apache Config",16);
	exit;
}
elseif (!$handle = fopen($filename, 'a')) {
	windowsPopup("Cannot open '$filename'. You will need to manually configure Apache.","Apache Config",16);
	exit;
}
elseif (fwrite($handle, $directivetowrite) === FALSE) {
   windowsPopup("Cannot write to '$filename'. You will need to manually configure Apache.","Apache Config",16);
   exit;
}
else {
   fclose($handle);
   windowsPopup("Successfully updated '$filename'","Apache Config",64);
}

// Update mime.types
$directivetowrite = "application/x-httpd-php\tphp\n";
$directivetowrite .= "application/x-httpd-php-source\tphps\n";
$filename = $foldername . "\mime.types";
if (!file($filename)) {
	$filename = $foldername . "\conf\mime.types";
	if (!file($filename)) {
		windowsPopup("The file '$filename' is not found. You will need to manually configure Apache.","Apache Config",16);	
		exit;
	}
}
if (!is_writable($filename)) {
	windowsPopup("The file '$filename' is not writable. You will need to manually configure Apache.","Apache Config",16);
	exit;
}
elseif (!copy($filename,$foldername . "\mime.types.bak")) {
	windowsPopup("Cannot backup '$filename'. You will need to manually configure Apache.","Apache Config",16);
	exit;
}
elseif (!$handle = fopen($filename, 'a')) {
	windowsPopup("Cannot open '$filename'. You will need to manually configure Apache.","Apache Config",16);
	exit;
}
elseif (fwrite($handle, $directivetowrite) === FALSE) {
	windowsPopup("Cannot write to '$filename'. You will need to manually configure Apache.","Apache Config",16);
	exit;
}
else {
	fclose($handle);
	windowsPopup("Successfully updated '$filename'","Apache Config",64);
}
?>
