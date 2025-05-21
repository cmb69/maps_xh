# Maps_XH

The plugin facilitates displaying [OpenStreetMap](https://www.openstreetmap.org/)
maps on a CMSimple_XH website without needing to fiddle around with JavaScript.
Such maps are useful to show others where they can find you, but can be used
for other purposes as well.
The plugin provides data privacy by default; i.e. no data are transferred to
third-party servers prior to explicit agreement.
The maps can have an arbitrary amount of markers (optionally with info texts),
and there is basic support for importing GeoJSON features.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
  - [Define Maps](#define-maps)
  - [Importing GeoJSON](#importing-geojson)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Maps_XH is a plugin for [CMSimple_XH](https://cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0, and PHP ≥ 8.0.0.
Maps_XH also requires [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.9;
if that is not already installed (see *Settings*→*Info*),
get the [lastest release](https://github.com/cmb69/plib_xh/releases/latest),
and install it.

## Download

The [lastest release](https://github.com/cmb69/maps_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins.

1.  Backup the data on your server.
1.  Unzip the distribution on your computer.
1.  Upload the whole folder `maps/` to your server into
    the `plugins/` folder of CMSimple_XH.
1.  Set write permissions to the subfolders `config/`, `css/`, and
    `languages/`.
1.  Check under `Plugins` → `Maps` in the back-end of the website,
    if all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other
CMSimple_XH plugins in the back-end of the Website. Select
`Plugins` → `Maps`.

You can change the default settings of Maps_XH under
`Config`. Hints for the options will be displayed when hovering
over the help icons with your mouse.

Localization is done under `Language`. You can translate the
character strings to your own language if there is no appropriate
language file available, or customize them according to your
needs.

The look of Maps_XH can be customized under `Stylesheet`.

## Usage

To display a map on a page:

    {{{maps('name')}}}

To display a map in the template:

    <?=maps('name')?>

Where `name` is the name of the map.  See [Define Maps](#define-maps) for details.

If you switch to view mode, and you have tile privacy enabled
(`Plugins` → `Maps` → `Config` → `Tile` → `Privacy`) what is recommended, you
will not see the actual map, but rather a grey area with the markers (if any
are defined).  Below you find a form where you have to agree to the data
transmission first, before the map will be fully shown.  This also happens
to visitors of your website.

**Please heed the terms of use of the tile providers.**
The plugin does its best to comply, but finally it is up to you.
For the default tile provider, see <https://operations.osmfoundation.org/policies/tiles/>.

### Define Maps

In the plugin back-end (`Plugins` → `Map` → `Administration`) you can define
your map(s).  The user interface is supposed to be self explaining, but some
notes are in order:

* Every map has a unique name under which it is stored in the `content/` folder.
  If you want to change the name, you need to rename the respective file via FTP.

* The coordinates (latitude/longitude) determine the center of the map, and
  are given as decimal numbers (not degrees and minutes).  Search the Web to
  find the coordinates for the desired location.

* Zoom level 0 means the whole world, level 20 is roughly a building.
  [Definition of the zoom levels](https://wiki.openstreetmap.org/wiki/Zoom_levels).

* The aspect ratio determines the height of the map when displayed; its width
  is always 100%.

* You can define an arbitrary amount of markers which are placed at the given
  coordinates.  These markers can have info text which is shown when the marker
  is clicked, or, if you check the checkbox, is shown when the map is displayed.

### Importing GeoJSON

There is support for importing so-called [GeoJSON](https://geojson.org/) features;
only points are recognized, and these are imported as markers.  The import form
has a field for the the `GeoJSON` (use copy&paste to fill it), and a `Template` field
which can be used to fill the marker info with data from the GeoJSON features.
The template expects HTML with placeholders which will be replaced with so called
properties of the GeoJSON feature.  If the property does not exists, no
replacement is done.  A placeholders is the name of a property enclosed
in curly braces, e.g. `{name}`.
Note that you can choose to replace the existing markers, what is useful when
reimporting updated GeoJSON.

## Troubleshooting

Report bugs and ask for support either on
[Github](https://github.com/cmb69/maps_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Maps_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Maps_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Maps_XH.  If not, see <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

## Credits

Maps_XH was inspired by *hufnala*.

The plugin is powered by [OpenStreetMap](https://www.openstreetmap.org/)
and [Leaflet](https://leafletjs.com/).
Many thanks for providing these awesome tools and services to the community!

The plugin icon is designed by [Freepik - Flaticon](https://www.flaticon.com/free-icons/street-map).
Many thanks for making this icon available for free.

Many thanks to the community at the
[CMSimple_XH Forum](https://www.cmsimpleforum.com/) for tips, suggestions
and testing.

And last but not least many thanks to [Peter Harteg](httsp://www.harteg.dk),
the “father” of CMSimple,
and all developers of [CMSimple_XH](https://www.cmsimple-xh.org)
without whom this amazing CMS would not exist.
