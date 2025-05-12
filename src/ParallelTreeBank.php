<?php

namespace olcaytaner\ParseTree;

class ParallelTreeBank
{
    protected TreeBank $fromTreeBank;
    protected TreeBank $toTreeBank;

    /**
     * Another constructor for the ParallelTreeBank class. A ParallelTreeBank consists of two treebanks, where each
     * sentence appears in both treebanks with possibly different tree structures. Each treebank is stored in a separate
     * folder. Both treebanks are read and distinct sentences are removed from the treebanks. In thid constructor, only
     * files matching the pattern are read. Pattern is used for matching the extensions such as .train, .test, .dev.
     * @param string|null $folder1 Folder containing the files for trees in the first treebank.
     * @param string|null $folder2 Folder containing the files for trees in the second treebank.
     * @param string|null $pattern File pattern used for matching. Patterns are usually used for setting the extensions such as
     *                .train, .test, .dev.
     */
    public function __construct(?string $folder1, ?string $folder2 = null, ?string $pattern = null){
        $this->fromTreeBank = new TreeBank($folder1, $pattern);
        $this->toTreeBank = new TreeBank($folder2, $pattern);
        $this->removeDifferentTrees();
    }

    /**
     * Given two treebanks read, the method removes the trees which do not exist in one of the treebanks. At the end,
     * we will only have the tree files that exist in both treebanks.
     */
    public function removeDifferentTrees(): void{
        $i = 0;
        $j = 0;
        while ($i < $this->fromTreeBank->size() && $j < $this->toTreeBank->size()){
            if ($this->fromTreeBank->get($i)->getName() < $this->toTreeBank->get($j)->getName()){
                $this->fromTreeBank->removeTree($i);
            } else {
                if ($this->fromTreeBank->get($i)->getName() > $this->toTreeBank->get($j)->getName()){
                    $this->toTreeBank->removeTree($j);
                } else {
                    $i++;
                    $j++;
                }
            }
        }
        while ($i < $this->fromTreeBank->size()){
            $this->fromTreeBank->removeTree($i);
        }
        while ($j < $this->toTreeBank->size()){
            $this->toTreeBank->removeTree($j);
        }
    }

    /**
     * Returns number of sentences in ParallelTreeBank.
     * @return int Number of sentences.
     */
    public function size(): int{
        return $this->fromTreeBank->size();
    }

    /**
     * Returns the tree at position index in the first treebank.
     * @param int $index Position of the tree in the first treebank.
     * @return ParseTree|null The tree at position index in the first treebank.
     */
    public function fromTree(int $index): ?ParseTree{
        return $this->fromTreeBank->get($index);
    }

    /**
     * Returns the tree at position index in the second treebank.
     * @param int $index Position of the tree in the second treebank.
     * @return ParseTree|null The tree at position index in the second treebank.
     */
    public function toTree(int $index): ?ParseTree{
        return $this->toTreeBank->get($index);
    }

    /**
     * Returns the first treebank.
     * @return TreeBank First treebank.
     */
    public function getFromTreeBank(): TreeBank{
        return $this->fromTreeBank;
    }

    /**
     * Returns the second treebank.
     * @return TreeBank Second treebank.
     */
    public function getToTreeBank(): TreeBank{
        return $this->toTreeBank;
    }
}