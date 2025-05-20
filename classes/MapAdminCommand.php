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
use Plib\DocumentStore2 as DocumentStore;
use Plib\Request;
use Plib\Response;
use Plib\View;

class MapAdminCommand
{
    private DocumentStore $store;
    private View $view;

    public function __construct(DocumentStore $store, View $view)
    {
        $this->store = $store;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        switch ($request->get("action")) {
            default:
                return $this->read($request);
            case "create":
                return $this->create($request);
            case "update":
                return $this->update($request);
        }
    }

    private function read(Request $request): Response
    {
        $maps = $this->store->find('/[a-z0-9\-]+\.xml$/');
        return Response::create($this->view->render("maps_admin", [
            "maps" => $this->mapDtos($request, $maps),
        ]))->withTitle("Maps – " . $this->view->text("menu_main"));
    }

    /**
     * @param list<string> $maps
     * @return list<object{name:string,checked:string}>
     */
    private function mapDtos(Request $request, array $maps): array
    {
        $res = [];
        foreach ($maps as $map) {
            $name = basename($map, ".xml");
            $res[] = (object) [
                "name" => $name,
                "checked" => $request->get("maps_map") === $name ? "checked" : "",
            ];
        }
        return $res;
    }

    private function create(Request $request): Response
    {
        if ($request->post("maps_do") !== null) {
            return $this->doCreate($request);
        }
        $dto = new MapDto("", 0, 0, 0, 0, "");
        return $this->respondWithEditor(true, $dto);
    }

    private function doCreate(Request $request): Response
    {
        $map = Map::create($request->post("name") ?? "", $this->store);
        $dto = $this->dtoFromRequest($request);
        $this->updateMapFromDto($map, $dto);
        if (!$this->store->commit()) {
            return $this->respondWithEditor(true, $dto, [$this->view->message("fail", "error_save")]);
        }
        return Response::redirect($request->url()->without("action")->absolute());
    }

    private function update(Request $request): Response
    {
        if ($request->post("maps_do") !== null) {
            return $this->doUpdate($request);
        }
        if ($request->get("maps_map") === null) {
            return Response::create("no map selected");
        }
        $map = Map::read($request->get("maps_map"), $this->store);
        if ($map === null) {
            return Response::create("no such map");
        }
        $dto = $this->mapToDto($map);
        return $this->respondWithEditor(false, $dto);
    }

    private function doUpdate(Request $request): Response
    {
        $map = Map::update($request->get("maps_map") ?? "", $this->store);
        if ($map === null) {
            return Response::create("no such map");
        }
        $dto = $this->dtoFromRequest($request);
        $this->updateMapFromDto($map, $dto);
        if (!$this->store->commit()) {
            return $this->respondWithEditor(false, $dto, [$this->view->message("fail", "error_save")]);
        }
        return Response::redirect($request->url()->without("action")->absolute());
    }

    private function mapToDto(Map $map): MapDto
    {
        $lines = [];
        foreach ($map->markers() as $marker) {
            $lines[] = $marker->latitude() .  "|" . $marker->longitude() . "|" . $marker->info()
                . "|" . $marker->showInfo();
        }
        return new MapDto(
            $map->name(),
            $map->latitude(),
            $map->longitude(),
            $map->zoom(),
            $map->maxZoom(),
            implode("\n", $lines) . "\n"
        );
    }

    private function dtoFromRequest(Request $request): MapDto
    {
        return new MapDto(
            $request->post("name") ?? $request->get("maps_map") ?? "",
            (float) ($request->post("latitude") ?? ""),
            (float) ($request->post("longitude") ?? ""),
            (int) ($request->post("zoom") ?? ""),
            (int) ($request->post("max_zoom") ?? ""),
            $request->post("markers") ?? ""
        );
    }

    private function updateMapFromDto(Map $map, MapDto $dto): void
    {
        $map->setCoordinates((float) $dto->latitude, (float) $dto->longitude);
        $map->setZoom((int) $dto->zoom, (int) $dto->maxZoom);
        $map->purgeMarkers();
        $lines = preg_split('/\r?\n/', $dto->markers);
        if ($lines === false) {
            return;
        }
        foreach ($lines as $line) {
            $fields = explode("|", $line);
            if (count($fields) === 4) {
                $map->addMarker((float) $fields[0], (float) $fields[1], $fields[2], (bool) $fields[3]);
            }
        }
    }

    /** @param list<string> $errors */
    private function respondWithEditor(bool $new, MapDto $dto, array $errors = []): Response
    {
        return Response::create($this->view->render("map_edit", [
            "errors" => $errors,
            "name_disabled" => $new ? "" : "disabled",
            "map" => $dto,
        ]))->withTitle("Maps – " . $this->view->text("menu_main"));
    }
}
