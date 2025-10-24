<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var array<string,mixed> $conf
 * @var string $title
 * @var string $aspectRatio
 * @var string $map_image
 * @var bool $privacy
 */
?>

<figure class="maps_map" data-maps-conf='<?=$this->json($conf)?>'>
  <figcaption><?=$this->raw($title)?></figcaption>
  <div class="maps_static">
    <img src="<?=$this->esc($map_image)?>" alt="" style="width: 100%; aspect-ratio: <?=$this->esc($aspectRatio)?>">
    <span><?=$this->plain("tile_attribution")?></span>
  </div>
  <script type="text/x-template">
    <div class="maps_map" style="width: 100%; aspect-ratio: <?=$this->esc($aspectRatio)?>; display: none" data-aspect-ratio="<?=$this->esc($aspectRatio)?>"></div>
<?if ($privacy):?>
    <form method="post">
      <p><?=$this->text("message_tile_privacy")?></p>
      <p class="maps_controls">
        <button name="maps_agree" value="1"><?=$this->text("label_agree")?></button>
      </p>
    </form>
<?endif?>
  </script>
</figure>
