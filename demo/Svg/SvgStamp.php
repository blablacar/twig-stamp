<?php

namespace Demo\Svg;

use Blablacar\Twig\Api\StampInterface;

class SvgStamp implements StampInterface
{
    protected $twig;
    protected $svgDirectory;
    protected $requiredSvgs = [];

    public function __construct(\Twig_Environment $twig, $svgDirectory)
    {
        $this->twig         = $twig;
        $this->svgDirectory = $svgDirectory;
    }

    /**
     * This method is called when using {{ stamp_use('svg', ...) }} twig function.
     *
     * @return string
     * @throws \Twig_Error_Runtime
     */
    public function useStamp()
    {
        $arguments = func_get_args();
        if (count($arguments) == 0) {
            throw new \Twig_Error_Runtime('The "stamp_use" function for "svg" requires at least an icon id.');
        }

        $iconId = $arguments[0];
        if (!array_key_exists($iconId, $this->requiredSvgs)) {
            $viewBox = '0 0 100 100';
            if (isset($arguments[1])) {
                $viewBox = $arguments[1];
            }

            $this->requiredSvgs[$iconId] = [
                'iconId'  => $iconId,
                'viewBox' => $viewBox,
            ];
        }

        return $this->twig->render('use_svg.html.twig', ['iconId' => $iconId]);
    }

    /**
     * This method is called when reaching {% endstamp %}, and will replace {% stamp_dump 'svg' %}
     * by the dump of all required svg sprites.
     *
     * @return string
     * @throws \Twig_Error_Runtime
     */
    public function dumpStamp()
    {
        $svgs = [];
        foreach ($this->requiredSvgs as $svg) {
            $svgs[] = array_merge($svg, [
                'contents' => $this->getSVGContents($svg['iconId']),
            ]);
        }

        return $this->twig->render('dump_svgs.html.twig', ['svgs' => $svgs]);
    }

    protected function getSVGContents($iconId)
    {
        $path = realpath($this->svgDirectory.'/'.strtolower($iconId).'.svg');
        if (!is_readable($path)) {
            throw new \Twig_Error_Runtime(sprintf('"stamp_use" function for "svg": can\'t load icon "%s": file not found or not readable.', $iconId));
        }

        if (strncmp($this->svgDirectory, realpath($path), strlen($this->svgDirectory)) != 0) {
            throw new \Twig_Error_Runtime(sprintf('"stamp_use" function for "svg": icon "%s" is not located in the right directory.', $iconId));
        }

        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($path));

        $contents = [];
        $paths    = $dom->getElementsByTagName('path');
        foreach ($paths as $path) {
            $contents[] = $path->getAttribute('d');
        }

        return $contents;
    }

    public function getName()
    {
        return 'svg';
    }
}
