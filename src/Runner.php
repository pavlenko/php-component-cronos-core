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

        $this->enqueueInterval = $enqueueInterval ?: function (): float { return 60.0; };
        $this->waitingInterval = $waitingInterval ?: function (): float { return 0.1; };
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

        $enqueueAt = 0;
        $enqueueTo = 0;

        $waitingAt = 0;
        $waitingTo = 0;

        while (!($this->executor->isShouldStop() || microtime(true) - $startedAt > $this->maxLifeTime)) {
            $this->executor->dispatch();

            // Enqueue tasks at each minute
            if (microtime(true) - $enqueueAt > $enqueueTo) {
                $enqueueAt = microtime(true);
                $enqueueTo = max(call_user_func($this->enqueueInterval), 60);

                $server->trigger(ServerInterface::EVENT_ENQUEUE_TASKS, $queue);
            }

            // Ensure delay between tasks, sleep may not sleep
            if (microtime(true) - $waitingAt < $waitingTo) {
                usleep(100000);
                continue;
            }

            $waitingAt = microtime(true);
            $waitingTo = call_user_func($this->waitingInterval);

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
        }

        $server->trigger(ServerInterface::EVENT_STOPPING);
        $this->executor->stop();
        $server->trigger(ServerInterface::EVENT_STOPPED);
    }
}
