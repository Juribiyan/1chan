<?php

/**
 * This file is part of the Texy! (http://texy.info)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */


/**
 * Heading module.
 */
final class TexyHeadingModule extends TexyModule
{
	const
		DYNAMIC = 1, // auto-leveling
		FIXED = 2; // fixed-leveling

	/** @var string  textual content of first heading */
	public $title;

	/** @var array  generated Table of Contents */
	public $TOC;

	/** @var bool  autogenerate ID */
	public $generateID = FALSE;

	/** @var string  prefix for autogenerated ID */
	public $idPrefix = 'toc-';

	/** @var int  level of top heading, 1..6 */
	public $top = 1;

	/** @var bool  surrounded headings: more #### means higher heading */
	public $moreMeansHigher = TRUE;

	/** @var int  balancing mode */
	public $balancing = self::DYNAMIC;

	/** @var array  when $balancing = TexyHeadingModule::FIXED */
	public $levels = array(
		'#' => 0, // # --> $levels['#'] + $top = 0 + 1 = 1 --> <h1> ... </h1>
		'*' => 1,
		'=' => 2,
		'-' => 3,
	);

	/** @var array  used ID's */
	private $usedID;


	public function __construct($texy)
	{
		$this->texy = $texy;

		$texy->addHandler('heading', array($this, 'solve'));
		$texy->addHandler('beforeParse', array($this, 'beforeParse'));
		$texy->addHandler('afterParse', array($this, 'afterParse'));

		$texy->registerBlockPattern(
			array($this, 'patternUnderline'),
			'#^(\S.{0,1000})'.TexyPatterns::MODIFIER_H.'?\n'
			. '(\#{3,}+|\*{3,}+|={3,}+|-{3,}+)$#mU',
			'heading/underlined'
		);

		$texy->registerBlockPattern(
			array($this, 'patternSurround'),
			'#^(\#{2,}+|={2,}+)(.+)'.TexyPatterns::MODIFIER_H.'?()$#mU',
			'heading/surrounded'
		);
	}


	public function beforeParse()
	{
		$this->title = NULL;
		$this->usedID = array();
		$this->TOC = array();
	}


	/**
	 * @param  Texy
	 * @param  TexyHtml
	 * @param  bool
	 * @return void
	 */
	public function afterParse($texy, $DOM, $isSingleLine)
	{
		if ($isSingleLine) {
			return;
		}

		if ($this->balancing === self::DYNAMIC) {
			$top = $this->top;
			$map = array();
			$min = 100;
			foreach ($this->TOC as $item) {
				$level = $item['level'];
				if ($item['type'] === 'surrounded') {
					$min = min($level, $min);
					$top = $this->top - $min;

				} elseif ($item['type'] === 'underlined') {
					$map[$level] = $level;
				}
			}

			asort($map);
			$map = array_flip(array_values($map));
		}

		foreach ($this->TOC as $key => $item) {
			if ($this->balancing === self::DYNAMIC) {
				if ($item['type'] === 'surrounded') {
					$level = $item['level'] + $top;

				} elseif ($item['type'] === 'underlined') {
					$level = $map[$item['level']] + $this->top;

				} else {
					$level = $item['level'];
				}

				$item['el']->setName('h' . min(6, max(1, $level)));
				$this->TOC[$key]['level'] = $level;
			}

			if ($this->generateID) {
				if (!empty($item['el']->style['toc']) && is_array($item['el']->style)) {
					$title = $item['el']->style['toc'];
					unset($item['el']->style['toc']);
				} else {
					$title = trim($item['el']->toText($this->texy));
				}
				$this->TOC[$key]['title'] = $title;
				if (empty($item['el']->attrs['id'])) {
					$id = $this->idPrefix . Texy::webalize($title);
					$counter = '';
					if (isset($this->usedID[$id . $counter])) {
						$counter = 2;
						while (isset($this->usedID[$id . '-' . $counter])) {
							$counter++;
						}
						$id .= '-' . $counter;
					}
					$this->usedID[$id] = TRUE;
					$item['el']->attrs['id'] = $id;
				}
			}
		}

		// document title
		if ($this->title === NULL && count($this->TOC)) {
			$item = reset($this->TOC);
			$this->title = isset($item['title']) ? $item['title'] : trim($item['el']->toText($this->texy));
		}
	}


	/**
	 * Callback for underlined heading.
	 *
	 * Heading .(title)[class]{style}>
	 * -------------------------------
	 *
	 * @param  TexyBlockParser
	 * @param  array      regexp matches
	 * @param  string     pattern name
	 * @return TexyHtml|string|FALSE
	 */
	public function patternUnderline($parser, $matches)
	{
		list(, $mContent, $mMod, $mLine) = $matches;
		// $matches:
		// [1] => ...
		// [2] => .(title)[class]{style}<>
		// [3] => ...

		$mod = new TexyModifier($mMod);
		$level = $this->levels[$mLine[0]];
		return $this->texy->invokeAroundHandlers('heading', $parser, array($level, $mContent, $mod, FALSE));
	}


	/**
	 * Callback for surrounded heading.
	 *
	 * ### Heading .(title)[class]{style}>
	 *
	 * @param  TexyBlockParser
	 * @param  array      regexp matches
	 * @param  string     pattern name
	 * @return TexyHtml|string|FALSE
	 */
	public function patternSurround($parser, $matches)
	{
		list(, $mLine, $mContent, $mMod) = $matches;
		// [1] => ###
		// [2] => ...
		// [3] => .(title)[class]{style}<>

		$mod = new TexyModifier($mMod);
		$level = min(7, max(2, strlen($mLine)));
		$level = $this->moreMeansHigher ? 7 - $level : $level - 2;
		$mContent = rtrim($mContent, $mLine[0] . ' ');
		return $this->texy->invokeAroundHandlers('heading', $parser, array($level, $mContent, $mod, TRUE));
	}


	/**
	 * Finish invocation.
	 *
	 * @param  TexyHandlerInvocation  handler invocation
	 * @param  int  0..5
	 * @param  string
	 * @param  TexyModifier
	 * @param  bool
	 * @return TexyHtml
	 */
	public function solve($invocation, $level, $content, $mod, $isSurrounded)
	{
		// as fixed balancing, for block/texysource & correct decorating
		$el = TexyHtml::el('h' . min(6, max(1, $level + $this->top)));
		$mod->decorate($this->texy, $el);

		$el->parseLine($this->texy, trim($content));

		$this->TOC[] = array(
			'el' => $el,
			'level' => $level,
			'type' => $isSurrounded ? 'surrounded' : 'underlined',
		);

		return $el;
	}

}
