<?php

namespace PE\Component\Cronos\Core\Tests;

use PE\Component\Cronos\Core\QueueInterface;
use PE\Component\Cronos\Core\Runner;
use PE\Component\Cronos\Core\ServerInterface;
use PE\Component\Cronos\Core\Task;
use PE\Component\Cronos\Core\Executor\ExecutorInterface;
use PE\Component\Cronos\Core\HandlerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RunnerTest extends TestCase
{
    /**
     * @var ExecutorInterface|MockObject
     */
    private $executor;

    /**
     * @var HandlerInterface|MockObject
     */
    private $handler;

    /**
     * @var ServerInterface|MockObject
     */
    private $server;

    /**
     * @var QueueInterface|MockObject
     */
    private $queue;

    /**
     * @var Runner
     */
    private $runner;

    protected function setUp()
    {
        $this->executor = $this->createMock(ExecutorInterface::class);
        $this->handler  = $this->createMock(HandlerInterface::class);
        $this->server   = $this->createMock(ServerInterface::class);
        $this->queue    = $this->createMock(QueueInterface::class);
        $this->runner   = new Runner($this->executor, $this->handler);
    }

    public function testRunWithShouldStop(): void
    {
        $this->executor->expects(static::once())->method('isShouldStop')->willReturn(true);
        $this->executor->expects(static::never())->method('dispatch');

        $this->server->expects(static::exactly(4))->method('trigger')->withConsecutive(
            [ServerInterface::EVENT_STARTING],
            [ServerInterface::EVENT_STARTED],
            [ServerInterface::EVENT_STOPPING],
            [ServerInterface::EVENT_STOPPED]
        );

        $this->runner->run($this->server, $this->queue);
    }

    public function testRunWithoutAnyExecution(): void
    {
        $this->executor->expects(static::exactly(2))->method('isShouldStop')->willReturnOnConsecutiveCalls(false, true);
        $this->executor->expects(static::once())->method('dispatch');

        $this->server->method('trigger')->withConsecutive(
            [ServerInterface::EVENT_STARTING],
            [ServerInterface::EVENT_STARTED],
            [ServerInterface::EVENT_ENQUEUE_TASKS],
            [ServerInterface::EVENT_WAITING_TASKS],
            [ServerInterface::EVENT_STOPPING],
            [ServerInterface::EVENT_STOPPED]
        );

        $this->runner->run($this->server, $this->queue);
    }

    public function testRunWithBusyExecutor(): void
    {
        $this->executor->expects(static::exactly(2))->method('isShouldStop')->willReturnOnConsecutiveCalls(false, true);
        $this->executor->expects(static::once())->method('dispatch');
        $this->executor->expects(static::once())->method('canExecute')->willReturn(false);

        $this->server->method('trigger')->withConsecutive(
            [ServerInterface::EVENT_STARTING],
            [ServerInterface::EVENT_STARTED],
            [ServerInterface::EVENT_ENQUEUE_TASKS],
            [ServerInterface::EVENT_STOPPING],
            [ServerInterface::EVENT_STOPPED]
        );

        $task = new Task();

        $this->queue->expects(static::once())->method('dequeue')->willReturn($task);
        $this->queue->expects(static::once())->method('enqueue')->with($task);

        $this->runner->run($this->server, $this->queue);
    }

    public function testRunWithExecuteSuccess(): void
    {
        $this->executor->expects(static::exactly(2))->method('isShouldStop')->willReturnOnConsecutiveCalls(false, true);
        $this->executor->expects(static::once())->method('dispatch');
        $this->executor->expects(static::once())->method('canExecute')->willReturn(true);
        $this->executor->expects(static::once())->method('runExecute')->willReturnCallback(static function ($c) { $c(); });

        $this->handler->expects(static::once())->method('execute');

        $this->server->method('trigger')->withConsecutive(
            [ServerInterface::EVENT_STARTING],
            [ServerInterface::EVENT_STARTED],
            [ServerInterface::EVENT_ENQUEUE_TASKS],
            [ServerInterface::EVENT_STOPPING],
            [ServerInterface::EVENT_STOPPED]
        );

        $task = new Task();

        $this->queue->expects(static::once())->method('dequeue')->willReturn($task);
        $this->queue->expects(static::never())->method('enqueue');

        $this->runner->run($this->server, $this->queue);
    }

    public function testRunWithExecuteError(): void
    {
        $this->executor->expects(static::exactly(2))->method('isShouldStop')->willReturnOnConsecutiveCalls(false, true);
        $this->executor->expects(static::once())->method('dispatch');
        $this->executor->expects(static::once())->method('canExecute')->willReturn(true);
        $this->executor->expects(static::once())->method('runExecute')->willReturnCallback(static function ($c) { $c(); });

        $this->handler->expects(static::once())->method('execute')->willThrowException(new \Exception());

        $this->server->method('trigger')->withConsecutive(
            [ServerInterface::EVENT_STARTING],
            [ServerInterface::EVENT_STARTED],
            [ServerInterface::EVENT_ENQUEUE_TASKS],
            [ServerInterface::EVENT_STOPPING],
            [ServerInterface::EVENT_STOPPED]
        );

        $task = new Task();

        $this->queue->expects(static::once())->method('dequeue')->willReturn($task);
        $this->queue->expects(static::never())->method('enqueue');

        $this->runner->run($this->server, $this->queue);
    }
}
