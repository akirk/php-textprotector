<?php

class TextProtector {
	protected $regex = false;
	protected $replacement = 'PRTCT';
	protected $current;

	public function __construct($regex, $namespace) {
		$this->regex = $regex;
		$this->replacement .= ":" . $namespace;
	}

	public function getCurrent() {
		return $this->current;
	}

	protected function replaceCallback($match) {
		$i = count($this->current);
		$this->current[] = $match[0];
		return '| {{{' . $this->replacement . $i . '}}} |';
	}

	public function protect($text) {
		$this->current = array();
		$text = preg_replace_callback($this->regex, array($this, "replaceCallback"), $text);
		return $text;
	}

	public function unprotect($text) {
		$text = $this->insertBack($this->replacement, $this->current, $text);
		return $this->removePartial($text);
	}

	protected function removePartial($text,$x = false) {
		if (($p = strrpos($text, "| {{{")) !== false) {
			if (strpos($text, '}}} |', $p) === false) {
				return substr($text, 0, $p);
			}
		}

		$s = '| {{{ ';
		while ($s = substr($s, 0, -1)) {
			if ($s == substr($text, -strlen($s))) {
				return substr($text, 0, -strlen($s));
			}
		}

		return $text;
	}

	protected function strlen($text) {
		$text = $this->removePartial($text);

		$length = $l = mb_strlen($text, "UTF-8");

		$p = $q = 0;
		while (($p = strpos($text, '| {{{' . $this->replacement, $p)) !== false) {
			$q = strpos($text, '}}} |', $p);
			if ($q === false) {
				$length	-= $l - $p;
				break;
			}
			$q += 5;

			$length -= $q - $p;
			$p = $q;
		}

		return $length;
	}

	public function truncate($originalText, $length) {
		$l1 = $l2 = $length;
		$c = 0;
		do {
			$l1 += $length > $l2 ? strlen($this->replacement) + 10 : $length - $l2;
			$text = mb_substr($originalText, 0, $l1, "UTF-8");
			if ($c++ > 100) break;
			if ($text == $originalText) break;
		} while ($length != ($l2 = $this->strlen($text)));

		return $this->removePartial($text, true);
	}

	protected function insertBack($original_replacement, $replace, $subject) {
		$pos = 0;
		for ($i = 0, $count = count($replace); $i < $count; $i++) {
			$replacement = '| {{{' . $original_replacement . $i . '}}} |';
			$len = strlen($replacement);
			if (($pos = strpos($subject, $replacement, $pos)) === false) continue;
			$subject = substr_replace($subject, $replace[$i], $pos, $len);
		}

		return $subject;
	}

}

class TagProtector extends TextProtector {
	public function __construct() {
		parent::__construct("!<.*?>!s", "TAG");
	}
}

class HtmlPreProtector extends TextProtector {
	public function __construct() {
		parent::__construct("!<pre.*?</pre>!s", "TAG");
	}
}

class WholeLinkProtector extends TextProtector {
	public function __construct() {
		parent::__construct("!<a[^>]+>.*?</a>!is", "LINK");
	}
}
