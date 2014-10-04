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

use Nette\Application\UI\Control;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class WidgetManager extends \Nette\Object
{

	/** @var \Nette\DI\Container */
	private $container;

	/** @var string[] */
	private $widgets = array();

	/**
	 * @param \Nette\DI\Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @param string $name
	 * @param string $factory
	 */
	public function addWidget($name, $factory)
	{
		if (!is_string($name)) {
			throw new InvalidArgumentException('Name of widget must be string');
		}

		if (!is_string($factory) && !method_exists($factory, 'create') && !is_callable($factory)) {
			throw new InvalidArgumentException('Second argument must be string or factory or callable');
		}

		if (is_string($factory) && !$this->container->hasService($factory)) {
			throw new InvalidArgumentException(sprintf('Service \'%s\' does not exist', $factory));
		}

		$this->widgets[$name] = $factory;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasWidget($name)
	{
		return isset($this->widgets[$name]);
	}

	/**
	 * @return string[]
	 */
	public function getWidgetNames()
	{
		return array_keys($this->widgets);
	}

	/**
	 * @param string $name
	 * @return \Nette\Application\UI\Control
	 */
	public function getWidget($name)
	{
		if (!$this->hasWidget($name)) {
			throw new InvalidArgumentException(sprintf('Widget \'%s\' does not exists', $name));
		}

		$factory = $this->widgets[$name];

		if (is_callable($factory)) {
			$widget = $factory();
		} else {
			if (is_string($factory)) {
				$factory = $this->container->getService($factory);

				if (!method_exists($factory, 'create')) {
					throw new InvalidStateException('Service is not factory.');
				}
			}

			$widget = $factory->create();
		}

		if (!$widget instanceof Control) {
			throw new InvalidStateException('Widget is not instance of \'Nette\Application\UI\Control\'.');
		}

		return $widget;
	}

}
