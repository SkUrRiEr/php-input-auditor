<?php

namespace PhpInputAudit;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class IncludeCollector extends NodeVisitorAbstract
{
    private $found_includes;
    private $cwd;

    public function __construct()
    {
        $this->found_includes = array();
    }

    public function newFile($filename)
    {
        $this->found_includes = array();
        $this->cwd = dirname($filename);
    }

    public function getFoundIncludes()
    {
        return $this->found_includes;
    }

    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Expr\Include_) {
            return;
        }

        if ($node->expr instanceof Node\Scalar\String_) {
            $this->processInclude($node->expr->value);

            return;
        }

        error_log(" => Cannot resolve non-string include starting on line #".
            $node->getAttribute("startLine")." => Failing for now.");

        // TODO: resolve name from expression, variable or whatever. How? Not sure. Let's leave it for now.
    }

    private function processInclude($file)
    {
        if (substr($file, 0, 1) == "/" || file_exists($file)) {
            $this->found_includes[] = $file;

            return;
        }

        if (file_exists($this->cwd."/".$file)) {
            $this->found_includes[] = $this->cwd."/".$file;

            return;
        }

        error_log(" => ".$file." is a system include, skipping for now.");

        // TODO: get from include path
    }
}
