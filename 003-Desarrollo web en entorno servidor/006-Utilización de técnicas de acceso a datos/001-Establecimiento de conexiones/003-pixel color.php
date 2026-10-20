<?php

  $image = imagecreatefromjpeg("andresruiz.jpeg");
  $color = imagecolorat($image, 0, 0);
  imagedestroy($image);
  echo $color;

?>