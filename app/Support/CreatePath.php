<?php

declare (strict_types = 1);

namespace WPEmergeMagic\Support;

class CreatePath
{
    public function create(string $root = '/', array $paths = [], $endWithSeparator = true)
    {
        foreach ($paths as $path) {
            if (!$this->endsWith(DIRECTORY_SEPARATOR, $root)) {
                $root .= DIRECTORY_SEPARATOR;
            }

            $root .= $path;
        }

        if ($endWithSeparator) {
            $root .= DIRECTORY_SEPARATOR;
        }

        return $root;
    }

    protected function endsWith(string $key, string $string)
    {
        $length = strlen($string);

        if ($length === 0) {
            return true;
        }

        return (substr($string, -$length) === $key);
    }
}
