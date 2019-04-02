<?php

namespace PE\Component\Cronos\Core\Tests;

use PE\Component\Cronos\Core\Queue;
use PE\Component\Cronos\Core\Task;
use PE\Component\Cronos\Core\TaskInterface;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    public function testDuplicateHandlingMode(): void
    {
        $queue = new Queue();

        static::assertSame($queue::MODE_ERROR, $queue->getDuplicateHandlingMode());

        $queue->setDuplicateHandlingMode($queue::MODE_IGNORE);

        static::assertSame($queue::MODE_IGNORE, $queue->getDuplicateHandlingMode());

        $queue->setDuplicateHandlingMode($queue::MODE_ERROR);

        static::assertSame($queue::MODE_ERROR, $queue->getDuplicateHandlingMode());
    }

    public function testContents(): void
    {
        $task  = (new Task())->setID('ID')->setModuleID('MODULE');
        $queue = new Queue();

        static::assertSame([], $queue->contents());

        $queue->enqueue($task);

        static::assertSame([$task], $queue->contents());
    }

    public function testContains(): void
    {
        $task  = (new Task())->setID('ID')->setModuleID('MODULE');
        $queue = new Queue();

        static::assertFalse($queue->contains($task));

        $queue->enqueue($task);

        static::assertTrue($queue->contains($task));
    }

    public function testDequeue(): void
    {
        $queue = new Queue();

        static::assertNull($queue->dequeue());

        $queue->enqueue((new Task())->setID('ID')->setModuleID('MODULE'));

        static::assertInstanceOf(TaskInterface::class, $queue->dequeue());
    }

    public function testEnqueueModeError(): void
    {
        $this->expectException(\LogicException::class);

        $queue = new Queue();
        $queue->setDuplicateHandlingMode($queue::MODE_ERROR);

        $task = (new Task())->setID('ID')->setModuleID('MODULE');

        $queue->enqueue($task);
        $queue->enqueue($task);
    }

    public function testEnqueueModeIgnore(): void
    {
        $queue = new Queue();
        $queue->setDuplicateHandlingMode($queue::MODE_IGNORE);

        $task = (new Task())->setID('ID')->setModuleID('MODULE');

        $queue->enqueue($task);
        $queue->enqueue($task);

        static::assertCount(1, $queue->contents());
    }

    public function testEnqueueDifferentModules(): void
    {
        $queue = new Queue();
        $queue->setDuplicateHandlingMode($queue::MODE_IGNORE);

        $task1 = (new Task())->setID('ID')->setModuleID('MODULE1');
        $task2 = (new Task())->setID('ID')->setModuleID('MODULE2');

        $queue->enqueue($task1);
        $queue->enqueue($task2);

        static::assertCount(2, $queue->contents());
    }
}
