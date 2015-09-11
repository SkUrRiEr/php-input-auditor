#!/usr/bin/php5
<?php

require("PhpParser/bootstrap.php");
require("lib/IncludeCollector.php");

ini_set("xdebug.max_nesting_level", 3000);

// Disable XDebug var_dump() output truncation
ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("xdebug.var_display_max_depth", -1);

$d = dir(".");

$files = array();

while ($item = $d->read()) {
    if (preg_match("/\.php$/", $item)) {
        $files[] = $item;
    }
}

$d->close();

if (empty($files)) {
    die("Please run this script in a directory containing at least one PHP file.");
}

$lexer = new PhpParser\Lexer\Emulative(array("usedAttributes" => array(
    "startLine", "endLine", "startFilePos", "endFilePos"
)));
$parser = new PhpParser\Parser($lexer);

$traverser = new PhpParser\NodeTraverser();
$traverser->addVisitor(new PhpParser\NodeVisitor\NameResolver);

$include_collector = new PhpInputAudit\IncludeCollector();
$traverser->addVisitor($include_collector);

for ($i = 0; $i < count($files); $i++) {
    $file = $files[$i];

    $include_collector->newFile($file);

    $code = file_get_contents($file);
    echo "====> File $file:\n";

    try {
        $stmts = $parser->parse($code);
    } catch (PhpParser\Error $e) {
        if ($attributes["with-column-info"] && $e->hasColumnInfo()) {
            $startLine = $e->getStartLine();
            $endLine = $e->getEndLine();
            $startColumn = $e->getStartColumn($code);
            $endColumn   = $e->getEndColumn($code);
            $message .= $e->getRawMessage()." from ".$startLine.":".$startColumn." to ".$endLine.":".$endColumn;
        } else {
            $message = $e->getMessage();
        }

        die($message."\n");
    }

//    print_r($stmts);
//    exit;

    $stmts = $traverser->traverse($stmts);

    foreach ($include_collector->getFoundIncludes() as $f) {
        if (!array_search($f, $files)) {
            $files[] = $f;
        }
    }
}
