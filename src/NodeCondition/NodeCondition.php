<?php

namespace olcaytaner\ParseTree\NodeCondition;

use olcaytaner\ParseTree\ParseNode;

interface NodeCondition
{
    public function satisfies(ParseNode $parseNode): bool;
}