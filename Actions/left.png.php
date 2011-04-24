<?php
Header('Content-type: image/png');
$image = getCachedImage('foldmap-left', 604800);
if (!empty($image)) exit($image);
$maps = createFoldmaps();
imagePNG($maps->left, null, 9);
