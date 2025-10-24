<?php

/**
 * Copyright (c) Netzwolf
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

/**
 * @see https://www.netzwolf.info/karten/karte-ohne-javascript.html
 * @phpstan-type Tile object{tileX:int,tileY:int,zoom:int,x:int,y:int}
 */
class TileCalculator
{
    private const TILE_PIXEL_SIZE = 256;

    /** @return list<Tile> */
    public function calculate(
        float $centerLat,
        float $centerLon,
        int $zoom,
        int $mapPixelWidth,
        int $mapPixelHeight
    ): array {
        [$centerX, $centerY] = $this->lonLatToXY($centerLon, $centerLat);
        $zoomScale = pow(2, $zoom);
        $worldPixelSize = $zoomScale * self::TILE_PIXEL_SIZE;
        $pixelLeft = (int) round($centerX * $worldPixelSize - $mapPixelWidth  / 2);
        $pixelTop = (int) round($centerY * $worldPixelSize - $mapPixelHeight / 2);
        $pixelRight = $pixelLeft + $mapPixelWidth;
        $pixelBottom = $pixelTop  + $mapPixelHeight;
        $topLeftTileX = intdiv($pixelLeft, self::TILE_PIXEL_SIZE);
        $topLeftTileY = intdiv($pixelTop, self::TILE_PIXEL_SIZE);
        $result = [];
        for ($tileY = $topLeftTileY; $tileY * self::TILE_PIXEL_SIZE < $pixelBottom; $tileY++) {
            if ($tileY < 0 || $tileY >= $zoomScale) {
                continue;
            }
            $yPosition = $tileY * self::TILE_PIXEL_SIZE - $pixelTop;
            for ($tileX = $topLeftTileX; $tileX * self::TILE_PIXEL_SIZE < $pixelRight; $tileX++) {
                if ($tileX < 0 || $tileX >= $zoomScale) {
                    continue;
                }
                $xPosition = $tileX * self::TILE_PIXEL_SIZE - $pixelLeft;
                $result[] = (object) [
                    "tileX" => $tileX,
                    "tileY" => $tileY,
                    "zoom" => $zoom,
                    "x" => $xPosition,
                    "y" => $yPosition,
                ];
            }
        }
        return $result;
    }

    /** @return array{float,float} */
    private function lonLatToXY(float $lon, float $lat): array
    {
        $rho  = 180 / M_PI;
        $lon -= 360 * floor(($lon + 180) / 360);
        $lambda  = $lon / $rho;
        $phi     = $this->limit($lat, -86, +86) / $rho;
        $mercPhi = log(tan($phi) + 1 / cos($phi));
        return [
            $this->limit((1 + $lambda / M_PI) / 2, 0, 1),
            $this->limit((1 - $mercPhi / M_PI) / 2, 0, 1)
        ];
    }

    private function limit(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
