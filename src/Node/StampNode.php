<?php

namespace Blablacar\Twig\Node;

class StampNode extends \Twig_Node
{
    public function __construct($name, $aboveDumps, $belowDump, $lineno, $tag)
    {
        parent::__construct([], [
            'name'        => $name,
            'above_dumps' => $aboveDumps,
            'below_dump'  => $belowDump,
        ], $lineno, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        foreach ($this->getAttribute('above_dumps') as $aboveDump) {
            $compiler->subcompile($aboveDump);
        }

        $compiler
            ->addDebugInfo($this)
            ->write("ob_start();\n")
            ->subcompile($this->getAttribute('below_dump'))
        ;

        $compiler
            ->write("\$buffer = ob_get_clean();\n")
        ;

        // echo $this->env->getExtension('stamp')->dumpStamp('svg');
        $compiler
            ->addDebugInfo($this)
            ->write("echo \$this->env->getExtension('stamp')->dumpStamp(")
            ->string($this->getAttribute('name'))
            ->raw(");\n")
        ;

        $compiler
            ->write("echo \$buffer;\n")
        ;
    }
}
