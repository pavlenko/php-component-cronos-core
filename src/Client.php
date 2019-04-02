<?php

namespace PE\Component\Cronos\Core;

final class Client implements ClientInterface
{
    /**
     * @var ServerInterface
     */
    private $server;

    /**
     * @param ServerInterface $server
     */
    public function __construct(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * @inheritDoc
     */
    public function request(string $action, $request)
    {
        $clientAction = new ClientAction($action, $request);

        $this->server->trigger(ServerInterface::EVENT_CLIENT_ACTION, $clientAction);

        return $clientAction->getResult();
    }
}
