<?php

namespace PE\Component\Cronos\Core\Tests;

use PE\Component\Cronos\Core\Client;
use PE\Component\Cronos\Core\ClientAction;
use PE\Component\Cronos\Core\ServerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testRequest(): void
    {
        /* @var $server ServerInterface|MockObject */
        $server = $this->createMock(ServerInterface::class);
        $server
            ->expects(self::once())
            ->method('trigger')
            ->with(ServerInterface::EVENT_CLIENT_ACTION, self::isInstanceOf(ClientAction::class))
            ->willReturnCallback(static function ($event, ClientAction $action) {
                self::assertSame('ACTION', $action->getName());
                self::assertSame(['PARAM'], $action->getParams());

                $action->setResult(['RESULT']);
                return 1;
            });

        $result = (new Client($server))->request('ACTION', ['PARAM']);

        self::assertSame(['RESULT'], $result);
    }
}
