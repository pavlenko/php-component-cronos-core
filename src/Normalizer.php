<?php

namespace PE\Component\Cronos\Core;

final class Normalizer implements NormalizerInterface
{
    /**
     * @var \ReflectionClass[]
     */
    private $reflections = [];

    /**
     * @inheritDoc
     */
    public function encode($value)
    {
        if (is_resource($value) || $value instanceof \Closure) {
            throw new \InvalidArgumentException('Cannot serialize resources and closures');
        }

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        if (is_array($value)) {
            return array_map([$this, 'encode'], $value);
        }

        return $this->encodeObject($value);
    }

    /**
     * @param object $value
     *
     * @return array
     */
    private function encodeObject($value): array
    {
        $r = $this->resolveReflection(get_class($value));

        $data = ['@class' => $r->getName()];

        if (method_exists($value, '__sleep')) {
            $properties = $value->__sleep();
        } else {
            $properties = [];

            foreach ($r->getProperties() as $p) {
                $properties[] = $p->getName();
            }

            $properties = array_unique(array_merge($properties, array_keys(get_object_vars($value))));
        }

        foreach ($properties as $property) {
            try {
                $p = $r->getProperty($property);
                $p->setAccessible(true);

                $data[$property] = $p->getValue($value);
            } catch (\ReflectionException $e) {
                $data[$property] = $value->{$property};
            }
        }

        return array_map([$this, 'encode'], $data);
    }

    /**
     * @inheritDoc
     */
    public function decode($value)
    {
        if (is_scalar($value) || $value === null) {
            return $value;
        }

        if (is_array($value)) {
            if (array_key_exists('@class', $value)) {
                return $this->decodeObject($value);
            }

            return array_map([$this, 'decode'], $value);
        }

        throw new \InvalidArgumentException(
            'Cannot decode type ' . (is_object($value) ? get_class($value) : gettype($value))
        );
    }

    /**
     * @param array $value
     *
     * @return object
     */
    private function decodeObject($value)
    {
        $class = $value['@class'];

        unset($value['@class']);

        $r = $this->resolveReflection($class);
        $o = $r->newInstanceWithoutConstructor();

        foreach ($value as $property => $data) {
            try {
                $p = $r->getProperty($property);
                $p->setAccessible(true);
                $p->setValue($o, $this->decode($data));
            } catch (\ReflectionException $ex) {
                $o->{$property} = $this->decode($data);
            }
        }

        if (method_exists($o, '__wakeup')) {
            $o->__wakeup();
        }

        return $o;
    }

    /**
     * @param string $class
     *
     * @return \ReflectionClass
     */
    private function resolveReflection(string $class): \ReflectionClass
    {
        if (array_key_exists($class, $this->reflections)) {
            return $this->reflections[$class];
        }

        try {
            $r = $this->reflections[$class] = new \ReflectionClass($class);
        } catch (\ReflectionException $ex) {
            throw new \InvalidArgumentException($ex->getMessage(), 0, $ex);
        }

        return $r;
    }
}
