<?php
$xmlFile = 'docx_content/word/document.xml';
if (!file_exists($xmlFile)) {
    die("File not found");
}
$content = file_get_contents($xmlFile);
$xml = new DOMDocument();
$xml->loadXML($content);
$text = '';
$paragraphs = $xml->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'p');
foreach ($paragraphs as $p) {
    $runs = $p->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'r');
    foreach ($runs as $r) {
        $texts = $r->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 't');
        foreach ($texts as $t) {
            $text .= $t->nodeValue;
        }
    }
    $text .= "\n";
}
echo $text;
