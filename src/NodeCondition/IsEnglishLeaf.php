<?php

namespace olcaytaner\ParseTree\NodeCondition;

use olcaytaner\ParseTree\ParseNode;

class IsEnglishLeaf extends IsLeaf
{

    /**
     * Implemented node condition for English leaf node.
     * @param ParseNode $parseNode Checked node.
     * @return bool If the node is a leaf node and is not a dummy node, returns true; false otherwise.
     */
    public function satisfies(ParseNode $parseNode): bool
    {
        if (parent::satisfies($parseNode)) {
            $data = $parseNode->getData()->getName();
            $parentData = $parseNode->getParent()->getData()->getName();
            if (str_contains($data, '*') || ($data == "0" && $parentData == "-NONE-")) {
                return false;
            }
            return true;
        }
        return false;
    }
}