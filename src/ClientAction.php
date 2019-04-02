<?php

namespace PE\Component\Cronos\Core;

final class ClientAction
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $params;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @param string $name
     * @param mixed  $params
     */
    public function __construct(string $name, $params)
    {
        $this->name   = $name;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result): void
    {
        $this->result = $result;
    }
}
