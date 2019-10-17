<?php

namespace PE\Component\Cronos\Core;

final class Handler implements HandlerInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @inheritDoc
     */
    public function execute(ServerInterface $server, TaskInterface $task, ...$args): int
    {
        $result = call_user_func($this->callable, $server, $task, ...$args);

        if (!in_array((int) $result, [$task::STATUS_IN_PROGRESS, $task::STATUS_DONE, $task::STATUS_ERROR], true)) {
            $result = $task::STATUS_DONE;
        }

        return $result;
    }
}
