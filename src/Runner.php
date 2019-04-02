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
     * @param ExecutorInterface $executor
     * @param HandlerInterface  $handler
     */
    public function __construct(ExecutorInterface $executor, HandlerInterface $handler)
    {
        $this->executor = $executor;
        $this->handler  = $handler;
    }

    /**
     * @inheritDoc
     */
    public function run(ServerInterface $server, QueueInterface $queue): void
    {
        $server->trigger(ServerInterface::EVENT_STARTING);
        $this->executor->start();
        $server->trigger(ServerInterface::EVENT_STARTED);

        while (!$this->executor->isShouldStop()) {
            $this->executor->dispatch();

            if (empty($startedAt) || microtime(true) - $startedAt > 1) {
                $startedAt = microtime(true);
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

            usleep(100000);
        }

        $server->trigger(ServerInterface::EVENT_STOPPING);
        $this->executor->stop();
        $server->trigger(ServerInterface::EVENT_STOPPED);
    }
}
