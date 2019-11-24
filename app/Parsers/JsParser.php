<?php

namespace WPEmergeMagic\Parsers;

class JsParser
{
    public function parse(string $jsObject)
    {
        $lines = explode(PHP_EOL, $jsObject);
        $lineCount = count($lines);
        $newLines = '';
        $addTrailingComma = true;

        for ($lineIndex = 0; $lineIndex < $lineCount; $lineIndex++) {
            $line = preg_replace('~"~', '', $lines[$lineIndex]);

            // get all object keys
            // and replace them with the same key but without the quotes
            $line = preg_replace('~(\'|")(.*)(\'|"):~', '$2:', $line);

            if (strpos($line, '/') !== false) {
                $line = $this->wrapRegex($line);
            }

            // if the next line is the last line
            // we try to figure out line count by adding 2, so we compensate for starting from 0
            // we start from 0 and get the first line
            // but the count starts from 1
            // so if we want to get the next line we have to add 1 surplus due to this difference
            $isNextLineEnd = $lineIndex + 2 === $lineCount;
            $atEndLine = $lineIndex + 2 > $lineCount;
            $nextLine = !$atEndLine ? $lines[$lineIndex + 1] : '';
            $addTrailingComma = true;

            if (preg_match('~\{|\[$~', $line)) {
                $addTrailingComma = true;
            }

            // if after all the space
            // we start with a closed curly bracket
            // do not add a triling comma
            if (preg_match('~^\s+\}|^\s+\]~', $nextLine)) {
                $addTrailingComma = false;
                $isClosedObject = true;
            }

            if (strpos($line, '(') !== false) {
                $newLines .= $this->parseFunctionLineInJsObject($line, $addTrailingComma);
                continue;
            }

            // replace all single quotes to double quotes
            $line = preg_replace('~\'~', '"', $line);

            // if the next line is the last line
            // check if we have a trailing comma
            // and if we do remove it
            if ($isNextLineEnd) {
                $line = preg_replace('~,$~', '', $line);
            }

            if (!$addTrailingComma) {
                $line = preg_replace('~,$~', '', $line);
            }

            $newLines .= $this->addDoubleQuotesToObjects($line) . PHP_EOL;
        }

        return json_decode($newLines, true);
    }

    protected function parseFunctionLineInJsObject(string $line, bool $addTrailingComma): string
    {
        if (strpos($line, ':') === false) {
            return preg_replace('~(\s+)(.*)\),?~', $addTrailingComma ? '$1"$2)",' : '$1"$2)"', $line) . PHP_EOL;
        }

        $splittedLineByObject = explode(':', $line);

        // we are poping the object from the spllited line objects
        // so we can wrap it in double quotes
        // and we are not going to touch the right half
        $objectName = $this->addDoubleQuotesToObjects(
            array_shift($splittedLineByObject) . ':'
        );

        // glue the right side and remove the space on the start
        $gluedRightSide = preg_replace('~^\s|,$~', '', implode(':', $splittedLineByObject));

        // add double qoutes to the enire right thalf of the splitted line objects
        $parsedLine = $objectName . ' "' . $gluedRightSide . '"';

        if ($addTrailingComma) {
            $parsedLine .= ',';
        }

        return $parsedLine . PHP_EOL;
    }

    protected function addDoubleQuotesToObjects(string $line): string
    {
        $line = preg_replace('~(?<!:)(?<!,)(\s+)(["\w\$]*):~', '$1"$2":', $line);

        return $line;
    }

    protected function wrapRegex(string $line): string
    {
        // wrap regex with qoutes
        $line = preg_replace('~\s\/(.*)\/([\w]*)(,*)~', '"/$1/$2"$3', $line);

        return preg_replace('~\\\~', '\\\\\\', $line);
    }
}
