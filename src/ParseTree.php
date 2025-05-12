<?php

namespace olcaytaner\ParseTree;

use olcaytaner\ParseTree\NodeCondition\IsEnglishLeaf;

class ParseTree
{
    static array $sentenceLabels = ["SINV", "SBARQ", "SBAR", "SQ", "S"];

    protected ?ParseNode $root = null;
    protected string $name;

    /**
     * Basic constructor for a ParseTree. Initializes the root node with the input.
     * @param string|ParseNode|null $rootOrFileName Root node of the tree
     */
    public function __construct(string|ParseNode|null $rootOrFileName = null)
    {
        if ($rootOrFileName != null) {
            if ($rootOrFileName instanceof ParseNode) {
                $this->root = $rootOrFileName;
            } else {
                $data = file_get_contents($rootOrFileName);
                $line = explode("\n", $data)[0];
                if (str_contains($line, "(") && str_contains($line, ")")) {
                    $line = trim(mb_substr($line, mb_strpos($line, "(") + 1, mb_strrpos($line, ")") - mb_strpos($line, "(") - 1));
                    $this->root = new ParseNode(null, $line, false);
                }
            }
        }
    }

    /**
     * Accessor for the name attribute.
     * @return string Name of the parse tree.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Mutator for the name attribute.
     * @param string $name Name of the parse tree.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets the next leaf node after the given leaf node in the ParseTree.
     * @param ParseNode $parseNode ParseNode for which next node is calculated.
     * @return ParseNode|null Next leaf node after the given leaf node.
     */
    public function nextLeafNode(ParseNode $parseNode): ?ParseNode
    {
        $nodeCollector = new NodeCollector($this->root, new IsEnglishLeaf());
        $leafList = $nodeCollector->collect();
        for ($i = 0; $i < count($leafList) - 1; $i++) {
            if ($leafList[$i] === $parseNode) {
                return $leafList[$i + 1];
            }
        }
        return null;
    }

    /**
     * Gets the previous leaf node before the given leaf node in the ParseTree.
     * @param ParseNode $parseNode ParseNode for which previous node is calculated.
     * @return ParseNode|null Previous leaf node before the given leaf node.
     */
    public function previousLeafNode(ParseNode $parseNode): ?ParseNode
    {
        $nodeCollector = new NodeCollector($this->root, new IsEnglishLeaf());
        $leafList = $nodeCollector->collect();
        for ($i = 1; $i < count($leafList); $i++) {
            if ($leafList[$i] === $parseNode) {
                return $leafList[$i - 1];
            }
        }
        return null;
    }

    /**
     * Calls recursive method to calculate the number of all nodes, which have more than one children.
     * @return int Number of all nodes, which have more than one children.
     */
    public function nodeCountWithMultipleChildren(): int
    {
        return $this->root->nodeCountWithMultipleChildren();
    }

    /**
     * Calls recursive method to calculate the number of all nodes tree.
     * @return int Number of all nodes in the tree.
     */
    public function nodeCount(): int
    {
        return $this->root->nodeCount();
    }

    /**
     * Calls recursive method to calculate the number of all leaf nodes in the tree.
     * @return int Number of all leaf nodes in the tree.
     */
    public function leafCount(): int
    {
        return $this->root->leafCount();
    }

    /**
     * Checks if the sentence is a full sentence or not. A sentence is a full sentence is its root tag is S, SINV, etc.
     * @return bool True if the sentence is a full sentence, false otherwise.
     */
    public function isFullSentence(): bool
    {
        if ($this->root != null && in_array($this->root->getData()->getName(), ParseTree::$sentenceLabels)) {
            return true;
        }
        return false;
    }

    /**
     * Generates a list of constituents in the parse tree and their spans.
     * @return array A list of constituents in the parse tree and their spans.
     */
    public function constituentSpanList(): array
    {
        $result = [];
        if ($this->root != null) {
            $this->root->constituentSpanList(1, $result);
        }
        return $result;
    }

    /**
     * Calls recursive method to restore the parents of all nodes in the tree.
     */
    public function correctParents(): void
    {
        $this->root->correctParents();
    }

    /**
     * Calls recursive method to remove all punctuation nodes from the tree.
     */
    public function stripPunctuation(): void
    {
        $this->root->stripPunctuation();
    }

    /**
     * Accessor method for the root node.
     * @return ?ParseNode Root node
     */
    public function getRoot(): ?ParseNode
    {
        return $this->root;
    }

    /**
     * Calls recursive function to convert the tree to a string.
     * @return string A string which contains all words in the tree.
     */
    public function toString(): string
    {
        return $this->root->toString();
    }

    /**
     * Calls recursive function to convert the tree to a sentence.
     * @return string A sentence which contains all words in the tree.
     */
    public function toSentence(): string
    {
        return trim($this->root->toSentence());
    }

    /**
     * Calls recursive function to count the number of words in the tree.
     * @param bool $excludeStopWords If true, stop words are not counted.
     * @return int Number of words in the tree.
     */
    public function wordCount(bool $excludeStopWords): int
    {
        return $this->root->wordCount($excludeStopWords);
    }
}