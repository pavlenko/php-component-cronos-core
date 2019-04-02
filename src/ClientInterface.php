<?php

namespace PE\Component\Cronos\Core;

interface ClientInterface
{
    /**
     * @param string $action
     * @param mixed  $request
     *
     * @return mixed
     */
    public function request(string $action, $request);
}