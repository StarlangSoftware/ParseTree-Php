<?php

use olcaytaner\ParseTree\NodeCollector;
use olcaytaner\ParseTree\NodeCondition\IsEnglishLeaf;
use olcaytaner\ParseTree\NodeCondition\IsLeaf;
use olcaytaner\ParseTree\ParseTree;
use PHPUnit\Framework\TestCase;

class NodeCollectorTest extends TestCase
{
    public function testCollectLeaf()
    {
        $parseTree1 = new ParseTree("../trees/0000.dev");
        $parseTree2 = new ParseTree("../trees/0001.dev");
        $parseTree3 = new ParseTree("../trees/0002.dev");
        $parseTree4 = new ParseTree("../trees/0003.dev");
        $parseTree5 = new ParseTree("../trees/0014.dev");
        $nodeCollector1 = new NodeCollector($parseTree1->getRoot(), new IsLeaf());
        $this->assertCount(13, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree2->getRoot(), new IsLeaf());
        $this->assertCount(15, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree3->getRoot(), new IsLeaf());
        $this->assertCount(10, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree4->getRoot(), new IsLeaf());
        $this->assertCount(10, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree5->getRoot(), new IsLeaf());
        $this->assertCount(4, $nodeCollector1->collect());
    }

    public function testCollectNode()
    {
        $parseTree1 = new ParseTree("../trees/0000.dev");
        $parseTree2 = new ParseTree("../trees/0001.dev");
        $parseTree3 = new ParseTree("../trees/0002.dev");
        $parseTree4 = new ParseTree("../trees/0003.dev");
        $parseTree5 = new ParseTree("../trees/0014.dev");
        $nodeCollector1 = new NodeCollector($parseTree1->getRoot(), null);
        $this->assertCount(34, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree2->getRoot(), null);
        $this->assertCount(39, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree3->getRoot(), null);
        $this->assertCount(32, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree4->getRoot(), null);
        $this->assertCount(28, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree5->getRoot(), null);
        $this->assertCount(9, $nodeCollector1->collect());
    }

    public function testCollectEnglish()
    {
        $parseTree1 = new ParseTree("../trees/0000.dev");
        $parseTree2 = new ParseTree("../trees/0001.dev");
        $parseTree3 = new ParseTree("../trees/0002.dev");
        $parseTree4 = new ParseTree("../trees/0003.dev");
        $parseTree5 = new ParseTree("../trees/0014.dev");
        $nodeCollector1 = new NodeCollector($parseTree1->getRoot(), new IsEnglishLeaf());
        $this->assertCount(13, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree2->getRoot(), new IsEnglishLeaf());
        $this->assertCount(15, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree3->getRoot(), new IsEnglishLeaf());
        $this->assertCount(9, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree4->getRoot(), new IsEnglishLeaf());
        $this->assertCount(10, $nodeCollector1->collect());
        $nodeCollector1 = new NodeCollector($parseTree5->getRoot(), new IsEnglishLeaf());
        $this->assertCount(4, $nodeCollector1->collect());
    }

}