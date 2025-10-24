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

namespace Maps\Infra;

class Fetcher
{
    private const USER_AGENT = "Maps_XH/%s (+https://github.com/cmb69/maps_xh)";

    private const SEVEN_DAYS = 7 * 24 * 60 * 60;

    private string $version;

    private string $cacheFolder;

    public function __construct(string $version, string $cacheFolder)
    {
        $this->version = $version;
        $this->cacheFolder = $cacheFolder;
    }

    public function fetch(string $url): ?string
    {
        if (($data = $this->fetchFromCache($url)) !== null) {
            return $data;
        }
        if (extension_loaded("curl")) {
            if (($data = $this->fetchWithCurl($url)) === null) {
                return null;
            }
            $this->cacheData($url, $data);
            return $data;
        }
        if (($data = $this->fetchNative($url)) === null) {
            return null;
        }
        $this->cacheData($url, $data);
        return $data;
    }

    private function fetchFromCache(string $url): ?string
    {
        $cacheFile = $this->cacheFile($url);
        if (!is_readable($cacheFile) || filemtime($cacheFile) < time() - self::SEVEN_DAYS) {
            return null;
        }
        if (($data = file_get_contents($cacheFile)) === false) {
            return null;
        }
        return $data;
    }

    private function cacheData(string $url, string $data): void
    {
        $cacheFile = $this->cacheFile($url);
        file_put_contents($cacheFile, $data);
    }

    private function cacheFile(string $url): string
    {
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        return $this->cacheFolder . sha1($url) . "." . $ext;
    }

    private function fetchWithCurl(string $url): ?string
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        assert($data !== true); // due to CURLOPT_RETURNTRANSFER
        return $data !== false ? $data : null;
    }

    private function fetchNative(string $url): ?string
    {
        $context = stream_context_create([
            "http" => [
                "header" => "User-Agent: {$this->userAgent()}"
            ],
        ]);
        $data = file_get_contents($url, false, $context);
        return $data !== false ? $data : null;
    }

    private function userAgent(): string
    {
        return sprintf(self::USER_AGENT, $this->version);
    }
}
