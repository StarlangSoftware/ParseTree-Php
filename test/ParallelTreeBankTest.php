<?php

use olcaytaner\ParseTree\ParallelTreeBank;
use PHPUnit\Framework\TestCase;

class ParallelTreeBankTest extends TestCase
{
    public function testParallelTreeBank(){
        $treeBank1 = new ParallelTreeBank("../trees", "../trees2");
        $this->assertEquals(3, $treeBank1->size());
    }
}