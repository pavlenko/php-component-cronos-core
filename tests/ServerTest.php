<?php

namespace PE\Component\Cronos\Core\Tests;

use PE\Component\Cronos\Core\ModuleInterface;
use PE\Component\Cronos\Core\RunnerInterface;
use PE\Component\Cronos\Core\Server;
use PE\Component\Cronos\Core\ServerInterface;
use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Process\Daemon;
use PE\Component\Cronos\Process\Process;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    /**
     * @var RunnerInterface|MockObject
     */
    private $runner;

    /**
     * @var Daemon|MockObject
     */
    private $daemon;

    /**
     * @var Server
     */
    private $server;

    protected function setUp()
    {
        $this->runner = $this->createMock(RunnerInterface::class);
        $this->daemon = $this->createMock(Daemon::class);

        $this->server = new Server($this->runner, $this->daemon);
    }

    public function testAttachModuleThrowsExceptionIfNoID(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Module ID is required');

        /* @var $module ModuleInterface|MockObject */
        $module = $this->createMock(ModuleInterface::class);

        $this->server->attachModule('', $module);
    }

    public function testAttachModuleThrowsExceptionIfAlreadyAttached(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Module with ID = "MODULE" is already attached');

        /* @var $module ModuleInterface|MockObject */
        $module = $this->createMock(ModuleInterface::class);

        $this->server->attachModule('MODULE', $module);
        $this->server->attachModule('MODULE', $module);
    }

    public function testAttachModule(): void
    {
        /* @var $module ModuleInterface|MockObject */
        $module = $this->createMock(ModuleInterface::class);
        $module->expects(static::once())->method('setID')->with('MODULE');
        $module->expects(static::once())->method('attachServer')->with($this->server);

        $this->server->attachModule('MODULE', $module);
    }

    public function testDetachModuleThrowsExceptionIfNoID(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Module ID is required');

        $this->server->detachModule('');
    }

    public function testDetachModuleThrowsExceptionIfAlreadyDetached(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Module with ID = "MODULE" is already detached');

        $this->server->detachModule('MODULE');
    }

    public function testDetachModule(): void
    {
        /* @var $module ModuleInterface|MockObject */
        $module = $this->createMock(ModuleInterface::class);
        $module->expects(static::once())->method('detachServer')->with($this->server);

        $this->server->attachModule('MODULE', $module);
        $this->server->detachModule('MODULE');
    }

    public function testRun(): void
    {
        $this->runner->expects(self::once())->method('run');
        $this->server->run();
    }

    public function testStart(): void
    {
        $this->daemon
            ->expects(self::once())
            ->method('fork')
            ->with(self::isInstanceOf(Process::class))
            ->willReturnCallback(static function (Process $process) {
                call_user_func($process->getCallable());
            });

        $this->runner->expects(self::once())->method('run');

        $this->server->start();
    }

    public function testStop(): void
    {
        $this->daemon->expects(self::once())->method('kill');
        $this->server->stop();
    }

    public function testSetTaskExecuted(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('setExecutedAt')->with(static::isInstanceOf(\DateTime::class));
        $task->expects(static::once())->method('setStatus')->with(TaskInterface::STATUS_IN_EXECUTOR);

        $listener = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $listener->expects(static::once())->method('__invoke')->with($task);

        /* @var $listener callable|MockObject */
        $this->server->attachListener(ServerInterface::EVENT_SET_TASK_EXECUTED, $listener);

        $this->server->setTaskExecuted($task);
    }

    public function testSetTaskEstimate(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('setEstimate')->with(100);
        $task->expects(static::never())->method('setStatus');

        /* @var $listener callable|MockObject */
        $listener = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $listener->expects(static::once())->method('__invoke')->with($task);

        $this->server->attachListener(ServerInterface::EVENT_SET_TASK_ESTIMATE, $listener);

        $this->server->setTaskEstimate($task, 100);
    }

    public function testSetTaskProgress(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('setProgress')->with(100);
        $task->expects(static::never())->method('setStatus');

        /* @var $listener callable|MockObject */
        $listener = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $listener->expects(static::once())->method('__invoke')->with($task);

        $this->server->attachListener(ServerInterface::EVENT_SET_TASK_PROGRESS, $listener);

        $this->server->setTaskProgress($task, 100);
    }

    public function testSetTaskFinished(): void
    {
        $exception = new \Exception();

        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('setFinishedAt')->with(static::isInstanceOf(\DateTime::class));
        $task->expects(static::once())->method('setStatus')->with(static::logicalNot(static::equalTo(TaskInterface::STATUS_IN_EXECUTOR)));
        $task->expects(static::once())->method('setError')->with($exception);

        /* @var $listener callable|MockObject */
        $listener = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $listener->expects(static::once())->method('__invoke')->with($task);

        $this->server->attachListener(ServerInterface::EVENT_SET_TASK_FINISHED, $listener);

        $this->server->setTaskFinished($task, TaskInterface::STATUS_ERROR, $exception);
    }
}
