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
use Nette\Object;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class WidgetManager extends Object
{

	/** @var Container */
	private $container;

	/** @var Callback[] */
	private $widgets = array();


	/**
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	/**
	 * @param $name
	 * @param $factory
	 * @return $this
	 * @throws \Nette\InvalidArgumentException
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
			throw new InvalidArgumentException("Service '$factory' does not exist");
		}

		$this->widgets[$name] = $factory;
		return $this;
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
	 * @return \Callback[]
	 */
	public function getWidgets()
	{
		return $this->widgets;
	}


	/**
	 * @param $name
	 * @return mixed
	 * @throws \Nette\InvalidStateException
	 * @throws \Nette\InvalidArgumentException
	 */
	public function getWidget($name)
	{
		if (!$this->hasWidget($name)) {
			throw new InvalidArgumentException("Widget $name does not exists");
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
			throw new InvalidStateException("Widget is not instance of 'Nette\Application\UI\Control'.");
		}

		return $widget;
	}

}

