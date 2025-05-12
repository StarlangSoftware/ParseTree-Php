<?php

namespace olcaytaner\ParseTree;

use olcaytaner\Dictionary\Dictionary\Word;

class ParseNode
{
    protected array $children = [];
    protected ?ParseNode $parent = null;
    protected ?Symbol $data = null;

    static array $ADJP = ["NNS", "QP", "NN", "$", "ADVP", "JJ", "VBN", "VBG", "ADJP", "JJR", "NP", "JJS", "DT", "FW", "RBR", "RBS", "SBAR", "RB"];
    static array $ADVP = ["RB", "RBR", "RBS", "FW", "ADVP", "TO", "CD", "JJR", "JJ", "IN", "NP", "JJS", "NN"];
    static array $CONJP = ["CC", "RB", "IN"];
    static array $FRAG = [];
    static array $INTJ = [];
    static array $LST = ["LS", ":"];
    static array $NAC = ["NN", "NNS", "NNP", "NNPS", "NP", "NAC", "EX", "$", "CD", "QP", "PRP", "VBG", "JJ", "JJS", "JJR", "ADJP", "FW"];
    static array $PP = ["IN", "TO", "VBG", "VBN", "RP", "FW"];
    static array $PRN = [];
    static array $PRT = ["RP"];
    static array $QP = ["$", "IN", "NNS", "NN", "JJ", "RB", "DT", "CD", "NCD", "QP", "JJR", "JJS"];
    static array $RRC = ["VP", "NP", "ADVP", "ADJP", "PP"];
    static array $S = ["TO", "IN", "VP", "S", "SBAR", "ADJP", "UCP", "NP"];
    static array $SBAR = ["WHNP", "WHPP", "WHADVP", "WHADJP", "IN", "DT", "S", "SQ", "SINV", "SBAR", "FRAG"];
    static array $SBARQ = ["SQ", "S", "SINV", "SBARQ", "FRAG"];
    static array $SINV = ["VBZ", "VBD", "VBP", "VB", "MD", "VP", "S", "SINV", "ADJP", "NP"];
    static array $SQ = ["VBZ", "VBD", "VBP", "VB", "MD", "VP", "SQ"];
    static array $UCP = [];
    static array $VP = ["TO", "VBD", "VBN", "MD", "VBZ", "VB", "VBG", "VBP", "VP", "ADJP", "NN", "NNS", "NP"];
    static array $WHADJP = ["CC", "WRB", "JJ", "ADJP"];
    static array $WHADVP = ["CC", "WRB"];
    static array $WHNP = ["WDT", "WP", "WP$", "WHADJP", "WHPP", "WHNP"];
    static array $WHPP = ["IN", "TO", "FW"];
    static array $NP1 = ["NN", "NNP", "NNPS", "NNS", "NX", "POS", "JJR"];
    static array $NP2 = ["NP"];
    static array $NP3 = ["$", "ADJP", "PRN"];
    static array $NP4 = ["CD"];
    static array $NP5 = ["JJ", "JJS", "RB", "QP"];

    private function constructor1(Symbol $data, ParseNode $left, ParseNode $right): void
    {
        $this->data = $data;
        $this->children[] = $left;
        $left->parent = $this;
        $this->children[] = $right;
        $right->parent = $this;
    }

    private function constructor2(Symbol $data, ParseNode $left): void
    {
        $this->data = $data;
        $this->children[] = $left;
        $left->parent = $this;
    }

    private function constructor3(Symbol $data): void
    {
        $this->data = $data;
    }

