<?php

namespace WPEmergeMagic\Composers;

class JsComposer
{
    public function compose(array $jsData): string
    {
        // convert to json
        $encodedData = json_encode($jsData, JSON_PRETTY_PRINT);

        // remove double quotes from object keys and check if the key can remove the double quotes else use single quotes
        $encodedData = preg_replace('~(?<!:)(?<!,)(\s+)(")([\w\$]+)("):~', '$1$3:', $encodedData);

        // remove double quotes from functions
        $encodedData = preg_replace('~"~', '\'', $encodedData);

        // return
        return $encodedData;
    }
}
