<?php

declare (strict_types = 1);

namespace WPEmergeMagic\Parsers;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class StubParser
{
    /**
     * Parse content with arguments
     *
     * @param string $content
     * @param array $arguments
     * @return string
     */
    public function parse($content, array $arguments)
    {
        if (!$content) {
            throw new \InvalidArgumentException('No content is passed!');
        }

        foreach ($arguments as $key => $value) {
            $content = preg_replace('~\{' . $key . '\}~', $value, $content);
        }

        return $content;
    }

    /**
     * Parse content by passing stub name
     * and automatically will resolve the content
     *
     * @param string $stubName
     * @param array $arguments
     * @return string
     */
    public function parseViaStub(string $stubName, array $arguments)
    {
        $stubFile = ROOT_DIR . 'stubs' . DIRECTORY_SEPARATOR . $stubName . '.stub';

        if (!file_exists($stubFile)) {
            throw new FileNotFoundException('Stub named "' . $stubName . '" cannot be found');
        }

        return $this->parse(\file_get_contents($stubFile), $arguments);
    }
}
