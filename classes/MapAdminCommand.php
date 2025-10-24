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

use GdImage;
use Maps\Infra\Fetcher;
use Maps\Model\Map;
use Maps\Model\TileCalculator;
use Plib\CsrfProtector;
use Plib\DocumentStore2 as DocumentStore;
use Plib\JavaScript;
use Plib\Request;
use Plib\Response;
use Plib\View;

/**
 * @phpstan-import-type Tile from TileCalculator
 */
class MapAdminCommand
{
    private string $pluginFolder;
    /** @var array<string,string> */
    private array $config;
    private DocumentStore $store;
    private CsrfProtector $csrfProtector;
    private TileCalculator $tileCalculator;
    private Fetcher $fetcher;
    private JavaScript $javaScript;
    private View $view;

    /** @param array<string,string> $config */
    public function __construct(
        string $pluginFolder,
        array $config,
        DocumentStore $store,
        CsrfProtector $csrfProtector,
        TileCalculator $tileCalculator,
        Fetcher $fetcher,
        JavaScript $javaScript,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->config = $config;
        $this->store = $store;
        $this->csrfProtector = $csrfProtector;
        $this->tileCalculator = $tileCalculator;
        $this->fetcher = $fetcher;
        $this->javaScript = $javaScript;
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
            case "create_image":
                return $this->createImage($request);
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
        return $this->respondWithEditor(true, $dto);
    }

    private function doCreate(Request $request): Response
    {
        $map = Map::create($request->post("name") ?? "", $this->store);
        $dto = $this->dtoFromRequest($request);
        if (!$this->csrfProtector->check($request->post("maps_token"))) {
            $this->store->rollback();
            $errors = [$this->view->message("fail", "error_not_authorized")];
            return $this->respondWithEditor(true, $dto, $errors);
        }
        $this->updateMapFromDto($map, $dto);
        if (!$this->store->commit()) {
            $errors = [$this->view->message("fail", "error_save", $dto->name)];
            return $this->respondWithEditor(true, $dto, $errors);
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
        return $this->respondWithEditor(false, $dto);
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
            $errors = [$this->view->message("fail", "error_not_authorized")];
            return $this->respondWithEditor(true, $dto, $errors);
        }
        $this->updateMapFromDto($map, $dto);
        if (!$this->store->commit()) {
            $errors = [$this->view->message("fail", "error_save", $dto->name)];
            return $this->respondWithEditor(false, $dto, $errors);
        }
        return Response::redirect($request->url()->without("action")->absolute());
    }

    private function createImage(Request $request): Response
    {
        if ($request->post("maps_do") !== null) {
            return $this->doCreateImage($request);
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
        return $this->respondWithCreateImageForm($map, 500);
    }

    private function doCreateImage(Request $request): Response
    {
        if ($request->get("maps_map") === null) {
            return $this->respondWithOverview($request, [$this->view->message("fail", "error_no_map")]);
        }
        $map = Map::read($request->get("maps_map"), $this->store);
        if ($map === null) {
            return $this->respondWithOverview($request, [
                $this->view->message("fail", "error_load", $request->get("maps_map"))
            ]);
        }
        $width = (int) ($request->post("width") ?? "500");
        if ($width < 100 || $width > 1000) {
            $errors = [$this->view->message("fail", "error_invalid_width")];
            return $this->respondWithCreateImageForm($map, $width, $errors);
        }
        if (!$this->csrfProtector->check($request->post("maps_token"))) {
            $errors = [$this->view->message("fail", "error_not_authorized")];
            return $this->respondWithCreateImageForm($map, $width, $errors);
        }
        $height = (int) round($width * $map->aspectDenominator() / $map->aspectNumerator());
        $tiles = $this->tileCalculator->calculate($map->latitude(), $map->longitude(), $map->zoom(), $width, $height);
        if (($im = $this->createMapImage($width, $height, $tiles)) === null) {
            $errors = [$this->view->message("fail", "error_create_image")];
            return $this->respondWithCreateImageForm($map, $width, $errors);
        }
        if (!imagejpeg($im, $this->pluginFolder . "static/{$map->name()}.jpg")) {
            $errors = [$this->view->message("fail", "error_save_image")];
            return $this->respondWithCreateImageForm($map, $width, $errors);
        }
        return Response::redirect($request->url()->without("action")->absolute());
    }

    /**
     * @param list<Tile> $tiles
     * @return ?GdImage
     */
    private function createMapImage(int $width, int $height, $tiles)
    {
        $dst = imagecreatetruecolor($width, $height);
        foreach ($tiles as $tile) {
            $url = str_replace(
                ["{x}", "{y}", "{z}"],
                [$tile->tileX, $tile->tileY, $tile->zoom],
                $this->config["tile_url"]
            );
            if (($data = $this->fetcher->fetch($url)) === null) {
                return null;
            }
            if (($src = @imagecreatefromstring($data)) === false) {
                return null;
            }
            imagecopy($dst, $src, $tile->x, $tile->y, 0, 0, imagesx($src), imagesy($src));
        }
        return $dst;
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
        return Response::redirect($request->url()->with("action", "update")->absolute());
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
        return Response::create($this->view->render("admin", [
            "errors" => $errors,
            "maps" => $this->mapDtos($request, $maps),
        ]))->withTitle("Maps – " . $this->view->text("menu_main"));
    }

    /** @param list<string> $errors */
    private function respondWithEditor(bool $new, MapDto $dto, array $errors = []): Response
    {
        $this->javaScript->includePolyfills();
        $this->javaScript->include($this->pluginFolder . "js/admin");
        return Response::create($this->view->render("edit", [
            "errors" => $errors,
            "name_disabled" => $new ? "" : "disabled",
            "map" => $dto,
            "token" => $this->csrfProtector->token(),
        ]))->withTitle("Maps – " . $this->view->text("label_edit"));
    }

    /** @param list<string> $errors */
    private function respondWithCreateImageForm(Map $map, int $width, array $errors = []): Response
    {
        return Response::create($this->view->render("create_image", [
            "errors" => $errors,
            "name" => $map->name(),
            "width" => $width,
            "token" => $this->csrfProtector->token(),
        ]))->withTitle("Maps – " . $this->view->text("label_create_image"));
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