    private function constructor4(?ParseNode $parent, string $line, bool $isLeaf): void
    {
        $parenthesisCount = 0;
        $childLine = "";
        $this->parent = $parent;
        if ($isLeaf) {
            $this->data = new Symbol($line);
        } else {
            $this->data = new Symbol(mb_substr($line, 1, mb_strpos($line, " ") - 1));
            if (mb_strpos($line, " ") === mb_strrpos($line, " ")) {
                $this->children[] = new ParseNode($this, mb_substr($line, mb_strpos($line, " ") + 1, mb_strrpos($line, ")") - mb_strpos($line, " ") - 1), true);
            } else {
                for ($i = mb_strpos($line, " ") + 1; $i < mb_strlen($line); $i++) {
                    if (mb_substr($line, $i, 1) != " " || $parenthesisCount > 0) {
                        $childLine .= mb_substr($line, $i, 1);
                    }
                    if (mb_substr($line, $i, 1) === "(") {
                        $parenthesisCount++;
                    } else {
                        if (mb_substr($line, $i, 1) === ")") {
                            $parenthesisCount--;
                        }
                    }
                    if ($parenthesisCount === 0 && $childLine !== "") {
                        $this->children[] = new ParseNode($this, trim($childLine), false);
                        $childLine = "";
                    }
                }
            }
        }
    }

    public function __construct($dataOrParent, $leftOrLine = null, $rightOrIsLeaf = null)
    {
        $this->children = [];
        $this->parent = null;
        $this->data = null;
        if ($dataOrParent instanceof Symbol) {
            $this->constructor3($dataOrParent);
        } else {
            if ($rightOrIsLeaf === null) {
                $this->constructor2($dataOrParent, $leftOrLine);
            } else {
                if ($rightOrIsLeaf instanceof ParseNode) {
                    $this->constructor1($dataOrParent, $leftOrLine, $rightOrIsLeaf);
                } else {
                    $this->constructor4($dataOrParent, $leftOrLine, $rightOrIsLeaf);
                }
            }
        }
    }

    /**
     * Extracts the head of the children of this current node.
     * @param array $priorityList Depending on the pos of current node, the priorities among the children are given with this parameter
     * @param SearchDirectionType $direction Depending on the pos of the current node, search direction is either from left to right, or from
     *                  right to left.
     * @param bool $defaultCase If true, and no child appears in the priority list, returns first child on the left, or first
     *                    child on the right depending on the search direction.
     * @return ?ParseNode Head node of the children of the current node
     */
    private function searchHeadChild(array $priorityList, SearchDirectionType $direction, bool $defaultCase): ?ParseNode
    {
        switch ($direction) {
            case SearchDirectionType::LEFT:
                foreach ($priorityList as $item) {
                    foreach ($this->children as $child) {
                        if ($child->getData()->trimSymbol()->getName() == $item) {
                            return $child;
                        }
                    }
                }
                if ($defaultCase) {
                    return $this->firstChild();
                }
                break;
            case SearchDirectionType::RIGHT:
                foreach ($priorityList as $item) {
                    for ($j = count($this->children) - 1; $j >= 0; $j--) {
                        $child = $this->children[$j];
                        if ($child->getData()->trimSymbol()->getName() == $item) {
                            return $child;
                        }
                    }
                }
                if ($defaultCase) {
                    return $this->lastChild();
                }
        }
        return null;
    }

    /**
     * If current node is not a leaf, it has one or more children, this method determines recursively the head of
     * that (those) child(ren). Otherwise, it returns itself. In this way, this method returns the head of all leaf
     * successors.
     * @return ?ParseNode Head node of the descendant leaves of this current node.
     */
    public function headLeaf(): ?ParseNode
    {
        if (count($this->children) > 0) {
            $head = $this->headChild();
            if ($head !== null) {
                return $head->headLeaf();
            } else {
                return null;
            }
        } else {
            return $this;
        }
    }

