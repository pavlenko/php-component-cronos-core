<?php

namespace PE\Component\Cronos\Core;

interface ServerInterface extends EventEmitterInterface
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE   = 1;
    public const STATUS_STARTING = 3;
    public const STATUS_STOPPING = 4;

    public const EVENT_STARTING          = 'starting';
    public const EVENT_STARTED           = 'started';
    public const EVENT_STOPPING          = 'stopping';
    public const EVENT_STOPPED           = 'stopped';
    public const EVENT_ENQUEUE_TASKS     = 'enqueue_tasks';
    public const EVENT_WAITING_TASKS     = 'waiting_tasks';
    public const EVENT_SET_TASK_EXECUTED = 'set_task_executed';
    public const EVENT_SET_TASK_ESTIMATE = 'set_task_estimate';
    public const EVENT_SET_TASK_PROGRESS = 'set_task_progress';
    public const EVENT_SET_TASK_FINISHED = 'set_task_finished';
    public const EVENT_CLIENT_ACTION     = 'client_action';

    /**
     * @param string          $id
     * @param ModuleInterface $module
     *
     * @throws \InvalidArgumentException If module already attached
     */
    public function attachModule(string $id, ModuleInterface $module): void;

    /**
     * @param string $id
     *
     * @throws \InvalidArgumentException If module already detached
     */
    public function detachModule(string $id): void;

    /**
     * Proxy execution to internal runner instance
     */
    public function run(): void;

    /**
     * Run server in background
     *
     * @throws \RuntimeException If daemon not configured
     */
    public function start(): void;

    /**
     * Stop background server
     *
     * @throws \RuntimeException If daemon not configured
     */
    public function stop(): void;

    /**
     * Set task executed date/time, status & trigger event
     *
     * @param TaskInterface $task
     */
    public function setTaskExecuted(TaskInterface $task): void;

    /**
     * Set task estimate value & trigger event
     *
     * @param TaskInterface $task
     * @param int           $estimate
     */
    public function setTaskEstimate(TaskInterface $task, int $estimate): void;

    /**
     * Set task progress value & trigger event
     *
     * @param TaskInterface $task
     * @param int           $progress
     */
    public function setTaskProgress(TaskInterface $task, int $progress): void;

    /**
     * Set task finished date/time, status, error & trigger event
     *
     * @param TaskInterface   $task
     * @param int             $status
     * @param \Exception|null $error
     */
    public function setTaskFinished(TaskInterface $task, int $status, \Exception $error = null): void;
}
