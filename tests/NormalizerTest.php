<?php

namespace PE\Component\Cronos\Core\Tests;

use PE\Component\Cronos\Core\Normalizer;
use PHPUnit\Framework\TestCase;

class NormalizerTest extends TestCase
{
    public function testEncodeThrowsExceptionOnResource(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Normalizer())->encode(fopen('php://memory', 'rb'));
    }

    public function testEncodeThrowsExceptionOnClosure(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Normalizer())->encode(function () {
        });
    }

    public function testEncodeScalar(): void
    {
        static::assertSame(['A', 1, 1.1, true], (new Normalizer())->encode(['A', 1, 1.1, true]));
    }

    public function testEncodeNested(): void
    {
        static::assertSame(['Foo'], (new Normalizer())->encode(['Foo']));
    }

    public function testEncodeStdClass(): void
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        static::assertSame(['@class' => 'stdClass', 'foo' => 'bar'], (new Normalizer())->encode($object));
    }

    public function testEncodeObjectWithPrivateProperty(): void
    {
        static::assertSame(['@class' => __NAMESPACE__ . '\\A', 'foo' => 'bar'], (new Normalizer())->encode(new A()));
    }

    public function testEncodeObjectWithSleepMethod(): void
    {
        static::assertSame(['@class' => __NAMESPACE__ . '\\B', 'foo' => 'bar'], (new Normalizer())->encode(new B()));
    }

    public function testDecodeThrowsExceptionOnObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Normalizer())->decode(new \stdClass());
    }

    public function testDecodeThrowsExceptionOnResource(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Normalizer())->decode(fopen('php://memory', 'rb'));
    }

    public function testDecodeThrowsExceptionOnClosure(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Normalizer())->decode(function () {
        });
    }

    public function testDecodeThrowsExceptionOnUnknownClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Normalizer())->decode(['@class' => __NAMESPACE__ . '\\Z']);
    }

    public function testDecodeScalar(): void
    {
        static::assertSame(['A', 1, 1.1, true], (new Normalizer())->decode(['A', 1, 1.1, true]));
    }

    public function testDecodeStdClass(): void
    {
        $decoded = (new Normalizer())->decode(['@class' => \stdClass::class, 'foo' => 'bar2']);

        static::assertInstanceOf(\stdClass::class, $decoded);
        static::assertSame('bar2', $decoded->foo);
    }

    public function testDecodeObject(): void
    {
        $decoded = (new Normalizer())->decode(['@class' => __NAMESPACE__ . '\\B', 'foo' => 'bar2']);

        static::assertInstanceOf(B::class, $decoded);
        static::assertSame('bar2', $decoded->getFoo());
    }

    public function testDecodeObjectsArray(): void
    {
        $decoded = (new Normalizer())->decode([
            ['@class' => __NAMESPACE__ . '\\B', 'foo' => 'bar2'],
            ['@class' => __NAMESPACE__ . '\\B', 'foo' => 'bar3'],
        ]);

        static::assertInstanceOf(B::class, $decoded[0]);
        static::assertInstanceOf(B::class, $decoded[1]);
        static::assertSame('bar2', $decoded[0]->getFoo());
        static::assertSame('bar3', $decoded[1]->getFoo());
    }
}


class A
{
    protected $foo = 'bar';

    public function getFoo(): string
    {
        return $this->foo;
    }
}

class B
{
    protected $foo = 'bar';

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function __sleep()
    {
        return ['foo'];
    }

    public function __wakeup()
    {}
}
