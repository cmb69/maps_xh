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

namespace Maps;

class MapDto
{
    public string $name;
    public string $title;
    public float $latitude;
    public float $longitude;
    public int $zoom;
    public int $maxZoom;
    public string $aspectRatio;
    public string $markers;

    public function __construct(
        string $name,
        string $title,
        float $latitude,
        float $longitude,
        int $zoom,
        int $maxZoom,
        string $aspectRatio,
        string $markers
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->zoom = $zoom;
        $this->maxZoom = $maxZoom;
        $this->aspectRatio = $aspectRatio;
        $this->markers = $markers;
    }
}
