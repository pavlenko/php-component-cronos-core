<?php

namespace PE\Component\Cronos\Core;

interface QueueInterface
{
    public const MODE_ERROR  = 0;
    public const MODE_IGNORE = 1;

    /**
     * @return int
     */
    public function getDuplicateHandlingMode(): int;

    /**
     * @param int $mode
     */
    public function setDuplicateHandlingMode(int $mode): void;

    /**
     * @return TaskInterface[]
     */
    public function contents(): array;

    /**
     * @param TaskInterface $task
     *
     * @return bool
     */
    public function contains(TaskInterface $task): bool;

    /**
     * @return TaskInterface|null
     */
    public function dequeue(): ?TaskInterface;

    /**
     * @param TaskInterface $task
     */
    public function enqueue(TaskInterface $task): void;
}