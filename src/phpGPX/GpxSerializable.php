<?php

namespace phpGPX;

interface GpxSerializable
{
    public static function gpxSerialize(\SimpleXMLElement $node): void;

    public function gpxDeserialize(\DOMDocument &$document): void;
}
