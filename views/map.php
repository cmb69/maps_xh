<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $script
 */
?>

<div id="map"></div>
<script type="module" src="<?=$this->esc($script)?>"></script>
