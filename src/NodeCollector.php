<?php

namespace olcaytaner\ParseTree;

use olcaytaner\ParseTree\NodeCondition\NodeCondition;

class NodeCollector
{
    private ?NodeCondition $condition;
    private ParseNode $rootNode;

    /**
     * Constructor for the NodeCollector class. NodeCollector's main aim is to collect a set of ParseNode's from a
     * subtree rooted at rootNode, where the ParseNode's satisfy a given NodeCondition, which is implemented by other
     * interface class.
     * @param ParseNode $rootNode Root node of the subtree
     * @param NodeCondition|null $condition The condition interface for which all nodes in the subtree rooted at rootNode will be checked
     */
    public function __construct(ParseNode $rootNode, ?NodeCondition $condition)
    {
        $this->condition = $condition;
        $this->rootNode = $rootNode;
    }

    /**
     * Private recursive method to check all descendants of the parseNode, if they ever satisfy the given node condition
     * @param ParseNode $parseNode Root node of the subtree
     * @param array $collected The {@link Array} where the collected ParseNode's will be stored.
     */
    private function collectNodes(ParseNode $parseNode, array &$collected): void
    {
        if ($this->condition === null || $this->condition->satisfies($parseNode)) {
            $collected[] = $parseNode;
        }
        for ($i = 0; $i < $parseNode->numberOfChildren(); $i++) {
            $this->collectNodes($parseNode->getChild($i), $collected);
        }
    }

    /**
     * Collects and returns all ParseNode's satisfying the node condition.
     * @return array All ParseNode's satisfying the node condition.
     */
    public function collect(): array{
        $result = [];
        $this->collectNodes($this->rootNode, $result);
        return $result;
    }
}