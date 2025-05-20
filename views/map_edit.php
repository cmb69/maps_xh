<?php

use Maps\MapDto;
use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<string> $errors
 * @var string $name_disabled
 * @var MapDto $map
 * @var iterable<object{latitude:float,longitude:float,info:string,show:string}> $markers
 * @var string $token
 * @var string $script
 */
?>

<script type="module" src="<?=$this->esc($script)?>"></script>
<article class="map_edit">
  <h1>Maps – <?=$this->text("menu_main")?></h1>
<?foreach ($errors as $error):?>
  <?=$this->raw($error)?>
<?endforeach?>
  <form method="post">
    <label>
      <span><?=$this->text("label_name")?></span>
      <span class="maps_help"><?=$this->text("help_name")?></span>
      <input name="name" value="<?=$this->esc($map->name)?>" <?=$this->esc($name_disabled)?> required pattern="[a-z0-9\-]+">
    </label>
    <label>
      <span><?=$this->text("label_title")?></span>
      <input name="title" value="<?=$this->esc($map->title)?>" required>
    </label>
    <label>
      <span><?=$this->text("label_latitude")?></span>
      <span class="maps_help"><?=$this->text("help_latitude")?></span>
      <input type="number" name="latitude" value="<?=$this->esc($map->latitude)?>" min="-90" max="90" step="any">
    </label>
    <label>
      <span><?=$this->text("label_longitude")?></span>
      <span class="maps_help"><?=$this->text("help_longitude")?></span>
      <input type="number" name="longitude" value="<?=$this->esc($map->longitude)?>" min="-180" max="180" step="any">
    <label>
      <span><?=$this->text("label_zoom")?></span>
      <span class="maps_help"><?=$this->text("help_zoom")?></span>
      <input type="number" name="zoom" value="<?=$this->esc($map->zoom)?>" min="0" max="20">
    </label>
    <label>
      <span><?=$this->text("label_max_zoom")?></span>
      <span class="maps_help"><?=$this->text("help_zoom")?></span>
      <input type="number" name="max_zoom" value="<?=$this->esc($map->maxZoom)?>" min="0" max="20">
    </label>
    <label>
      <span><?=$this->text("label_aspect_ratio")?></span>
      <span class="maps_help"><?=$this->text("help_aspect_ratio")?></span>
      <input name="aspect_ratio" value="<?=$this->esc($map->aspectRatio)?>" required pattern="\d+/\d+">
    </label>
    <label>
      <span><?=$this->text("label_markers")?></span>
      <textarea name="markers"><?=$this->esc($map->markers)?></textarea>
    </label>
    <table style="display: none">
      <caption><?=$this->text("label_markers")?></caption>
      <colgroup>
        <col>
        <col>
        <col>
        <col>
        <col>
      </colgroup>
      <thead>
        <tr>
          <th><?=$this->text("label_latitude")?> <span class="maps_help"><?=$this->text("help_latitude")?></span></th>
          <th><?=$this->text("label_longitude")?> <span class="maps_help"><?=$this->text("help_longitude")?></span></th>
          <th><?=$this->text("label_info")?> <span class="maps_help"><?=$this->text("help_info")?></span></th>
          <th></th>
          <th><button class="maps_add_row" type="button"><svg xmlns="http://www.w3.org/2000/svg" role="img" viewBox="0 0 384 512" width="1em" height="1em" fill="currentColor"><title><?=$this->text("label_create_marker")?></title><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg></button></th>
        </tr>
      </thead>
      <tbody>
<?foreach ($markers as $marker):?>
        <tr>
          <td><input type="number" value="<?=$this->esc($marker->latitude)?>" min="-90" max="90" step="any"></td>
          <td><input type="number" value="<?=$this->esc($marker->longitude)?>" min="-180" max="180" step="any"></td>
          <td><textarea><?=$this->esc($marker->info)?></textarea></td>
          <td><input type="checkbox" <?=$this->esc($marker->show)?>></td>
          <td></td>
        </tr>
<?endforeach?>
      </tbody>
      <tfoot style="display: none">
        <tr>
          <td><input type="number" value="0" min="-90" max="90" step="any"></td>
          <td><input type="number" value="0" min="-180" max="180" step="any"></td>
          <td><textarea></textarea></td>
          <td><input type="checkbox"></td>
          <td><button type="button" class="maps_delete_row"><svg xmlns="http://www.w3.org/2000/svg" role="img" viewBox="0 0 448 512" width="1em" height="1em" fill="currentColor"><title><?=$this->text("label_delete_marker")?></title><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg></button></td>
        </tr>
      </tfoot>
    </table>
    <p class="maps_controls">
      <button name="maps_do"><?=$this->text("label_save")?></button>
    </p>
    <input type="hidden" name="maps_token" value="<?=$this->esc($token)?>">
  </form>
</article>
