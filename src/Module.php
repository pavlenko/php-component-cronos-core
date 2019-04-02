<?php

namespace PE\Component\Cronos\Core;

abstract class Module implements ModuleInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @inheritDoc
     */
    final public function getID(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    final public function setID(string $id): void
    {
        $this->id = $id;
    }
}
