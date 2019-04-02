<?php

namespace PE\Component\Cronos\Core;

final class Queue implements QueueInterface
{
    /**
     * @var int
     */
    private $mode;

    /**
     * @var TaskInterface[]
     */
    private $tasks = [];

    /**
     * @param int $mode
     */
    public function __construct(int $mode = self::MODE_ERROR)
    {
        $this->mode = $mode;
    }

    /**
     * @inheritDoc
     */
    public function getDuplicateHandlingMode(): int
    {
        return $this->mode;
    }

    /**
     * @inheritDoc
     */
    public function setDuplicateHandlingMode(int $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * @return TaskInterface[]
     */
    public function contents(): array
    {
        return array_values($this->tasks);
    }

    /**
     * @param TaskInterface $task
     *
     * @return bool
     */
    public function contains(TaskInterface $task): bool
    {
        return array_key_exists($this->resolveKey($task), $this->tasks);
    }

    /**
     * @return TaskInterface|null
     */
    public function dequeue(): ?TaskInterface
    {
        return array_shift($this->tasks);
    }

    /**
     * @param TaskInterface $task
     */
    public function enqueue(TaskInterface $task): void
    {
        $key = $this->resolveKey($task);

        if (!array_key_exists($key, $this->tasks)) {
            $this->tasks[$key] = $task;
        } else if (self::MODE_ERROR === $this->mode) {
            throw new \LogicException('Cannot add same task');
        }
    }

    /**
     * @param TaskInterface $task
     *
     * @return string
     */
    private function resolveKey(TaskInterface $task): string
    {
        return $task->getModuleID() . ':' . $task->getID();
    }
}
