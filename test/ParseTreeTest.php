<?php

use olcaytaner\ParseTree\ParseTree;
use PHPUnit\Framework\TestCase;

class ParseTreeTest extends TestCase
{
    public function testNodeCount(){
        $parseTree1 = new ParseTree("../trees/0000.dev");
        $parseTree2 = new ParseTree("../trees/0001.dev");
        $parseTree3 = new ParseTree("../trees/0002.dev");
        $parseTree4 = new ParseTree("../trees/0003.dev");
        $parseTree5 = new ParseTree("../trees/0014.dev");
        $this->assertEquals(34, $parseTree1->nodeCount());
        $this->assertEquals(39, $parseTree2->nodeCount());
        $this->assertEquals(32, $parseTree3->nodeCount());
        $this->assertEquals(28, $parseTree4->nodeCount());
        $this->assertEquals(9, $parseTree5->nodeCount());
    }

    public function testIsFullSentence(){
        $parseTree1 = new ParseTree("../trees/0000.dev");
        $parseTree2 = new ParseTree("../trees/0001.dev");
        $parseTree3 = new ParseTree("../trees/0002.dev");
        $parseTree4 = new ParseTree("../trees/0003.dev");
        $parseTree5 = new ParseTree("../trees/0014.dev");
        $this->assertTrue($parseTree1->isFullSentence());
        $this->assertTrue($parseTree2->isFullSentence());
        $this->assertTrue($parseTree3->isFullSentence());
        $this->assertTrue($parseTree4->isFullSentence());
        $this->assertFalse($parseTree5->isFullSentence());
    }

    public function testLeafCount(){
        $parseTree1 = new ParseTree("../trees/0000.dev");
        $parseTree2 = new ParseTree("../trees/0001.dev");
        $parseTree3 = new ParseTree("../trees/0002.dev");
        $parseTree4 = new ParseTree("../trees/0003.dev");
        $parseTree5 = new ParseTree("../trees/0014.dev");
        $this->assertEquals(13, $parseTree1->leafCount());
        $this->assertEquals(15, $parseTree2->leafCount());
        $this->assertEquals(10, $parseTree3->leafCount());
        $this->assertEquals(10, $parseTree4->leafCount());
        $this->assertEquals(4, $parseTree5->leafCount());
    }

    public function testNodeCountWithMultipleChildren(){
        $parseTree1 = new ParseTree("../trees/0000.dev");
        $parseTree2 = new ParseTree("../trees/0001.dev");
        $parseTree3 = new ParseTree("../trees/0002.dev");
        $parseTree4 = new ParseTree("../trees/0003.dev");
        $parseTree5 = new ParseTree("../trees/0014.dev");
        $this->assertEquals(8, $parseTree1->nodeCountWithMultipleChildren());
        $this->assertEquals(9, $parseTree2->nodeCountWithMultipleChildren());
        $this->assertEquals(8, $parseTree3->nodeCountWithMultipleChildren());
        $this->assertEquals(6, $parseTree4->nodeCountWithMultipleChildren());
        $this->assertEquals(1, $parseTree5->nodeCountWithMultipleChildren());
    }

    public function testWordCount(){
        $parseTree1 = new ParseTree("../trees/0000.dev");
        $parseTree2 = new ParseTree("../trees/0001.dev");
        $parseTree3 = new ParseTree("../trees/0002.dev");
        $parseTree4 = new ParseTree("../trees/0003.dev");
        $parseTree5 = new ParseTree("../trees/0014.dev");
        $this->assertEquals(7, $parseTree1->wordCount(true));
        $this->assertEquals(8, $parseTree2->wordCount(true));
        $this->assertEquals(6, $parseTree3->wordCount(true));
        $this->assertEquals(7, $parseTree4->wordCount(true));
        $this->assertEquals(2, $parseTree5->wordCount(true));
    }

    public function testToSentence(){
        $parseTree1 = new ParseTree("../trees/0000.dev");
        $parseTree2 = new ParseTree("../trees/0001.dev");
        $parseTree3 = new ParseTree("../trees/0002.dev");
        $parseTree4 = new ParseTree("../trees/0003.dev");
        $parseTree5 = new ParseTree("../trees/0014.dev");
        $this->assertEquals("The complicated language in the huge new law has muddied the fight .", $parseTree1->toSentence());
        $this->assertEquals("The Ways and Means Committee will hold a hearing on the bill next Tuesday .", $parseTree2->toSentence());
        $this->assertEquals("We 're about to see if advertising works .", $parseTree3->toSentence());
        $this->assertEquals("This time around , they 're moving even faster .", $parseTree4->toSentence());
        $this->assertEquals("Ad Notes ... .", $parseTree5->toSentence());
    }

}