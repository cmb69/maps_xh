<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<object{name:string,checked:string}> $maps
 */
?>

<article class="maps_admin">
  <h1>Maps – <?=$this->text("menu_main")?></h1>
  <form method="get">
    <input type="hidden" name="selected" value="maps">
    <input type="hidden" name="admin" value="plugin_main">
    <ul>
<?foreach ($maps as $map):?>
      <li>
        <label>
          <input type="radio" name="maps_map" value="<?=$this->esc($map->name)?>" <?=$this->esc($map->checked)?>>
          <span><?=$this->esc($map->name)?></span>
        </label>
      </li>
<?endforeach?>
    </ul>
    <p class="maps_controls">
      <button name="action" value="create"><?=$this->text("label_new")?></button>
      <button name="action" value="update"><?=$this->text("label_edit")?></button>
    </p>
  </form>
</article>
