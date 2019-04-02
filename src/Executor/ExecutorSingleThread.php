<?php

namespace PE\Component\Cronos\Core\Executor;

use PE\Component\Cronos\Core\TaskInterface;

final class ExecutorSingleThread implements ExecutorInterface
{
    private $maxIterations;
    private $curIterations;

    /**
     * @param $maxIterations
     */
    public function __construct(int $maxIterations = 1000)
    {
        $this->maxIterations = $maxIterations;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function start(): void
    {
        $this->curIterations = $this->maxIterations;
    }

    /**
     * @inheritDoc
     */
    public function isShouldStop(): bool
    {
        return $this->maxIterations > 0 && $this->curIterations <= 0;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(): void
    {
        $this->curIterations--;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function canExecute(TaskInterface $task): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function runExecute(callable $callback): void
    {
        $callback();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function stop(): void
    {}
}
