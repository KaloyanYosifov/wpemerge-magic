<?php

namespace WPEmergeMagic\Support;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class FileReader
{
    /**
     * Generator to read a line from file every time
     *
     * @param string $fileToOpen
     * @return string
     * @throws Symfony\Component\Filesystem\Exception\FileNotFoundException
     * @throws \LogicException
     */
    public function readLines(string $fileToOpen)
    {
        if (!file_exists($fileToOpen)) {
            throw new FileNotFoundException("File with path $fileToOpen couldn't be opened.");
        }

        $file = fopen($fileToOpen, 'r');

        if (!$file) {
            throw new \LogicException('Couldn\'t open file.');
        }

        while (($line = fgets($file, 4096)) !== false) {
            yield $line;
        }

        if (!feof($file)) {
            throw new \LogicException('File unexpectedly finished!');
        }

        fclose($file);
    }
}
