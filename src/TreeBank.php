<?php

namespace olcaytaner\ParseTree;

class TreeBank
{
    protected array $parseTrees = [];

    /**
     * A constructor of {@link TreeBank} class which reads all {@link ParseTree} files inside the given folder. For each
     * file inside that folder, the constructor creates a ParseTree and puts in inside the list parseTrees.
     * @param string|null $folder Folder where all parseTrees reside.
     * @param string|null $pattern File pattern such as "." ".train" ".test".
     */
    public function __construct(?string $folder = null, ?string $pattern = null)
    {
        $files = scandir($folder);
        foreach ($files as $file) {
            if ($pattern !== null){
                if (!str_contains($file, $pattern)) {
                    continue;
                }
            }
            $parseTree = new ParseTree($folder . "/" . $file);
            if ($parseTree->getRoot() !== null) {
                $parseTree->setName($file);
                $this->parseTrees[] = $parseTree;
            }
        }
    }

    /**
     * Strips punctuation symbols from all parseTrees in this TreeBank.
     */
    public function stripPunctuation(): void{
        foreach ($this->parseTrees as $parseTree) {
            $parseTree->stripPunctuation();
        }
    }

    /**
     * Returns number of trees in the TreeBank.
     * @return int Number of trees in the TreeBank.
     */
    public function size(): int{
        return count($this->parseTrees);
    }

    /**
     * Returns number of words in the parseTrees in the TreeBank. If excludeStopWords is true, stop words are not
     * counted.
     * @param bool $excludeStopWords If true, stop words are not included in the count process.
     * @return int Number of all words in all parseTrees in the TreeBank.
     */
    public function wordCount(bool $excludeStopWords): int{
        $count = 0;
        foreach ($this->parseTrees as $parseTree) {
            $count += $parseTree->wordCount($excludeStopWords);
        }
        return $count;
    }

    /**
     * Accessor for a single ParseTree.
     * @param int $index Index of the parseTree.
     * @return ParseTree|null The ParseTree at the given index.
     */
    public function get(int $index): ?ParseTree{
        return $this->parseTrees[$index] ?? null;
    }

    /**
     * Removes the parse tree at position index from the treebank.
     * @param int $index Position of the tree in the treebank.
     */
    public function removeTree(int $index): void{
        array_splice($this->parseTrees, $index, 1);
    }
}