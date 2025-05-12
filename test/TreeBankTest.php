<?php

use olcaytaner\ParseTree\TreeBank;
use PHPUnit\Framework\TestCase;

class TreeBankTest extends TestCase
{
    public function testTreeBank()
    {
        $treeBank1 = new TreeBank("../trees");
        $this->assertEquals(5, $treeBank1->size());
        $this->assertEquals(30, $treeBank1->wordCount(true));
        $treeBank2 = new TreeBank("../trees2");
        $this->assertEquals(4, $treeBank2->size());
        $this->assertEquals(18, $treeBank2->wordCount(true));
    }
}