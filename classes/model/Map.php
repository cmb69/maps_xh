<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Maps_XH.
 *
 * Maps_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Maps_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Maps_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Maps\Model;

use DOMDocument;
use DOMElement;
use Plib\Document;
use Plib\DocumentStore;

final class Map implements Document
{
    private float $longitude;
    private float $latitude;
    private int $zoom;
    private int $maxZoom;

    public static function fromString(string $contents, string $key): ?self
    {
        $that = new self(0, 0, 0, 0);
        if ($contents === "") {
            return $that;
        }
        $doc = new DOMDocument("1.0", "UTF-8");
        if (!$doc->loadXML($contents)) {
            return $that;
        }
        if (!$doc->relaxNGValidate(__DIR__ . "/../../map.rng")) {
            return $that;
        }
        assert($doc->documentElement instanceof DOMElement);
        $map = $doc->documentElement;
        $that->longitude = (float) $map->getAttribute("longitude");
        $that->latitude = (float) $map->getAttribute("latitude");
        $that->zoom = (int) $map->getAttribute("zoom");
        $that->maxZoom = (int) $map->getAttribute("maxZoom");
        return $that;
    }

    public static function retrieve(string $name, DocumentStore $store): ?self
    {
        return $store->retrieve("$name.xml", self::class);
    }

    public function __construct(float $longitude, float $latitude, int $zoom, int $maxZoom)
    {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->zoom = $zoom;
        $this->maxZoom = $maxZoom;
    }

    public function toString(): string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $map = $doc->createElement('map');
        $map->setAttribute("longitude", (string) $this->longitude);
        $map->setAttribute("latitude", (string) $this->latitude);
        $map->setAttribute("zoom", (string) $this->zoom);
        $map->setAttribute("maxZoom", (string) $this->maxZoom);
        $doc->appendChild($map);
        if (!$doc->relaxNGValidate(__DIR__ . "/../../map.rng")) {
            return "";
        }
        $doc->formatOutput = true;
        return (string) $doc->saveXML();
    }
}
