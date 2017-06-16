<?php

namespace QuakePhil;

require_once 'helpers.php';

class ParsedClasses
{
    public $children;
    public $interfaces;
    public $classes;

    public function __construct($argv)
    {
        $modelRoot = '/../app/Models';

        if (isset($argv[1])) {
            $modelRoot = '/../'.fixslash($argv[1]);
        }

        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname(__FILE__).$modelRoot));

        $this->parsed = [];

        foreach ($rii as $file) {
            if (!$file->isDir()) $this->addByPathname($file->getPathname());
        }
    }

    public function addByPathname($path)
    {
        $command = "egrep '^class|^abstract class' " . $path;

        $output = str_replace('abstract class', 'abstract-class', trim(`$command`));

        // todo: split by any amount of whitespace, not just one
        $tokens = explode(' ', $output);
        $class = isset($tokens[1]) ? $tokens[1] : '';
        $extends = isset($tokens[3]) ? $tokens[3] : '';
        $implements = isset($tokens[5]) ? $tokens[5] : '';

        if (trim($extends)) {
            $this->children[$extends][] = $class;
            $this->interfaces[$class] = $implements;
            $this->classes[] = $class;
        }
    }
}
