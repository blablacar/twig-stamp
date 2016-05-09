<?php

namespace Demo\TableOfContents;

use Blablacar\Twig\Api\StampInterface;

class TableOfContentsStamp implements StampInterface
{
    protected $twig;
    protected $list = [];

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function useStamp()
    {
        list($title) = func_get_args();
        $this->list[] = $title;

        return $title;
    }

    public function dumpStamp()
    {
        return $this->twig->render('toc.twig', [
            'list' => $this->list
        ]);
    }

    public function getName()
    {
        return 'toc';
    }
}
