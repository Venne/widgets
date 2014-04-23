<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Widgets;

use Nette\InvalidArgumentException;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
trait WidgetsControlTrait
{

	/** @var WidgetManager|NULL */
	private $widgetManager;


	/**
	 * @param WidgetManager $widgetManager
	 */
	public function injectWidgetManager(WidgetManager $widgetManager)
	{
		$this->widgetManager = $widgetManager;
	}


	/**
	 * @return WidgetManager|NULL
	 */
	public function getWidgetManager()
	{
		return $this->widgetManager;
	}


	/**
	 * @param $name
	 * @return \Nette\Application\UI\Control
	 */
	protected function createComponent($name)
	{
		if ($control = parent::createComponent($name)) {
			return $control;
		}

		if ($this->widgetManager && $this->widgetManager->hasWidget($name)) {
			return $this->widgetManager->getWidget($name);
		}
	}

}
