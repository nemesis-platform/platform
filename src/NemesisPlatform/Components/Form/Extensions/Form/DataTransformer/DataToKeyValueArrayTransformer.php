<?php

namespace NemesisPlatform\Components\Form\Extensions\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DataToKeyValueArrayTransformer implements DataTransformerInterface
{

    /** {@inheritdoc} */
    public function transform($value)
    {
        if (!$value || !is_array($value) || count($value) === 0) {
            return [];
        }

        $data = [];

        foreach ($value as $key => $val) {
            $data[$key] = ['key' => $key, 'value' => $val];
        }

        return $data;
    }

    /** {@inheritdoc} */
    public function reverseTransform($value)
    {
        if (!$value || !is_array($value) || count($value) === 0) {
            return [];
        }

        $result = [];
        foreach ($value as $row) {
            $result[$row['key']] = $row['value'];
        }

        return $result;
    }
}
