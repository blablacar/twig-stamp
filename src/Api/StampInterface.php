<?php

namespace Blablacar\Twig\Api;

interface StampInterface
{
    /**
     * This method will be used when calling {{ stamp_use('name') }} from any part of your views.
     */
    public function useStamp();

    /**
     * This method will be used at the {% stamp_placeholder %} position, only when {% endstamp %}
     * will be reached (so all stamps requirements have been gathered already).
     */
    public function dumpStamp();

    /**
     * This is the name of your stamp: a name is required to be able to use nested stamps.
     */
    public function getName();
}