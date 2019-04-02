<?php

namespace PE\Component\Cronos\Core;

interface ModuleInterface
{
    /**
     * @return string
     */
    public function getID(): string;

    /**
     * @param string $id
     */
    public function setID(string $id): void;

    /**
     * @param ServerInterface $server
     */
    public function attachServer(ServerInterface $server): void;

    /**
     * @param ServerInterface $server
     */
    public function detachServer(ServerInterface $server): void;
}
