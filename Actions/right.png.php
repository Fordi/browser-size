<?php
Header('Content-type: image/png');
$image = getCachedImage('foldmap-right', 604800);
if (!empty($image)) exit($image);
$maps = createFoldmaps();
imagePNG($maps->right, null, 9);
