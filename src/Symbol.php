<?php

namespace olcaytaner\ParseTree;

use olcaytaner\Dictionary\Dictionary\Word;

class Symbol extends Word
{
    static array $nonTerminalList = ["ADJP", "ADVP", "CC", "CD", "CONJP", "DT", "EX", "FRAG", "FW", "IN", "INTJ", "JJ", "JJR",
        "JJS", "LS", "LST", "MD", "NAC", "NN", "NNP", "NNPS", "NNS", "NP", "NX", "PDT", "POS", "PP", "PRN", "PRP",
        "PRP$", "PRT", "PRT|ADVP", "QP", "RB", "RBR", "RP", "RRC", "S", "SBAR", "SBARQ", "SINV", "SQ", "SYM", "TO",
        "UCP", "UH", "VB", "VBD", "VBG", "VBN", "VBP", "VBZ", "VP", "WDT", "WHADJP", "WHADVP", "WHNP", "WP", "WP$",
        "WRB", "X", "-NONE-"];
    static array $verbLabels = ["VB", "VBD", "VBG", "VBN","VBP", "VBZ", "VERB"];
    static string $VPLabel = "VB";

    /**
     * Constructor for Symbol class. Sets the name attribute.
     * @param string $name Name attribute
     */
    public function __construct(string $name){
        parent::__construct($name);
    }

    /**
     * Checks if this symbol is a verb type.
     * @return bool True if the symbol is a verb, false otherwise.
     */
    public function isVerb(): bool{
        return in_array($this->name, self::$verbLabels);
    }

    /**
     * Checks if the symbol is VP or not.
     * @return bool True if the symbol is VB, false otherwise.
     */
    public function isVP(): bool{
        return $this->name === Symbol::$VPLabel;
    }

    /**
     * Checks if this symbol is a terminal symbol or not. A symbol is terminal if it is a punctuation symbol, or
     * if it starts with a lowercase symbol.
     * @return bool True if this symbol is a terminal symbol, false otherwise.
     */
    public function isTerminal(): bool{
        if (in_array($this->name, [",", ",", "!", "?", ":", ";", "\"", "''", "'", "`", "``", "...", "-", "--"])){
            return true;
        }
        if (in_array($this->name, self::$nonTerminalList)){
            return true;
        }
        if ($this->name === "I" || $this->name === "A"){
            return true;
        }
        for ($i = 0; $i < mb_strlen($this->name); $i++){
            if (mb_substr($this->name, $i, 1) >= "a" && mb_substr($this->name, $i, 1) <= "z"){
                return true;
            }
        }
        return false;
    }

    /**
     * If the symbol's data contains '-' or '=', this method trims all characters after those characters and returns
     * the resulting string.
     * @return Symbol Trimmed symbol.
     */
    public function trimSymbol(): Symbol{
        if (str_starts_with($this->name, "-") || (!str_contains($this->name, "-") && !str_contains($this->name, "="))){
            return $this;
        }
        $minusIndex = mb_strpos($this->name, "-");
        $equalIndex = mb_strpos($this->name, "=");
        if ($minusIndex !== false || $equalIndex !== false){
            if ($minusIndex !== false && $equalIndex !== false){
                if ($minusIndex < $equalIndex){
                    return new Symbol(mb_substr($this->name, 0, $minusIndex));
                } else {
                    return new Symbol(mb_substr($this->name,0, $equalIndex));
                }
            } else {
                if ($minusIndex != -1){
                    return new Symbol(mb_substr($this->name,0, $minusIndex));
                } else {
                    return new Symbol(mb_substr($this->name,0, $equalIndex));
                }
            }
        } else {
            return $this;
        }
    }
}