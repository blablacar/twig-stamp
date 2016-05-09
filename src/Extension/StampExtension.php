<?php

namespace Blablacar\Twig\Extension;

use Blablacar\Twig\Api\StampInterface;
use Blablacar\Twig\TokenParser\StampTokenParser;

class StampExtension extends \Twig_Extension
{
    protected $stamps = [];

    public function addStamp(StampInterface $stamp)
    {
        $this->stamps[$stamp->getName()] = $stamp;
    }

    public function getStamp($name)
    {
        if (!array_key_exists($name, $this->stamps)) {
            throw new \Twig_Error_Runtime(sprintf('Required stamp "%s" is not initialized.', $name));
        }

        return $this->stamps[$name];
    }

    public function useStamp($name)
    {
        return call_user_func_array([$this->getStamp($name), 'useStamp'], array_slice(func_get_args(), 1));
    }

    public function dumpStamp($name)
    {
        return call_user_func([$this->getStamp($name), 'dumpStamp']);
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('stamp_use', [$this, 'useStamp']),
            new \Twig_SimpleFunction('stamp_use_safe', [$this, 'useStamp'], ['is_safe' => ['html']]),
        ];
    }

    public function getTokenParsers()
    {
        return [
            new StampTokenParser(),
        ];
    }

    public function getName() {
        return 'stamp';
    }
}
