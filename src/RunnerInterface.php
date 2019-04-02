<?php

namespace PE\Component\Cronos\Core;

interface RunnerInterface
{
    /**
     * @param ServerInterface $server
     * @param QueueInterface  $queue
     */
    public function run(ServerInterface $server, QueueInterface $queue): void;
}
