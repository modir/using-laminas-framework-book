<?php
require "vendor/autoload.php";

#$Parsedown = new Parsedown();
$Parsedown = new ParsedownExtra(); # Parsedown Extra handles code blocks etc better

$template = file_get_contents('./template/template.html');

// output all files and directories except for '.' and '..'
foreach (new DirectoryIterator('./manuscript/en/') as $fileInfo) {
    if($fileInfo->isDot()) continue;
    if (!$fileInfo->isDir()) {
	    $content = file_get_contents('./manuscript/en/' . $fileInfo->getFilename());
	    $newFilename = substr_replace($fileInfo->getFilename() , 'html', strrpos($fileInfo->getFilename() , '.') +1);
        $fullPage = str_replace("###CONTENT###", $Parsedown->text($content), $template);
	    file_put_contents('./book/en/' . $newFilename, $fullPage);

    }
}
