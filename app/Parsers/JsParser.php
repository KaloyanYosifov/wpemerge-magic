<?php

namespace WPEmergeMagic\Parsers;

class JsParser
{
    public function parse(string $jsObject)
    {
        $lines = explode(PHP_EOL, $jsObject);
        $newLines = '';

        foreach ($lines as $line) {
            $line = preg_replace('~\'~', '"', $line);
            $line = preg_replace('~(?<!:)(?<!,)(\s+)(.*?):~', '$1"$2":', $line);
            $newLines .= $line . PHP_EOL;
        }

        return json_decode($newLines, true);
    }
}