    /**
     * Calls searchHeadChild to determine the head node of all children of this current node. The search direction and
     * the search priority list is determined according to the symbol in this current parent node.
     * @return ?ParseNode Head node among its children of this current node.
     */
    public function headChild(): ?ParseNode
    {
        switch ($this->data->trimSymbol()->getName()) {
            case "ADJP":
                return $this->searchHeadChild(self::$ADJP, SearchDirectionType::LEFT, true);
            case "ADVP":
                return $this->searchHeadChild(self::$ADVP, SearchDirectionType::RIGHT, true);
            case "CONJP":
                return $this->searchHeadChild(self::$CONJP, SearchDirectionType::RIGHT, true);
            case "FRAG":
                return $this->searchHeadChild(self::$FRAG, SearchDirectionType::RIGHT, true);
            case "INTJ":
                return $this->searchHeadChild(self::$INTJ, SearchDirectionType::LEFT, true);
            case "LST":
                return $this->searchHeadChild(self::$LST, SearchDirectionType::RIGHT, true);
            case "NAC":
                return $this->searchHeadChild(self::$NAC, SearchDirectionType::LEFT, true);
            case "PP":
                return $this->searchHeadChild(self::$PP, SearchDirectionType::RIGHT, true);
            case "PRN":
                return $this->searchHeadChild(self::$PRN, SearchDirectionType::LEFT, true);
            case "PRT":
                return $this->searchHeadChild(self::$PRT, SearchDirectionType::RIGHT, true);
            case "QP":
                return $this->searchHeadChild(self::$QP, SearchDirectionType::LEFT, true);
            case "RRC":
                return $this->searchHeadChild(self::$RRC, SearchDirectionType::RIGHT, true);
            case "S":
                return $this->searchHeadChild(self::$S, SearchDirectionType::LEFT, true);
            case "SBAR":
                return $this->searchHeadChild(self::$SBAR, SearchDirectionType::LEFT, true);
            case "SBARQ":
                return $this->searchHeadChild(self::$SBARQ, SearchDirectionType::LEFT, true);
            case "SINV":
                return $this->searchHeadChild(self::$SINV, SearchDirectionType::LEFT, true);
            case "SQ":
                return $this->searchHeadChild(self::$SQ, SearchDirectionType::LEFT, true);
            case "UCP":
                return $this->searchHeadChild(self::$UCP, SearchDirectionType::RIGHT, true);
            case "VP":
                return $this->searchHeadChild(self::$VP, SearchDirectionType::LEFT, true);
            case "WHADJP":
                return $this->searchHeadChild(self::$WHADJP, SearchDirectionType::LEFT, true);
            case "WHADVP":
                return $this->searchHeadChild(self::$WHADVP, SearchDirectionType::RIGHT, true);
            case "WHNP":
                return $this->searchHeadChild(self::$WHNP, SearchDirectionType::LEFT, true);
            case "WHPP":
                return $this->searchHeadChild(self::$WHPP, SearchDirectionType::RIGHT, true);
            case "NP":
                if ($this->lastChild() . getData() . getName() == "POS") {
                    return $this->lastChild();
                } else {
                    $result = $this->searchHeadChild(self::$NP1, SearchDirectionType::RIGHT, false);
                    if ($result !== null) {
                        return $result;
                    } else {
                        $result = $this->searchHeadChild(self::$NP2, SearchDirectionType::LEFT, false);
                        if ($result !== null) {
                            return $result;
                        } else {
                            $result = $this->searchHeadChild(self::$NP3, SearchDirectionType::RIGHT, false);
                            if ($result !== null) {
                                return $result;
                            } else {
                                $result = $this->searchHeadChild(self::$NP4, SearchDirectionType::RIGHT, false);
                                if ($result !== null) {
                                    return $result;
                                } else {
                                    $result = $this->searchHeadChild(self::$NP5, SearchDirectionType::RIGHT, false);
                                    if ($result !== null) {
                                        return $result;
                                    } else {
                                        return $this->lastChild();
                                    }
                                }
                            }
                        }
                    }
                }
        }
        return null;
    }

    /**
     * Returns an iterator for the child nodes of this {@link ParseNode}.
     * @return array Iterator for the children of thid very node.
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Adds a child node at the end of the children node list.
     * @param ParseNode $child Child node to be added.
     * @param ?int $index Index where the new child node will be added.
     */
    public function addChild(ParseNode $child, ?int $index = null): void
    {
        if ($index === null) {
            $this->children[] = $child;
        } else {
            array_splice($this->children, $index, 0, [$child]);
        }
        $child->parent = $this;
    }

    /**
     * Recursive method to restore the parents of all nodes below this node in the hierarchy.
     */
    public function correctParents(): void
    {
        foreach ($this->children as $child) {
            $child->parent = $this;
            $child->correctParents();
        }
    }

