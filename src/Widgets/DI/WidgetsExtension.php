<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Widgets\DI;

use Nette\DI\CompilerExtension;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class WidgetsExtension extends CompilerExtension
{

	const WIDGET_TAG = 'venne.widget';


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$container->addDefinition($this->prefix('widgetManager'))
			->setClass('Venne\Widgets\WidgetManager');
	}


	public function beforeCompile()
	{
		$container = $this->getContainerBuilder();
		$config = $container->getDefinition($this->prefix('widgetManager'));

		foreach ($container->findByTag(static::WIDGET_TAG) as $factory => $meta) {
			if (!is_string($meta)) {
				throw new \Nette\InvalidArgumentException("Tag " . static::WIDGET_TAG . " require name. Provide it in configuration. (tags: [venne.widget: name])");
			}
			$config->addSetup('addWidget', array($meta, $factory));
		}
	}

}
