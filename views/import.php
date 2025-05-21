<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<string> $errors
 * @var string $name
 * @var string $token
 */
?>

<article class="maps_import">
  <h1>Maps – <?=$this->text("label_import")?></h1>
<?foreach ($errors as $error):?>
  <?=$this->raw($error)?>
<?endforeach?>
  <form method="post">
    <p>
      <label>
        <span><?=$this->text("label_name")?></span>
        <input name="name" value="<?=$this->esc($name)?>" disabled>
      </label>
    </p>
    <p>
      <label>
        <span><?=$this->text("label_geojson")?></span>
        <textarea name="geojson" requrired></textarea>
      </label>
    </p>
    <p>
      <label>
        <span><?=$this->text("label_template")?></span>
        <span class="maps_help"><?=$this->text("help_template")?></span>
        <textarea name="template"></textarea>
      </label>
    </p>
    <p>
      <label>
        <input type="checkbox" name="replace">
        <span><?=$this->text("label_replace_markers")?></span>
      </label>
    </p>
    <p class="maps_controls">
      <button name="maps_do"><?=$this->text("label_import")?></button>
    </p>
    <input type="hidden" name="maps_token" value="<?=$this->esc($token)?>">
  </form>
</article>
