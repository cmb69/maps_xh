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

use Maps\Model\Map;
use Plib\DocumentStore;
use Plib\Request;
use Plib\Response;
use Plib\View;

class MapCommand
{
    private string $pluginFolder;

    /** @var array<string,string> */
    private array $conf;

    private DocumentStore $store;

    private View $view;

    /** @param array<string,string> $conf */
    public function __construct(
        string $pluginFolder,
        array $conf,
        DocumentStore $store,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->store = $store;
        $this->view = $view;
    }

    public function __invoke(string $name, Request $request): Response
    {
        if ($request->post("maps_agree")) {
            return $this->agree($request);
        }
        $map = Map::retrieve($name, $this->store);
        if ($map === null) {
            return Response::create("invalid map");
        }
        return Response::create($this->view->render("map", [
            "script" => $this->pluginFolder . "maps.js",
            "conf" => $this->jsConf($request, $map),
            "privacy" => $this->tilePrivacy($request),
        ]));
    }

    /** @return array<string,mixed> */
    private function jsConf(Request $request, Map $map): array
    {
        return [
            "tileUrl" => $this->conf["tile_url"],
            "tileAttribution" => $this->view->plain("tile_attribution"),
            "loadTiles" => !$this->tilePrivacy($request),
            "latitude" => $map->latitude(),
            "longitude" => $map->longitude(),
            "zoom" => $map->zoom(),
            "maxZoom" => $map->maxZoom(),
            "markers" => $this->markerTuples($map),
        ];
    }

    private function tilePrivacy(Request $request): bool
    {
        return $this->conf["tile_privacy"] && !$request->cookie("maps_agreed");
    }

    /** @return list<array{float,float,?string,bool}> */
    private function markerTuples(Map $map): array
    {
        $tuples = [];
        foreach ($map->markers() as $marker) {
            $tuples[] = [$marker->latitude(), $marker->longitude(), $marker->info(), $marker->showInfo()];
        }
        return $tuples;
    }

    private function agree(Request $request): Response
    {
        return Response::redirect($request->url()->absolute())
            ->withCookie("maps_agreed", "1", 0);
    }
}
