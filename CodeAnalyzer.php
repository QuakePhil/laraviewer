<?php

namespace HfaTools;

require_once 'ParsedClasses.php';

class CodeAnalyzer
{
    private $rootNode; // not always root, just whatever last function used
    private $parsed;
    public $report;

    public function __construct($argv)
    {
        $this->parsed = new ParsedClasses($argv);
    }

    private function isLeafNode($node, $depth) {
        if (!isset($this->parsed->children[$node]) || $depth > 5) {
            return true;
        }

        if ($node === $this->rootNode && $depth > 0) {
            return true; // should never happen
        }

        return false;
    }

    public function iterateNodes($node, $depth = 0)
    {
        if ($this->isLeafNode($node, $depth)) {
            return '';
        }

        $section = $indent = '';
        if ($depth > 0) {
            $indent = str_repeat('  ', $depth - 1).'* ';
        }

        foreach ($this->parsed->children[$node] as $child) {
            $interface = $this->parsed->interfaces[$child];
            $section .= sprintf("%s%s%s\n", $indent, $child, $interface ? ' (' .$interface.')': '');
            $section .= $this->iterateNodes($child, $depth + 1);
        }

        return $section;
    }

    public function diagram($rootNode)
    {
        $this->report = $this->iterateNodes($this->rootNode = $rootNode);
        return $this;
    }
}