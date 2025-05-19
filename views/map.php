<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $script
 * @var array<string,mixed> $conf
 */
?>

<div id="map" data-maps-conf='<?=$this->json($conf)?>'></div>
<script type="module" src="<?=$this->esc($script)?>"></script>
