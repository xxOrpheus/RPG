<?php
namespace David;

class ezBBC {
	protected $bbc = null;
	public function __construct(array $bbc = null) {
		$this->bbc = array(
			'/\[b\](.*?)\[\/b\]/i' => function($matches) {
				return '<span style="font-weight: bold">' . ($matches[1]) . '</span>';
			},
			'/\[i\](.*?)\[\/i\]/i' => function($matches) {
				return '<span style="font-style: italic">' . ($matches[1]) . '</span>';
			},
			'/\[u\](.*?)\[\/u\]/i' => function($matches) {
				return '<span style="text-decoration: underline">' . ($matches[1]) . '</span>';
			},
			'/\[url="(.*?)"\](.*?)\[\/url\]/i' => function($matches) {
				if(!filter_var($matches[1], FILTER_VALIDATE_URL)) {
					return ($matches[2]);
				}
				return '<a target="_blank" href="' . $matches[1] . '">' . ($matches[2]) . '</a>';
			},
			'/\[color="#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})"\](.*?)\[\/color\]/i' => function($matches) {
				return '<span style="color:#' . $matches[1] . ';">' . $matches[2] . '</span>';
			},
			'/\[s\](.*?)\[\/s\]/i' => function($match) {
				return '<span style="text-decoration: line-through;">' . $match[1] . '</span>';
			}
		);
		if($bbc != null && count($bbc) > 0) {
			foreach($bbc as $i => $coder) {
				if(!is_a($coder, 'closure')) {
					unset($bbc[$i]);
				}
			}
			$this->bbc = array_merge($this->bbc, $bbc);
		}
	}

	public function set($match, closure $coder) {
		$this->bbc[$match] = $coder;
	}

	public function bbcize($text) {
		$text = htmlentities($text, ENT_NOQUOTES);
		foreach($this->bbc as $find => $bbcoder) {
			$text = preg_replace_callback($find, $bbcoder, $text);
		}
		return $text;
	}
}