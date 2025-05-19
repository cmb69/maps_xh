<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $leaflet_base
 * @var string $js_integrity
 * @var string $css_integrity
 */
?>

<link rel="stylesheet" href="<?=$this->esc($leaflet_base)?>leaflet.css" <?=$this->raw($css_integrity)?> crossorigin="">
<script src="<?=$this->esc($leaflet_base)?>leaflet.js" <?=$this->raw($js_integrity)?> crossorigin=""></script>
