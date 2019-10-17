<?php

namespace PE\Component\Cronos\Core\Executor;

use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Process\Process;

final class ExecutorMultiThread implements ExecutorInterface
{
    /**
     * @var Process
     */
    private $master;

    /**
     * @var int
     */
    private $maxIterations;

    /**
     * @var int
     */
    private $curIterations;

    /**
     * @var int
     */
    private $maxChildren;

    /**
     * @var string
     */
    private $title;

    /**
     * @param Process $master
     * @param int     $maxIterations
     * @param int     $maxChildren
     * @param string  $title
     */
    public function __construct(Process $master, int $maxIterations = 1000, int $maxChildren = 3, string $title = null)
    {
        $this->master        = $master;
        $this->maxIterations = $maxIterations;
        $this->maxChildren   = $maxChildren;
        $this->title         = $title;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function start(): void
    {
        // Ensure handlers attached to signals
        $this->master->getSignals();

        // Ensure valid process title
        $this->master->setProcessTitle(sprintf($this->title, 'master'), true);

        // Init iterations countdown
        $this->curIterations = $this->maxIterations;
    }

    /**
     * @inheritDoc
     */
    public function isShouldStop(): bool
    {
        return $this->master->isShouldTerminate() || ($this->maxIterations > 0 && $this->curIterations <= 0);
    }

    /**
     * @inheritDoc
     */
    public function dispatch(): void
    {
        $this->master->dispatch();
    }

    /**
     * @inheritDoc
     */
    public function canExecute(TaskInterface $task): bool
    {
        return count($this->master->getChildren()) < $this->maxChildren;
    }

    /**
     * @inheritDoc
     */
    public function runExecute(callable $callback): void
    {
        $this->curIterations--;

        $worker = new Process();
        $worker->setCallable($callback);
        $worker->setProcessTitle(sprintf($this->title, 'worker'));

        $this->master->fork($worker);
    }

    /**
     * @inheritDoc
     */
    public function stop(): void
    {
        $this->master->wait();
    }
}
