<?php

namespace PE\Component\Cronos\Core\Executor;

use PE\Component\Cronos\Core\TaskInterface;

interface ExecutorInterface
{
    /**
     * Prepare executor for use in runner
     */
    public function start(): void;

    /**
     * Check if executor should stop
     *
     * @return bool
     */
    public function isShouldStop(): bool;

    /**
     * Dispatch loop iteration
     */
    public function dispatch(): void;

    /**
     * Check if executor can execute task now
     *
     * @param TaskInterface $task
     *
     * @return bool
     */
    public function canExecute(TaskInterface $task): bool;

    /**
     * Execute task
     *
     * @param callable $callback
     */
    public function runExecute(callable $callback): void;

    /**
     * Prepare executor before exit
     */
    public function stop(): void;
}
