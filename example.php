<?php
include "textprotector.php";

$html = 'This is a <a href="link.html" class="text">linked test</a>';
$length = 20;
var_dump(substr($html, 0, $length));

$tag = new TagProtector;
$html = $tag->protect($html);
var_dump($html);
$html = $tag->truncate($html, $length);
$html = $tag->unprotect($html);

var_dump($html);
