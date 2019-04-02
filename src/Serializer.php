<?php

namespace PE\Component\Cronos\Core;

final class Serializer implements SerializerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @param NormalizerInterface|null $normalizer
     */
    public function __construct(NormalizerInterface $normalizer = null)
    {
        $this->normalizer = $normalizer ?: new Normalizer();
    }

    /**
     * @return NormalizerInterface
     */
    public function getNormalizer(): NormalizerInterface
    {
        return $this->normalizer;
    }

    /**
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @inheritDoc
     */
    public function encode($value): string
    {
        return json_encode($this->normalizer->encode($value), JSON_PRESERVE_ZERO_FRACTION|JSON_UNESCAPED_SLASHES);
    }

    /**
     * @inheritDoc
     */
    public function decode(string $value)
    {
        return $this->normalizer->decode(json_decode($value, true));
    }
}
