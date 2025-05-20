<?php

use Maps\MapDto;
use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<string> $errors
 * @var string $name_disabled
 * @var MapDto $map
 * @var string $token
 */
?>

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
      <span class="maps_help"><?=$this->text("help_markers")?></span>
      <textarea name="markers"><?=$this->esc($map->markers)?></textarea>
    </label>
    <p class="maps_controls">
      <button name="maps_do"><?=$this->text("label_save")?></button>
    </p>
    <input type="hidden" name="maps_token" value="<?=$this->esc($token)?>">
  </form>
</article>
