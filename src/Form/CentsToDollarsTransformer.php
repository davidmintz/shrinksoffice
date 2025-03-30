<?php

namespace App\Form;
use Symfony\Component\Form\DataTransformerInterface;

class CentsToDollarsTransformer implements DataTransformerInterface
{
    public function transform($value): int|null
    {
        // Transform from cents to dollars
        return $value !== null ? $value / 100 : null;
    }

    public function reverseTransform($value):  int|null
    {
        // Transform from dollars to cents
        return $value !== null ? (int) round($value * 100) : null;
    }
}
