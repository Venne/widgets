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
use Nette\InvalidArgumentException;
use Venne\Widgets\WidgetManagerFactory;
use Venne\Widgets\WidgetManager;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class WidgetsExtension extends CompilerExtension
{

	const TAG_WIDGET = 'venne.widget';

	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$container->addDefinition($this->prefix('widgetManager'))
			->setClass(WidgetManager::class);

		$container->addDefinition($this->prefix('widgetManagerFactory'))
			->setImplement(WidgetManagerFactory::class);
	}

	public function beforeCompile()
	{
		$container = $this->getContainerBuilder();
		$config = $container->getDefinition($this->prefix('widgetManager'));

		foreach ($container->findByTag(static::TAG_WIDGET) as $factory => $meta) {
			if (!is_string($meta)) {
				throw new InvalidArgumentException(sprintf('Tag %s require name. Provide it in configuration. (tags: [venne.widget: name])', static::TAG_WIDGET));
			}
			$config->addSetup('addWidget', array($meta, $factory));
		}
	}

}
