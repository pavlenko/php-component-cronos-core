<?php

namespace PE\Component\Cronos\Core\Tests\Executor;

use PE\Component\Cronos\Core\Executor\ExecutorMultiThread;
use PE\Component\Cronos\Core\Task;
use PE\Component\Cronos\Process\Process;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ExecutorMultiThreadTest extends TestCase
{
    /**
     * @var Process|MockObject
     */
    private $master;

    protected function setUp(): void
    {
        $this->master = $this->createMock(Process::class);
    }

    public function testIsShouldStopByProcess(): void
    {
        $this->master->expects(self::once())->method('isShouldTerminate')->willReturn(true);

        self::assertTrue((new ExecutorMultiThread($this->master))->isShouldStop());
    }

    public function testIsShouldStopByIterations(): void
    {
        $this->master->expects(self::once())->method('isShouldTerminate')->willReturn(false);
        $this->master->expects(self::once())->method('fork');

        $executor = new ExecutorMultiThread($this->master, 1, 1);
        $executor->runExecute(function () {});

        self::assertTrue($executor->isShouldStop());
    }

    public function testDispatch(): void
    {
        $this->master->expects(self::once())->method('dispatch');

        (new ExecutorMultiThread($this->master))->dispatch();
    }

    public function testCanExecuteNoChildren(): void
    {
        $this->master->expects(self::once())->method('getChildren')->willReturn([]);

        self::assertTrue((new ExecutorMultiThread($this->master, 1, 1))->canExecute(new Task()));
    }

    public function testCanExecuteMaxChildren(): void
    {
        $this->master->expects(self::once())->method('getChildren')->willReturn(['FOO']);

        self::assertFalse((new ExecutorMultiThread($this->master, 1, 1))->canExecute(new Task()));
    }

    public function testRunExecute(): void
    {
        $this->master->expects(self::once())->method('fork')->willReturnCallback(function (Process $worker) {
            self::assertContains('worker', $worker->getProcessTitle());
        });

        $this->master->method('setProcessTitle')->willReturnCallback(function ($title) {
            self::assertContains('master', $title);
        });

        (new ExecutorMultiThread($this->master, 1, 1, 'cronos: %s process'))->runExecute(function () {});
    }

    public function testStop(): void
    {
        $this->master->expects(self::once())->method('wait');

        (new ExecutorMultiThread($this->master))->stop();
    }
}
