<?php
Header('Content-type: image/png');
$image = getCachedImage('foldmap-center', 604800);
if (!empty($image)) exit($image);
$maps = createFoldmaps();
imagePNG($maps->center, null, 9);
