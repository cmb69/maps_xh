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

use Plib\Response;
use Plib\View;

class MapCommand
{
    private string $pluginFolder;

    /** @var array<string,string> */
    private array $conf;

    private View $view;

    /** @param array<string,string> $conf */
    public function __construct(
        string $pluginFolder,
        array $conf,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->view = $view;
    }

    public function __invoke(): Response
    {
        return Response::create($this->view->render("map", [
            "script" => $this->pluginFolder . "maps.js",
            "conf" => $this->jsConf(),
        ]));
    }

    /** @return array<string,mixed> */
    private function jsConf(): array
    {
        return [
            "tileUrl" => $this->conf["tile_url"],
            "tileAttribution" => $this->view->plain("tile_attribution"),
        ];
    }
}
