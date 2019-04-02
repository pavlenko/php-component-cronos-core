<?php

namespace PE\Component\Cronos\Core\Tests;

use PE\Component\Cronos\Core\Task;
use PE\Component\Cronos\Core\Handler;
use PE\Component\Cronos\Core\ServerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testExecuteWithException(): void
    {
        $exception = new \Exception();

        $this->expectExceptionObject($exception);

        /* @var $server ServerInterface|MockObject */
        $server = $this->createMock(ServerInterface::class);

        $task = new Task();

        /* @var $callable callable|MockObject */
        $callable = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $callable
            ->expects(static::once())
            ->method('__invoke')
            ->with($server, $task)
            ->willThrowException($exception);

        (new Handler($callable))->execute($server, $task);
    }

    /**
     * @throws \Exception
     */
    public function testExecuteWithEmptyStatus(): void
    {
        /* @var $server ServerInterface|MockObject */
        $server = $this->createMock(ServerInterface::class);

        $task = new Task();

        /* @var $callable callable|MockObject */
        $callable = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $callable
            ->expects(static::once())
            ->method('__invoke')
            ->with($server, $task)
            ->willReturn(100);

        static::assertSame(Task::STATUS_DONE, (new Handler($callable))->execute($server, $task));
    }

    /**
     * @throws \Exception
     */
    public function testExecuteWithValidStatus(): void
    {
        /* @var $server ServerInterface|MockObject */
        $server = $this->createMock(ServerInterface::class);

        $task = new Task();

        /* @var $callable callable|MockObject */
        $callable = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $callable
            ->expects(static::once())
            ->method('__invoke')
            ->with($server, $task)
            ->willReturn(Task::STATUS_IN_PROGRESS);

        static::assertSame(Task::STATUS_IN_PROGRESS, (new Handler($callable))->execute($server, $task));
    }
}
