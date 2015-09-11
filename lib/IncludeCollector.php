<?php

namespace PhpInputAudit;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Exception;

/* IncludeCollector
 *
 * Collects filenames of all includes in the file.
 *
 * Can deal with string concatenation in the arguments.
 */

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

        try {
            $this->processInclude($this->evaluateArg($node->expr));
        } catch (Exception $e) {
            error_log(" => Cannot resolve include starting on line #".
                $node->getAttribute("startLine")." => Failing for now.");

            // TODO: resolve name from expression, variable or whatever. How? Not sure. Let's leave it for now.
        }
    }

    private function evaluateArg($expr)
    {
        if ($expr instanceof Node\Scalar\String_) {
            return $expr->value;
        }

        if ($expr instanceof Node\Expr\BinaryOp\Concat) {
            return $this->evaluateConcat($expr);
        }

        throw new Exception("Not sure how to deal with ".get_class($expr));
    }

    private function evaluateConcat($expr)
    {
        return $this->evaluateArg($expr->left).$this->evaluateArg($expr->right);
    }

    private function processInclude($file)
    {
        if (substr($file, 0, 1) == "/") {
            $this->found_includes[] = $file;

            return;
        }

        if (file_exists($file)) {
            $this->found_includes[] = getcwd()."/".$file;

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
