php-textprotector
=================

The class is designed to protect all sorts oft text while executing something on it. It is simple to use. Let's say you have some HTML then you would want to use the `TagProtector`

	$html = 'This is a <a href="link.html" class="text">linked test</a>';
	$tag = new TagProtector;
	$html = $tag->protect($html);
	$html = $tag->truncate($html, 20);
	$html = $tag->unprotect($html);

	// $html now is: This is a <a href="link.html" class="text">linked tes
	// instead of: This is a <a href="l


