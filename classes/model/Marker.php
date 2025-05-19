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

final class Marker
{
    private float $latitude;
    private float $longitude;

    public static function fromXml(DOMElement $elt): self
    {
        return new self(
            (float) $elt->getAttribute("latitude"),
            (float) $elt->getAttribute("longitude")
        );
    }

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function toXml(DOMDocument $doc): DOMElement
    {
        $elt = $doc->createElement("marker");
        $elt->setAttribute("latitude", (string) $this->latitude);
        $elt->setAttribute("longitude", (string) $this->longitude);
        return $elt;
    }
}
