<?php

namespace PE\Component\Cronos\Core;

use PE\Component\Cronos\Process\Daemon;
use PE\Component\Cronos\Process\Process;

final class Server implements ServerInterface
{
    use EventEmitterTrait;

    /**
     * @var RunnerInterface
     */
    private $runner;

    /**
     * @var Daemon
     */
    private $daemon;

    /**
     * @var ModuleInterface[]
     */
    private $modules = [];

    /**
     * @param RunnerInterface $runner
     * @param Daemon          $daemon
     */
    public function __construct(RunnerInterface $runner, Daemon $daemon)
    {
        $this->runner = $runner;
        $this->daemon = $daemon;
    }

    /**
     * @inheritDoc
     */
    public function attachModule(string $id, ModuleInterface $module): void
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Module ID is required');
        }

        if (array_key_exists($id, $this->modules)) {
            throw new \InvalidArgumentException(sprintf('Module with ID = "%s" is already attached', $id));
        }

        $module->setID($id);
        $module->attachServer($this);

        $this->modules[$id] = $module;
    }

    /**
     * @inheritDoc
     */
    public function detachModule(string $id): void
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Module ID is required');
        }

        if (!array_key_exists($id, $this->modules)) {
            throw new \InvalidArgumentException(sprintf('Module with ID = "%s" is already detached', $id));
        }

        $this->modules[$id]->detachServer($this);

        unset($this->modules[$id]);
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        $this->daemon->createPIDFile(getmypid());
        $this->runner->run($this, new Queue());
        $this->daemon->removePIDFile();
    }

    /**
     * @inheritDoc
     */
    public function start(): void
    {
        $process = new Process();
        $process->setCallable(function () {
            $this->runner->run($this, new Queue());
        });

        $this->daemon->fork($process);
    }

    /**
     * @inheritDoc
     */
    public function stop(): void
    {
        $this->daemon->kill();
    }

    /**
     * @inheritDoc
     */
    public function setTaskExecuted(TaskInterface $task): void
    {
        $task->setExecutedAt(\DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', '')));
        $task->setStatus(TaskInterface::STATUS_IN_EXECUTOR);

        $this->trigger(static::EVENT_SET_TASK_EXECUTED, $task);
    }

    /**
     * @inheritDoc
     */
    public function setTaskEstimate(TaskInterface $task, int $estimate): void
    {
        $task->setEstimate($estimate);
        $this->trigger(static::EVENT_SET_TASK_ESTIMATE, $task);
    }

    /**
     * @inheritDoc
     */
    public function setTaskProgress(TaskInterface $task, int $progress): void
    {
        $task->setProgress($progress);
        $this->trigger(static::EVENT_SET_TASK_PROGRESS, $task);
    }

    /**
     * @inheritDoc
     */
    public function setTaskFinished(TaskInterface $task, int $status, \Exception $error = null): void
    {
        $task->setFinishedAt(\DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', '')));
        $task->setStatus($status);
        $task->setError($error);

        $this->trigger(static::EVENT_SET_TASK_FINISHED, $task);
    }
}
