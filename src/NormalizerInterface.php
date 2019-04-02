<?php

namespace PE\Component\Cronos\Core;

interface NormalizerInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function encode($value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function decode($value);
}
