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

use Plib\CsrfProtector;
use Plib\DocumentStore2 as DocumentStore;
use Plib\SystemChecker;
use Plib\View;

class Dic
{
    public const VERSION = "1.0-dev";

    public static function mainCommand(): MainCommand
    {
        global $pth, $plugin_cf;
        return new MainCommand(
            $pth["folder"]["plugins"] . "maps/",
            $plugin_cf["maps"],
            self::view()
        );
    }

    public static function mapCommand(): MapCommand
    {
        global $pth, $plugin_cf;
        return new MapCommand(
            $pth["folder"]["plugins"] . "maps/",
            $plugin_cf["maps"],
            new DocumentStore(self::contentFolder()),
            self::view()
        );
    }

    public static function infoCommand(): InfoCommand
    {
        global $pth;
        return new InfoCommand(
            $pth["folder"]["plugins"] . "maps/",
            new SystemChecker(),
            self::view()
        );
    }

    public static function mapAdminCommand(): MapAdminCommand
    {
        global $pth;
        return new MapAdminCommand(
            $pth["folder"]["plugins"] . "maps/",
            new DocumentStore(self::contentFolder()),
            new CsrfProtector(),
            self::view()
        );
    }

    private static function contentFolder(): string
    {
        global $pth;
        return $pth["folder"]["content"] . "maps/";
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;
        return new View($pth["folder"]["plugins"] . "maps/views/", $plugin_tx["maps"]);
    }
}
