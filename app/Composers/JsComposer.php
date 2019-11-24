<?php

namespace WPEmergeMagic\Composers;

class JsComposer
{
    public function compose(array $jsData): string
    {
        // convert to json
        $encodedData = json_encode($jsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // remove double quotes from object keys and check if the key can remove the double quotes else use single quotes
        $encodedData = preg_replace('~(?<!:)(?<!,)(\s+)(")([\w\$]+)("):~', '$1$3:', $encodedData);

        // loop through all lines
        // so we find js functions and remove the quotes around them
        $encodedDataSplittedIntoLines = explode(PHP_EOL, $encodedData);
        $lineIndex = 0;
        $encodedData = '';

        foreach ($encodedDataSplittedIntoLines as $line) {
            if (strpos($line, '(') !== false) {
                $line = $this->parseFunctionLine($line);
            }

            if (strpos($line, '/') !== false) {
                $line = $this->parseRegex($line);
            }

            // remove double quotes from functions
            $encodedData .= preg_replace('~"~', '\'', $line);

            // check if we are not on the last line
            // if that is the case do not append an end line
            if ($lineIndex + 1 < count($encodedDataSplittedIntoLines)) {
                $encodedData .= PHP_EOL;
            }

            $lineIndex++;
        }

        // return
        return $encodedData;
    }

    protected function parseFunctionLine(string $line): string
    {
        if (strpos($line, ':') === false) {
            return preg_replace('~(\s+)(\'|")(.*)\)(\'|")(,?)~', '$1$3)$5', $line);
        }

        $splittedLineByObject = explode(':', $line);

        $objectName = array_shift($splittedLineByObject) . ':';

        // glue the right side and remove the space on the start
        $gluedRightSide = preg_replace('~^\s~', '', implode(':', $splittedLineByObject));

        // remove double qoutes on end and on begining
        $gluedRightSide = preg_replace('~^"|",?$~', '', $gluedRightSide);
        // we glue all things together
        // and we add comma on the stirng
        // since we removed it along with the double quotes
        return $objectName . ' ' . $gluedRightSide . ',';
    }

    protected function parseRegex(string $line): string
    {
        return preg_replace('~(\'|")\/(.*)\/([\w]*)(\'|")~', '/$2/$3', $line);
    }
}
