<?php

namespace WPEmergeMagic\Parsers;

class JsParser
{
    protected $objects = [];
    protected $closedObjects = [];
    protected $keysToAssign = [];
    protected $arrayValues = [];
    protected $keyDataAssignment = false;

    /**
     * Parse js objects to php arrays
     *
     * @return array
     */
    public function parse(string $jsObject): array
    {
        $data = '';

        for ($jsObjectIndex = 0; $jsObjectIndex < strlen($jsObject); $jsObjectIndex++) {
            $character = $jsObject[$jsObjectIndex];

            if ($character === '') {
                continue;
            }

            if ($character === '{' || $character === '[') {
                $this->createObject();

                continue;
            }

            if ($character === ':') {
                $this->keyDataAssignment = true;
                $this->keysToAssign[] = $data;
                $data = '';

                continue;
            }

            if ($character === ',') {
                $this->arrayValues[] = preg_replace('~\'|\s+~', '', $data);
                $data = '';

                continue;
            }

            if ($character === '}') {
                if ($this->keyDataAssignment) {
                    $this->arrayValues = [
                        $this->keysToAssign[0] => $this->arrayValues,
                    ];
                    $this->keyDataAssignment = false;
                }

                $this->closeCurrentObject();
                continue;
            }

            if ($character === ']') {
                $this->closeCurrentObject();
                break;
            }

            $data .= $character;
        }

    }

    /** @test */
    public function createObject()
    {
        $this->objects[] = [];
    }

    public function closeCurrentObject()
    {
        $this->objects[count($this->objects) - 1] = $this->arrayValues;
        $this->closedObjects[] = array_pop($this->objects);
    }
}
