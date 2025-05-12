<?php

namespace olcaytaner\ParseTree;

class ConstituentSpan
{
    private Symbol $constituent;
    private int $start;
    private int $end;

    /**
     * Constructor for the ConstituentSpan class. ConstituentSpan is a structure for storing constituents or phrases in
     * a sentence with a specific label. Sets the attributes.
     * @param Symbol $constituent Label of the span.
     * @param int $start Start index of the span.
     * @param int $end End index of the span.
     */
    public function __construct(Symbol $constituent, int $start, int $end){
        $this->constituent = $constituent;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Accessor for the constituent attribute
     * @return Symbol Current constituent
     */
    public function getConstituent(): Symbol
    {
        return $this->constituent;
    }

    /**
     * Accessor for the start attribute
     * @return int Current start
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * Accessor for the end attribute
     * @return int Current end
     */
    public function getEnd(): int
    {
        return $this->end;
    }
}