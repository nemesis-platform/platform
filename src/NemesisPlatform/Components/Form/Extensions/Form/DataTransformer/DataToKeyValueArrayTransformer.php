<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.07.2014
 * Time: 15:07
 */

namespace NemesisPlatform\Components\Form\Extensions\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;

class DataToKeyValueArrayTransformer implements DataTransformerInterface
{

    /** {@inheritdoc} */
    public function transform($value)
    {
        if (!$value || !is_array($value) || count($value) === 0) {
            return array();
        }

        $data = array();

        foreach ($value as $key => $val) {
            $data[$key] = array('key' => $key, 'value' => $val);
        }

        return $data;
    }

    /** {@inheritdoc} */
    public function reverseTransform($value)
    {
        if (!$value || !is_array($value) || count($value) === 0) {
            return array();
        }

        $result = array();
        foreach ($value as $row) {
            $result[$row['key']] = $row['value'];
        }

        return $result;
    }
}
