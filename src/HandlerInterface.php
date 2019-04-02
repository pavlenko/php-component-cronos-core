<?php

namespace PE\Component\Cronos\Core;

interface HandlerInterface
{
    /**
     * @param ServerInterface $server
     * @param TaskInterface   $task
     *
     * @return int
     *
     * @throws \Exception
     */
    public function execute(ServerInterface $server, TaskInterface $task): int;
}