    /**
     * Replaces a child node at the given specific with a new child node.
     * @param int $index Index where the new child node replaces the old one.
     * @param ParseNode $child Child node to be replaced.
     */
    public function setChild(int $index, ParseNode $child): void
    {
        array_splice($this->children, $index, 0, [$child]);
    }

    /**
     * Removes a given child from children node list.
     * @param ParseNode $child Child node to be deleted.
     */
    public function removeChild(ParseNode $child): void
    {
        for ($i = 0; $i < count($this->children); $i++) {
            if ($this->children[$i] === $child) {
                array_splice($this->children, $i, 1);
                break;
            }
        }
    }

    /**
     * Recursive method to calculate the number of all leaf nodes in the subtree rooted with this current node.
     * @return int Number of all leaf nodes in the current subtree.
     */
    public function leafCount(): int
    {
        if (count($this->children) == 0) {
            return 1;
        } else {
            $sum = 0;
            foreach ($this->children as $child) {
                $sum += $child->leafCount();
            }
            return $sum;
        }
    }

    /**
     * Recursive method to calculate the number of all nodes in the subtree rooted with this current node.
     * @return int Number of all nodes in the current subtree.
     */
    public function nodeCount(): int
    {
        if (count($this->children) > 0) {
            $sum = 1;
            foreach ($this->children as $child) {
                $sum += $child->nodeCount();
            }
            return $sum;
        } else {
            return 1;
        }
    }

    /**
     * Recursive method to calculate the number of all nodes, which have more than one children, in the subtree rooted
     * with this current node.
     * @return int Number of all nodes, which have more than one children, in the current subtree.
     */
    public function nodeCountWithMultipleChildren(): int
    {
        if (count($this->children) > 1) {
            $sum = 1;
            foreach ($this->children as $child) {
                $sum += $child->nodeCountWithMultipleChildren();
            }
            return $sum;
        } else {
            return 0;
        }
    }

    /**
     * Recursive method to remove all punctuation nodes from the current subtree.
     */
    public function stripPunctuation(): void
    {
        for ($i = 0; $i < count($this->children); $i++) {
            $node = $this->children[$i];
            if (Word::isPunctuationSymbol($node->getData()->getName())) {
                array_splice($this->children, $i, 1);
                $i--;
            }
        }
        foreach ($this->children as $child) {
            $child->stripPunctuation();
        }
    }

    /**
     * Returns number of children of this node.
     * @return int Number of children of this node.
     */
    public function numberOfChildren(): int
    {
        return count($this->children);
    }

    /**
     * Returns the i'th child of this node.
     * @param int $index Index of the retrieved node.
     * @return ParseNode|null i'th child of this node.
     */
    public function getChild(int $index): ?ParseNode
    {
        return $this->children[$index] ?? null;
    }

    /**
     * Returns the first child of this node.
     * @return ParseNode|null First child of this node.
     */
    public function firstChild(): ?ParseNode
    {
        return $this->children[0] ?? null;
    }

    /**
     * Returns the last child of this node.
     * @return ParseNode|null Last child of this node.
     */
    public function lastChild(): ?ParseNode
    {
        return $this->children[count($this->children) - 1] ?? null;
    }

    /**
     * Checks if the given node is the last child of this node.
     * @param ParseNode $child To be checked node.
     * @return bool True, if child is the last child of this node, false otherwise.
     */
    public function isLastChild(ParseNode $child): bool
    {
        return $this->children[count($this->children) - 1] === $child;
    }

    /**
     * Returns the index of the given child of this node.
     * @param ParseNode $child Child whose index shoud be returned.
     * @return int Index of the child of this node.
     */
    public function getChildIndex(ParseNode $child): int
    {
        return array_search($child, $this->children, true);
    }

