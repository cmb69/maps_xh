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
use Plib\CsrfProtector;
use Plib\DocumentStore2 as DocumentStore;
use Plib\Request;
use Plib\Response;
use Plib\View;

class MapAdminCommand
{
    private string $pluginFolder;
    private DocumentStore $store;
    private CsrfProtector $csrfProtector;
    private View $view;

    public function __construct(
        string $pluginFolder,
        DocumentStore $store,
        CsrfProtector $csrfProtector,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->store = $store;
        $this->csrfProtector = $csrfProtector;
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
            case "import":
                return $this->import($request);
        }
    }

    private function read(Request $request): Response
    {
        return $this->respondWithOverview($request);
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
        $dto = new MapDto("", "", 0, 0, 0, 0, "1/1", "");
        return $this->respondWithEditor(true, $dto, []);
    }

    private function doCreate(Request $request): Response
    {
        $map = Map::create($request->post("name") ?? "", $this->store);
        $dto = $this->dtoFromRequest($request);
        if (!$this->csrfProtector->check($request->post("maps_token"))) {
            $this->store->rollback();
            return $this->respondWithEditor(true, $dto, $this->markerDtos($map), [$this->view->message("fail", "error_not_authorized")]);
        }
        $this->updateMapFromDto($map, $dto);
        if (!$this->store->commit()) {
            return $this->respondWithEditor(true, $dto, $this->markerDtos($map), [$this->view->message("fail", "error_save", $dto->name)]);
        }
        return Response::redirect($request->url()->without("action")->absolute());
    }

    private function update(Request $request): Response
    {
        if ($request->post("maps_do") !== null) {
            return $this->doUpdate($request);
        }
        if ($request->get("maps_map") === null) {
            return $this->respondWithOverview($request, [$this->view->message("fail", "error_no_map")]);
        }
        $map = Map::read($request->get("maps_map"), $this->store);
        if ($map === null) {
            return $this->respondWithOverview($request, [
                $this->view->message("fail", "error_load", $request->get("maps_map"))
            ]);
        }
        $dto = $this->mapToDto($map);
        return $this->respondWithEditor(false, $dto, $this->markerDtos($map));
    }

    private function doUpdate(Request $request): Response
    {
        if ($request->get("maps_map") === null) {
            return $this->respondWithOverview($request, [$this->view->message("fail", "error_no_map")]);
        }
        $map = Map::update($request->get("maps_map"), $this->store);
        if ($map === null) {
            return $this->respondWithOverview($request, [
                $this->view->message("fail", "error_load", $request->get("maps_map"))
            ]);
        }
        $dto = $this->dtoFromRequest($request);
        if (!$this->csrfProtector->check($request->post("maps_token"))) {
            $this->store->rollback();
            return $this->respondWithEditor(true, $dto, $this->markerDtos($map), [$this->view->message("fail", "error_not_authorized")]);
        }
        $this->updateMapFromDto($map, $dto);
        if (!$this->store->commit()) {
            return $this->respondWithEditor(false, $dto, $this->markerDtos($map), [$this->view->message("fail", "error_save", $dto->name)]);
        }
        return Response::redirect($request->url()->without("action")->absolute());
    }

    private function import(Request $request): Response
    {
        if ($request->post("maps_do") !== null) {
            return $this->doImport($request);
        }
        if ($request->get("maps_map") === null) {
            return $this->respondWithOverview($request, [$this->view->message("fail", "error_no_map")]);
        }
        $map = Map::read($request->get("maps_map"), $this->store);
        if ($map === null) {
            return $this->respondWithOverview($request, [
                $this->view->message("fail", "error_load", $request->get("maps_map"))
            ]);
        }
        return $this->respondWithImportForm($map, "", "");
    }

    private function doImport(Request $request): Response
    {
        if ($request->get("maps_map") === null) {
            return $this->respondWithOverview($request, [$this->view->message("fail", "error_no_map")]);
        }
        $map = Map::update($request->get("maps_map"), $this->store);
        if ($map === null) {
            return $this->respondWithOverview($request, [
                $this->view->message("fail", "error_load", $request->get("maps_map"))
            ]);
        }
        $geojson = $request->post("geojson") ?? "";
        $template = $request->post("template") ?? "";
        if (!$this->csrfProtector->check($request->post("maps_token"))) {
            $this->store->rollback();
            $errors = [$this->view->message("fail", "error_not_authorized")];
            return $this->respondWithImportForm($map, $geojson, $template, $errors);
        }
        $json = json_decode($geojson, true);
        if (!is_array($json) || !array_key_exists("features", $json) || !is_array($json["features"])) {
            $this->store->rollback();
            $errors = [$this->view->message("fail", "error_geojson")];
            return $this->respondWithImportForm($map, $geojson, $template, $errors);
        }
        $map->importGeoJsonFeatures($json["features"], $template, (bool) $request->post("replace"));
        if (!$this->store->commit()) {
            $errors = [$this->view->message("fail", "error_save")];
            return $this->respondWithImportForm($map, $geojson, $template, $errors);
        }
        return Response::redirect($request->url()->without("action")->absolute());
    }

    private function mapToDto(Map $map): MapDto
    {
        $markers = [];
        foreach ($map->markers() as $marker) {
            $markers[] = [
                "latitude" => $marker->latitude(),
                "longitude" => $marker->longitude(),
                "info" => $marker->info(),
                "show" => $marker->showInfo(),
            ];
        }
        return new MapDto(
            $map->name(),
            $map->title(),
            $map->latitude(),
            $map->longitude(),
            $map->zoom(),
            $map->maxZoom(),
            $map->aspectRatio(),
            (string) json_encode($markers)
        );
    }

    /** @return iterable<object{latitude:float,longitude:float,info:string,show:string}> */
    private function markerDtos(Map $map): iterable
    {
        foreach ($map->markers() as $marker) {
            yield (object) [
                "latitude" => $marker->latitude(),
                "longitude" => $marker->longitude(),
                "info" => $marker->info() ?? "",
                "show" => $marker->showInfo() ? "checked" : "",
            ];
        }
    }

    private function dtoFromRequest(Request $request): MapDto
    {
        return new MapDto(
            $request->post("name") ?? $request->get("maps_map") ?? "",
            $request->post("title") ?? "",
            (float) ($request->post("latitude") ?? ""),
            (float) ($request->post("longitude") ?? ""),
            (int) ($request->post("zoom") ?? ""),
            (int) ($request->post("max_zoom") ?? ""),
            $request->post("aspect_ratio") ?? "",
            $request->post("markers") ?? ""
        );
    }

    private function updateMapFromDto(Map $map, MapDto $dto): void
    {
        $map->setTitle($dto->title);
        $map->setCoordinates((float) $dto->latitude, (float) $dto->longitude);
        $map->setZoom((int) $dto->zoom, (int) $dto->maxZoom);
        $map->setAspectRatio($dto->aspectRatio);
        $map->purgeMarkers();
        $markers = json_decode($dto->markers);
        if (is_array($markers)) {
            foreach ($markers as $marker) {
                $map->addMarker(
                    (float) $marker->latitude,
                    (float) $marker->longitude,
                    $marker->info,
                    (bool) $marker->show
                );
            }
        }
    }

    /** @param list<string> $errors */
    private function respondWithOverview(Request $request, array $errors = []): Response
    {
        $maps = $this->store->find('/[a-z0-9\-]+\.xml$/');
        return Response::create($this->view->render("maps_admin", [
            "errors" => $errors,
            "maps" => $this->mapDtos($request, $maps),
        ]))->withTitle("Maps – " . $this->view->text("menu_main"));
    }

    /**
     * @param iterable<object{latitude:float,longitude:float,info:string,show:string}> $markers
     * @param list<string> $errors
     */
    private function respondWithEditor(bool $new, MapDto $dto, iterable $markers, array $errors = []): Response
    {
        return Response::create($this->view->render("map_edit", [
            "errors" => $errors,
            "name_disabled" => $new ? "" : "disabled",
            "map" => $dto,
            "markers" => $markers,
            "token" => $this->csrfProtector->token(),
            "script" => $this->pluginFolder . "admin.js",
        ]))->withTitle("Maps – " . $this->view->text("label_edit"));
    }

    /** @param list<string> $errors */
    private function respondWithImportForm(Map $map, string $geojson, string $template, array $errors = []): Response
    {
        return Response::create($this->view->render("import", [
            "errors" => $errors,
            "name" => $map->name(),
            "geojson" => $geojson,
            "template" =>  $template,
            "token" => $this->csrfProtector->token(),
        ]))->withTitle("Maps – " . $this->view->text("label_import"));
    }
}
