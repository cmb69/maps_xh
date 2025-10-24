<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<string> $errors
 * @var string $name
 * @var int $width
 * @var string $token
 */
?>

<article class="maps_create_image">
  <h1>Maps – <?=$this->text("label_create_image")?></h1>
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
        <span><?=$this->text("label_width")?></span>
        <span class="maps_help"><?=$this->text("help_width")?></span>
        <input type="number" name="width" value="<?=$this->esc($width)?>" min="100" max="1000" step="50" requrired></input>
      </label>
    </p>
    <p class="maps_controls">
      <button name="maps_do"><?=$this->text("label_create_image")?></button>
    </p>
    <input type="hidden" name="maps_token" value="<?=$this->esc($token)?>">
  </form>
</article>
