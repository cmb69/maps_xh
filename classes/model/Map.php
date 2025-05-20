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
use Plib\Document2 as Document;
use Plib\DocumentStore2 as DocumentStore;

final class Map implements Document
{
    private string $name;
    private string $title;
    private float $latitude;
    private float $longitude;
    private int $zoom;
    private int $maxZoom;
    private string $aspectRatio;
    /** @var list<Marker> */
    private array $markers = [];

    public static function new(string $key): self
    {
        return new self(basename($key, ".xml"), "", 0, 0, 0, 0, "1/1");
    }

    public static function fromString(string $contents, string $key): ?self
    {
        if ($contents === "") {
            return null;
        }
        $doc = new DOMDocument("1.0", "UTF-8");
        if (!$doc->loadXML($contents)) {
            return null;
        }
        if (!$doc->relaxNGValidate(__DIR__ . "/../../map.rng")) {
            return null;
        }
        assert($doc->documentElement instanceof DOMElement);
        $map = $doc->documentElement;
        $that = new self(
            basename($key, ".xml"),
            $map->getAttribute("title"),
            (float) $map->getAttribute("latitude"),
            (float) $map->getAttribute("longitude"),
            (int) $map->getAttribute("zoom"),
            (int) $map->getAttribute("maxZoom"),
            $map->getAttribute("aspectRatio")
        );
        foreach ($map->childNodes as $childNode) {
            assert($childNode instanceof DOMNode);
            if ($childNode->nodeName === "marker") {
                assert($childNode instanceof DOMElement);
                $that->markers[] = Marker::fromXml($childNode);
            }
        }
        return $that;
    }

    public static function create(string $name, DocumentStore $store): self
    {
        $that = $store->create("$name.xml", self::class);
        assert($that instanceof self);
        return $that;
    }

    public static function read(string $name, DocumentStore $store): ?self
    {
        return $store->read("$name.xml", self::class);
    }

    public static function update(string $name, DocumentStore $store): ?self
    {
        return $store->update("$name.xml", self::class);
    }

    public function __construct(
        string $name,
        string $title,
        float $latitude,
        float $longitude,
        int $zoom,
        int $maxZoom,
        string $aspectRatio
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->zoom = $zoom;
        $this->maxZoom = $maxZoom;
        $this->aspectRatio = $aspectRatio;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function title(): string
    {
        return $this->title;
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

    public function aspectRatio(): string
    {
        return $this->aspectRatio;
    }

    /** @return list<Marker> */
    public function markers(): array
    {
        return $this->markers;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
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

    public function setAspectRatio(string $aspectRatio): void
    {
        $this->aspectRatio = $aspectRatio;
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

    /** @param array<mixed> $features */
    public function importGeoJsonFeatures(array $features, string $template, bool $replace): void
    {
        if (preg_match_all('/{([a-zA-Z]+)}/', $template, $matches, PREG_SET_ORDER) === false) {
            return;
        }
        if ($replace) {
            $this->purgeMarkers();
        }
        foreach ($features as $feature) {
            if (
                is_array($feature)
                && array_key_exists("geometry", $feature)
                && is_array($geometry = $feature["geometry"])
                && array_key_exists("type", $geometry) && $geometry["type"] === "Point"
                && array_key_exists("coordinates", $geometry)
                && is_array($coordinates = $geometry["coordinates"])
                && array_key_exists("properties", $feature)
                && is_array($properties = $feature["properties"])
            ) {
                [$longitude, $latitude] = $coordinates;
                $replacements = [];
                foreach ($matches as $match) {
                    if (array_key_exists($match[1], $properties)) {
                        $replacements[$match[0]] = $properties[$match[1]];
                    }
                }
                $info = strtr($template, $replacements);
                $this->addMarker($latitude, $longitude, $info, false);
            }
        }
    }

    public function toString(): string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $map = $doc->createElement('map');
        $map->setAttribute("title", $this->title);
        $map->setAttribute("latitude", (string) $this->latitude);
        $map->setAttribute("longitude", (string) $this->longitude);
        $map->setAttribute("zoom", (string) $this->zoom);
        $map->setAttribute("maxZoom", (string) $this->maxZoom);
        $map->setAttribute("aspectRatio", $this->aspectRatio);
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
