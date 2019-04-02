<?php

namespace PE\Component\Cronos\Core;

/**
 * Task DTO interface
 */
interface TaskInterface
{
    public const STATUS_PENDING     = 0;
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_IN_EXECUTOR = 2;
    public const STATUS_DONE        = 3;
    public const STATUS_ERROR       = 4;

    /**
     * @return string
     */
    public function getID(): string;

    /**
     * @param string $id
     *
     * @return TaskInterface
     */
    public function setID(string $id): TaskInterface;

    /**
     * @return string
     */
    public function getModuleID(): string;

    /**
     * @param string $moduleID
     *
     * @return TaskInterface
     */
    public function setModuleID(string $moduleID): TaskInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return TaskInterface
     */
    public function setName(string $name): TaskInterface;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param int $status
     *
     * @return TaskInterface
     */
    public function setStatus(int $status): TaskInterface;

    /**
     * @return \Throwable|null
     */
    public function getError(): ?\Throwable;

    /**
     * @param \Throwable|null $error
     *
     * @return TaskInterface
     */
    public function setError(\Throwable $error = null): TaskInterface;

    /**
     * @return string|null
     */
    public function getExpression(): ?string;

    /**
     * @param string|null $expression
     *
     * @return TaskInterface
     */
    public function setExpression(string $expression = null): TaskInterface;

    /**
     * @return array
     */
    public function getArguments(): array;

    /**
     * @param array $arguments
     *
     * @return TaskInterface
     */
    public function setArguments(array $arguments): TaskInterface;

    /**
     * @return int|null
     */
    public function getEstimate(): ?int;

    /**
     * @param int $estimate
     *
     * @return TaskInterface
     */
    public function setEstimate(?int $estimate): TaskInterface;

    /**
     * @return int|null
     */
    public function getProgress(): ?int;

    /**
     * @param int $progress
     *
     * @return TaskInterface
     */
    public function setProgress(?int $progress): TaskInterface;

    /**
     * @return \DateTimeInterface|null
     */
    public function getScheduledAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface $scheduledAt
     *
     * @return TaskInterface
     */
    public function setScheduledAt(?\DateTimeInterface $scheduledAt): TaskInterface;

    /**
     * @return \DateTimeInterface|null
     */
    public function getExecutedAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface $executedAt
     *
     * @return TaskInterface
     */
    public function setExecutedAt(?\DateTimeInterface $executedAt): TaskInterface;

    /**
     * @return \DateTimeInterface|null
     */
    public function getFinishedAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface $finishedAt
     *
     * @return TaskInterface
     */
    public function setFinishedAt(?\DateTimeInterface $finishedAt): TaskInterface;
}
