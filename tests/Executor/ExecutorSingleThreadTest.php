<?php

namespace PE\Component\Cronos\Core\Tests\Executor;

use PE\Component\Cronos\Core\Executor\ExecutorSingleThread;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ExecutorSingleThreadTest extends TestCase
{
    public function testDispatch(): void
    {
        $executor = new ExecutorSingleThread(10);
        $executor->start();

        for ($i = 0; $i < 9; $i++) {
            $executor->dispatch();
            static::assertFalse($executor->isShouldStop());
        }
    }

    public function testRunExecute(): void
    {
        /* @var $callable callable|MockObject */
        $callable = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $callable->expects(static::once())->method('__invoke');

        (new ExecutorSingleThread())->runExecute($callable);
    }
}
