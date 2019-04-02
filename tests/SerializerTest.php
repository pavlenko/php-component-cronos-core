<?php

namespace PE\Component\Cronos\Core\Tests;

use PE\Component\Cronos\Core\Normalizer;
use PE\Component\Cronos\Core\Serializer;
use PHPUnit\Framework\TestCase;

class SerializerTest extends TestCase
{
    public function testNormalizer(): void
    {
        $normalizer = new Normalizer();
        $serializer = new Serializer();

        static::assertNotSame($normalizer, $serializer->getNormalizer());

        $serializer->setNormalizer($normalizer);

        static::assertSame($normalizer, $serializer->getNormalizer());
    }

    public function testDecode(): void
    {
        static::assertSame([1.0], (new Serializer())->decode('[1.0]'));
        static::assertSame(['a/b'], (new Serializer())->decode('["a/b"]'));
    }

    public function testEncode(): void
    {
        static::assertSame('[1.0]', (new Serializer())->encode([1.0]));
        static::assertSame('["a/b"]', (new Serializer())->encode(['a/b']));
    }
}
