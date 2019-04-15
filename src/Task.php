<?php

namespace PE\Component\Cronos\Core;

/**
 * @codeCoverageIgnore
 */
class Task implements TaskInterface
{
    private $id;
    private $moduleID;
    private $name;
    private $status;
    private $error;
    private $expression;
    private $arguments = [];
    private $estimate;
    private $progress;
    private $scheduledAt;
    private $executedAt;
    private $finishedAt;

    /**
     * @inheritDoc
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setID(string $id): TaskInterface
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModuleID(): string
    {
        return $this->moduleID;
    }

    /**
     * @inheritDoc
     */
    public function setModuleID(string $moduleID): TaskInterface
    {
        $this->moduleID = $moduleID;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): TaskInterface
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setStatus(int $status): TaskInterface
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getError(): ?\Throwable
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     */
    public function setError(\Throwable $error = null): TaskInterface
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpression(): ?string
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    public function setExpression(string $expression = null): TaskInterface
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @inheritDoc
     */
    public function setArguments(array $arguments): TaskInterface
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEstimate(): ?int
    {
        return $this->estimate;
    }

    /**
     * @inheritDoc
     */
    public function setEstimate(?int $estimate): TaskInterface
    {
        $this->estimate = $estimate;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProgress(): ?int
    {
        return $this->progress;
    }

    /**
     * @inheritDoc
     */
    public function setProgress(?int $progress): TaskInterface
    {
        $this->progress = $progress;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getScheduledAt(): ?\DateTimeInterface
    {
        return $this->scheduledAt;
    }

    /**
     * @inheritDoc
     */
    public function setScheduledAt(?\DateTimeInterface $scheduledAt): TaskInterface
    {
        $this->scheduledAt = $scheduledAt;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExecutedAt(): ?\DateTimeInterface
    {
        return $this->executedAt;
    }

    /**
     * @inheritDoc
     */
    public function setExecutedAt(?\DateTimeInterface $executedAt): TaskInterface
    {
        $this->executedAt = $executedAt;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    /**
     * @inheritDoc
     */
    public function setFinishedAt(?\DateTimeInterface $finishedAt): TaskInterface
    {
        $this->finishedAt = $finishedAt;
        return $this;
    }
}
