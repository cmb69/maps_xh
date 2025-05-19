<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $script
 * @var array<string,mixed> $conf
 * @var bool $privacy
 */
?>

<script type="module" src="<?=$this->esc($script)?>"></script>
<div id="map" data-maps-conf='<?=$this->json($conf)?>'></div>
<?if ($privacy):?>
<form method="post">
  <p><?=$this->text("message_tile_privacy")?></p>
  <button name="maps_agree" value="1"><?=$this->text("label_agree")?></button>
</form>
<?endif?>
