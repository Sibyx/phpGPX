<?php

namespace phpGPX;

interface GpxSerializable
{
    public static function gpxSerialize(\SimpleXMLElement $node);

    public function gpxDeserialize(\DOMDocument &$document);
}