    /**
     * Returns true if the given node is a descendant of this node.
     * @param ParseNode $node Node to check if it is descendant of this node.
     * @return bool True if the given node is descendant of this node.
     */
    public function isDescendant(ParseNode $node): bool
    {
        foreach ($this->children as $child) {
            if ($child === $node) {
                return true;
            } else {
                if ($child->isDescendant($node)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns the previous sibling (sister) of this node.
     * @return ?ParseNode If this is the first child of its parent, returns null. Otherwise, returns the previous sibling of this
     * node.
     */
    public function previousSibling(): ?ParseNode
    {
        for ($i = 1; $i < count($this->children); $i++) {
            if ($this->parent->children[$i] === $this) {
                return $this->children[$i - 1];
            }
        }
        return null;
    }

    /**
     * Returns the next sibling (sister) of this node.
     * @return ?ParseNode If this is the last child of its parent, returns null. Otherwise, returns the next sibling of this
     * node.
     */
    public function nextSibling(): ?ParseNode
    {
        for ($i = 0; $i < count($this->children) - 1; $i++) {
            if ($this->children[$i] === $this) {
                return $this->children[$i + 1];
            }
        }
        return null;
    }

    /**
     * Accessor for the parent attribute.
     * @return ?ParseNode Parent of this node.
     */
    public function getParent(): ?ParseNode
    {
        return $this->parent;
    }

    /**
     * Accessor for the data attribute.
     * @return ?Symbol Data of this node.
     */
    public function getData(): ?Symbol
    {
        return $this->data;
    }

    /**
     * Mutator of the data attribute.
     * @param ?Symbol $data Data to be set.
     */
    public function setData(?Symbol $data): void
    {
        $this->data = $data;
    }

    /**
     * Recursive function to count the number of words in the subtree rooted at this node.
     * @param bool $excludeStopWords If true, stop words are not counted.
     * @return int Number of words in the subtree rooted at this node.
     */
    public function wordCount(bool $excludeStopWords): int
    {
        if (count($this->children) == 0) {
            if (!$excludeStopWords) {
                $sum = 1;
            } else {
                $lowerCase = mb_strtolower($this->data->getName(), 'utf-8');
                if (Word::isPunctuationSymbol($this->data->getName())
                    || str_contains($this->data->getName(), "*") || $lowerCase == "at" || $lowerCase == "the"
                    || $lowerCase == "to" || $lowerCase == "a" || $lowerCase == "an"
                    || $lowerCase == "not" || $lowerCase == "is" || $lowerCase == "was"
                    || $lowerCase == "were" || $lowerCase == "have" || $lowerCase == "had"
                    || $lowerCase == "has"
                    || $lowerCase == "by" || $lowerCase == "on"
                    || $lowerCase == "off" || $lowerCase == "'s" || $lowerCase == "n't"
                    || $lowerCase == "can" || $lowerCase == "could" || $lowerCase == "may"
                    || $lowerCase == "might" || $lowerCase == "as" || $lowerCase == "with"
                    || $lowerCase == "for" || $lowerCase == "will" || $lowerCase == "would"
                    || $lowerCase == "than" || $lowerCase == "$"
                    || $lowerCase == "and" || $lowerCase == "or" || $lowerCase == "of"
                    || $lowerCase == "are" || $lowerCase == "be" || $lowerCase == "been"
                    || $lowerCase == "do" || $lowerCase == "few" || $lowerCase == "there"
                    || $lowerCase == "up" || $lowerCase == "down" || $lowerCase == "in"
                    || $lowerCase == "'re") {
                    $sum = 0;
                } else {
                    $sum = 1;
                }
            }
        } else {
            $sum = 0;
        }
        foreach ($this->children as $child) {
            $sum += $child->wordCount($excludeStopWords);
        }
        return $sum;
    }

    /**
     * Construct recursively the constituent span list of a subtree rooted at this node.
     * @param int $startIndex Start index of the leftmost leaf node of this subtree.
     * @param array $list Returned span list.
     */
    public function constituentSpanList(int $startIndex, array $list): void
    {
        if (count($this->children) > 0) {
            $list[] = new ConstituentSpan($this->data, $startIndex, $startIndex + $this->leafCount());
        }
        $total = 0;
        foreach ($this->children as $child) {
            $child->constituentSpanList($startIndex + $total, $list);
            $total += $child->leafCount();
        }
    }

    /**
     * Returns true if this node is leaf, false otherwise.
     * @return bool true if this node is leaf, false otherwise.
     */
    public function isLeaf(): bool
    {
        return count($this->children) == 0;
    }

    /**
     * Returns true if this node does not contain a meaningful data, false otherwise.
     * @return bool true if this node does not contain a meaningful data, false otherwise.
     */
    public function isDummyNode(): bool
    {
        return str_contains($this->getData()->getName(), "*") || ($this->getData()->getName() == "0" &&
                $this->parent->getData()->getName() == "-NONE-");
    }

    /**
     * Recursive function to convert the subtree rooted at this node to a sentence.
     * @return string A sentence which contains all words in the subtree rooted at this node.
     */
    public function toSentence(): string
    {
        if (count($this->children) == 0) {
            if ($this->data !== null && !$this->isDummyNode()) {
                return " " . str_replace("-rcb-", "}", str_replace("-lcb-", "{", str_replace("-rsb-", "]", str_replace("-rrb-", ")", str_replace("-lsb-", "[", str_replace("-lrb-", "(", str_replace("-RCB-", "}", str_replace("-LCB-", "{", str_replace("-RSB-", "]", str_replace("-LSB-", "[", str_replace("-RRB-", ")", str_replace("-LRB-", "(", $this->getData()->getName()))))))))))));
            } else {
                if ($this->isDummyNode()) {
                    return "";
                } else {
                    return " ";
                }
            }
        } else {
            $st = "";
            foreach ($this->children as $child) {
                $st .= $child->toSentence();
            }
            return $st;
        }
    }

    /**
     * Recursive function to convert the subtree rooted at this node to a string.
     * @return string A string which contains all words in the subtree rooted at this node.
     */
    public function toString(): string
    {
        if (count($this->children) < 2) {
            if (count($this->children) < 1) {
                return $this->getData()->getName();
            } else {
                return "(" . $this->getData()->getName() . " " . $this->firstChild()->toString() . ")";
            }
        } else {
            $st = "(" . $this->getData()->getName();
            foreach ($this->children as $child) {
                $st .= " " . $child->toString();
            }
            return $st . ") ";
        }
    }

    /**
     * Swaps the given child node of this node with the previous sibling of that given node. If the given node is the
     * leftmost child, it swaps with the last node.
     * @param ParseNode $node Node to be swapped.
     */
    public function moveLeft(ParseNode $node): void
    {
        for ($i = 0; $i < count($this->children); $i++) {
            if ($this->children[$i] === $node) {
                if ($i == 0) {
                    $tmp = $this->children[0];
                    $this->children[0] = $this->children[count($this->children) - 1];
                    $this->children[count($this->children) - 1] = $tmp;
                } else {
                    $tmp = $this->children[$i];
                    $this->children[$i] = $this->children[($i - 1) % count($this->children)];
                    $this->children[count($this->children) - 1] = $tmp;
                }
                return;
            }
        }
        foreach ($this->children as $child) {
            $child->moveLeft($node);
        }
    }

    /**
     * Recursive function to concatenate the data of the all ascendant nodes of this node to a string.
     * @return string A string which contains all data of all the ascendant nodes of this node.
     */
    public function ancestorString(): string
    {
        if ($this->parent === null) {
            return $this->data->getName();
        } else {
            return $this->parent->ancestorString() . $this->data->getName();
        }
    }

    /**
     * Swaps the given child node of this node with the next sibling of that given node. If the given node is the
     * rightmost child, it swaps with the first node.
     * @param ParseNode $node Node to be swapped.
     */
    public function moveRight(ParseNode $node): void
    {
        for ($i = 0; $i < count($this->children); $i++) {
            if ($this->children[$i] === $node) {
                if ($i == count($this->children) - 1) {
                    $tmp = $this->children[0];
                    $this->children[0] = $this->children[count($this->children) - 1];
                    $this->children[count($this->children) - 1] = $tmp;
                } else {
                    $tmp = $this->children[$i];
                    $this->children[$i] = $this->children[($i + 1) % count($this->children)];
                    $this->children[count($this->children) - 1] = $tmp;
                }
            }
        }
        foreach ($this->children as $child) {
            $child->moveRight($node);
        }
    }
}