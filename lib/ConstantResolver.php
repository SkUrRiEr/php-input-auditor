<?php

namespace PhpInputAudit;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/* ConstantResolver
 *
 * This turns constants into their expression
 *
 * It does not yet delete the define() calls from the node tree.
 */

class ConstantResolver extends NodeVisitorAbstract
{
    private $found_constants;
    private $missing_constant_found;
    private $has_resolved_constant;
    const IGNORE = array(
        "true", "TRUE", "false", "FALSE", "null", "NULL"
    );

    public function __construct()
    {
        $this->found_constants = array();

        $this->clearFlags();
    }

    public function foundMissingConstant()
    {
        return $this->missing_constant_found;
    }

    public function hasResolvedConstant()
    {
        return $this->has_resolved_constant;
    }

    public function clearFlags()
    {
        $this->missing_constant_found = false;
        $this->has_resolved_constant = false;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\ConstFetch) {
            if (!$node->name instanceof Node\Name) {
                print_r($node);

                // TODO: What happens here?

                error_log(" => No idea how to deal with this.");

                return;
            }

            $name = $node->name->toString();

            if (array_search($name, self::IGNORE) !== false) {
                return;
            }

            if (!isset($this->found_constants[$name])) {
                $this->missing_constant_found = true;

                return;
            }

            $this->has_resolved_constant = true;

            return $this->found_constants[$name];
        }

        if ($node instanceof Node\Expr\FuncCall) {
            if (!$node->name instanceof Node\Name) {
                if (!$node->name instanceof Node\Expr\Variable) {
                    error_log(" => Non-variable function name => CAN THIS HAPPEN?.");
                    print_r($node);
                }

                return;
            }

            if ($node->name->toString() != "define") {
                return;
            }

            if (count($node->args) < 2) {
                error_log(" => define() call at line #".
                    $node->getAttribute("startLine")." has too few arguments.");

                return;
            }

            if (!$node->args[0]->value instanceof Node\Scalar\String_) {
                error_log(" => name of define at line #".
                    $node->getAttribute("startLine")." is not a string.");

                return;
            }

            $this->found_constants[$node->args[0]->value->value] = $node->args[1]->value;
        }
    }
}
