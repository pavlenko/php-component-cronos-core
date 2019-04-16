<?php

namespace PE\Component\Cronos\Core;

use PE\Component\Cronos\Core\Executor\ExecutorInterface;

final class Runner implements RunnerInterface
{
    /**
     * @var ExecutorInterface
     */
    private $executor;

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var int
     */
    private $maxLifeTime;

    /**
     * @var callable
     */
    private $enqueueInterval;

    /**
     * @var callable
     */
    private $waitingInterval;

    /**
     * @param ExecutorInterface $executor
     * @param HandlerInterface  $handler
     * @param int               $maxLifeTime
     * @param callable          $enqueueInterval
     * @param callable          $waitingInterval
     */
    public function __construct(
        ExecutorInterface $executor,
        HandlerInterface $handler,
        int $maxLifeTime = 3600,
        callable $enqueueInterval = null,
        callable $waitingInterval = null
    ) {
        $this->executor    = $executor;
        $this->handler     = $handler;
        $this->maxLifeTime = $maxLifeTime;

        $this->enqueueInterval = $enqueueInterval ?: function (): int { return 60; };
        $this->waitingInterval = $waitingInterval ?: function (): int { return 100000; };
    }

    /**
     * @inheritDoc
     */
    public function run(ServerInterface $server, QueueInterface $queue): void
    {
        $server->trigger(ServerInterface::EVENT_STARTING);
        $this->executor->start();
        $server->trigger(ServerInterface::EVENT_STARTED);

        $startedAt = microtime(true);
        $enqueueAt = null;

        while (!($this->executor->isShouldStop() || microtime(true) - $startedAt > $this->maxLifeTime)) {
            $this->executor->dispatch();

            if (empty($enqueueAt) || microtime(true) - $enqueueAt > call_user_func($this->enqueueInterval)) {
                $enqueueAt = microtime(true);
                $server->trigger(ServerInterface::EVENT_ENQUEUE_TASKS, $queue);
            }

            $task = $queue->dequeue();

            if ($task) {
                if ($this->executor->canExecute($task)) {
                    $server->setTaskExecuted($task);

                    $this->executor->runExecute(function () use ($server, $task) {
                        try {
                            $status = $this->handler->execute($server, $task);
                            $error  = null;
                        } catch (\Exception $exception) {
                            $status = TaskInterface::STATUS_ERROR;
                            $error  = $exception;
                        }

                        $server->setTaskFinished($task, $status, $error);
                    });
                } else {
                    $queue->enqueue($task);
                }
            } else {
                $server->trigger(ServerInterface::EVENT_WAITING_TASKS);
            }

            usleep(call_user_func($this->waitingInterval));
        }

        $server->trigger(ServerInterface::EVENT_STOPPING);
        $this->executor->stop();
        $server->trigger(ServerInterface::EVENT_STOPPED);
    }
}
