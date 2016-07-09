<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-05-26
 * Time: 22:41
 */

namespace NemesisPlatform\Admin\Exporter;

abstract class AbstractCSVExporter
{
    /** @var string */
    protected $encoding = 'windows-1251';
    /** @var string */
    protected $delimiter = ';';
    /** @var string */
    protected $enclosure = '"';

    /**
     * @param string[] $chunks
     *
     * @return string[]
     */
    protected function escapeChunks($chunks)
    {
        $escaped = [];

        foreach ($chunks as $chunk) {
            $escaped[] = $this->escape($chunk);
        }

        return $escaped;
    }

    /**
     * @param string $chunk
     *
     * @return string
     */
    private function escape($chunk)
    {
        $delimiterEsc = preg_quote($this->delimiter, '/');
        $enclosureEsc = preg_quote($this->enclosure, '/');

        $chunk = mb_convert_encoding($chunk, $this->encoding, 'utf-8');

        return preg_match("/(?:${delimiterEsc}|${enclosureEsc}|\s)/", $chunk) ? (
            $this->enclosure.str_replace(
                $this->enclosure,
                $this->enclosure.$this->enclosure,
                $chunk
            ).$this->enclosure
        ) : $chunk;
    }
}
