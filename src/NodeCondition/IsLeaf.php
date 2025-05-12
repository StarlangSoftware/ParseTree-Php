<?php

namespace olcaytaner\ParseTree\NodeCondition;

use olcaytaner\ParseTree\ParseNode;

class IsLeaf implements NodeCondition
{

    /**
     * Implemented node condition for the leaf node. If a node has no children it is a leaf node.
     * @param ParseNode $parseNode Checked node.
     * @return bool True if the input node is a leaf node, false otherwise.
     */
    public function satisfies(ParseNode $parseNode): bool
    {
        return count($parseNode->getChildren()) == 0;
    }
}