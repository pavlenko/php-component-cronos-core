<?php

namespace PE\Component\Cronos\Core;

interface SerializerInterface
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function encode($value): string;

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function decode(string $value);
}
