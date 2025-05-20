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
use DOMNode;
use Plib\Document;
use Plib\DocumentStore;

final class Map implements Document
{
    private string $name;
    private float $latitude;
    private float $longitude;
    private int $zoom;
    private int $maxZoom;
    /** @var list<Marker> */
    private array $markers = [];

    public static function fromString(string $contents, string $key): self
    {
        $that = new self(basename($key, ".xml"), 0, 0, 0, 0);
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
        $that->latitude = (float) $map->getAttribute("latitude");
        $that->longitude = (float) $map->getAttribute("longitude");
        $that->zoom = (int) $map->getAttribute("zoom");
        $that->maxZoom = (int) $map->getAttribute("maxZoom");
        foreach ($map->childNodes as $childNode) {
            assert($childNode instanceof DOMNode);
            if ($childNode->nodeName === "marker") {
                assert($childNode instanceof DOMElement);
                $that->markers[] = Marker::fromXml($childNode);
            }
        }
        return $that;
    }

    public static function retrieve(string $name, DocumentStore $store): self
    {
        $that = $store->retrieve("$name.xml", self::class);
        assert($that instanceof self);
        return $that;
    }

    public static function update(string $name, DocumentStore $store): self
    {
        $that = $store->update("$name.xml", self::class);
        assert($that instanceof self);
        return $that;
    }

    public function __construct(string $name, float $latitude, float $longitude, int $zoom, int $maxZoom)
    {
        $this->name = $name;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->zoom = $zoom;
        $this->maxZoom = $maxZoom;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function zoom(): int
    {
        return $this->zoom;
    }

    public function maxZoom(): int
    {
        return $this->maxZoom;
    }

    /** @return list<Marker> */
    public function markers(): array
    {
        return $this->markers;
    }

    public function setCoordinates(float $latitude, float $longitude): void
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function setZoom(int $zoom, int $maxZoom): void
    {
        $this->zoom = $zoom;
        $this->maxZoom = $maxZoom;
    }

    public function purgeMarkers(): void
    {
        $this->markers = [];
    }

    public function addMarker(float $latitude, float $longitude, string $info, bool $showInfo): Marker
    {
        $marker = new Marker($latitude, $longitude, $info, $showInfo);
        $this->markers[] = $marker;
        return $marker;
    }

    public function toString(): string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $map = $doc->createElement('map');
        $map->setAttribute("latitude", (string) $this->latitude);
        $map->setAttribute("longitude", (string) $this->longitude);
        $map->setAttribute("zoom", (string) $this->zoom);
        $map->setAttribute("maxZoom", (string) $this->maxZoom);
        $doc->appendChild($map);
        foreach ($this->markers as $marker) {
            $map->appendChild($marker->toXml($doc));
        }
        if (!$doc->relaxNGValidate(__DIR__ . "/../../map.rng")) {
            return "";
        }
        $doc->formatOutput = true;
        return (string) $doc->saveXML();
    }
}
