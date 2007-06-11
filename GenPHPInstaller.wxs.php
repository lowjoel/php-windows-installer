<?php
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
