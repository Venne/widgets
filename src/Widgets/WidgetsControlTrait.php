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

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
trait WidgetsControlTrait
{

	/** @var \Venne\Widgets\WidgetManager|null */
	private $widgetManager;

	/**
	 * @param \Venne\Widgets\WidgetManager $widgetManager
	 */
	public function injectWidgetManager(WidgetManager $widgetManager)
	{
		$this->widgetManager = $widgetManager;
	}

	/**
	 * @return \Venne\Widgets\WidgetManager|null
	 */
	public function getWidgetManager()
	{
		return $this->widgetManager;
	}

	/**
	 * @param string $name
	 * @return \Nette\Application\UI\Control
	 */
	protected function createComponent($name)
	{
		$control = parent::createComponent($name);

		if ($control === null && $this->widgetManager && $this->widgetManager->hasWidget($name)) {
			$control = $this->widgetManager->getWidget($name);
		}

		return $control;
	}

}
