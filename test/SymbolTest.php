<?php

use olcaytaner\ParseTree\Symbol;
use PHPUnit\Framework\TestCase;

class SymbolTest extends TestCase
{
    public function testTrimSymbol(){
        $this->assertEquals("NP", (new Symbol("NP-SBJ"))->trimSymbol()->getName());
        $this->assertEquals("VP", (new Symbol("VP-SBJ-2"))->trimSymbol()->getName());
        $this->assertEquals("NNP", (new Symbol("NNP-SBJ-OBJ-TN"))->trimSymbol()->getName());
        $this->assertEquals("S", (new Symbol("S-SBJ=OBJ"))->trimSymbol()->getName());
    }
}