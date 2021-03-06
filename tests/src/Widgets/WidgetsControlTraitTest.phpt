<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace VenneTests\Widgets;

use Nette;
use Tester\Assert;
use Tester\TestCase;
use Venne\Widgets\WidgetManager;
use Venne\Widgets\WidgetsControlTrait;

require __DIR__ . '/../bootstrap.php';

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class WidgetsControlTraitTest extends TestCase
{

	/** @var \Nette\Application\UI\Control */
	private $control;

	public function setUp()
	{
		$container = new Container;
		$container->addService('myService', new MyControlFactory);
		$container->meta[$container::TAGS]['venne.widget']['myService'] = 'foo';
		$widgetManager = new WidgetManager($container);
		$widgetManager->addWidget('foo', 'myService');
		$widgetManager->addWidget('bar', 'myService');

		$this->control = new Control;
		$this->control->injectWidgetManager($widgetManager);

	}

	public function testCreateComponent()
	{
		Assert::type(MyControl::class, $this->control->createComponent('foo'));
		Assert::type(BarControl::class, $this->control->createComponent('bar'));
		Assert::type(BarControl::class, $this->control->createComponent('test'));
		Assert::same(null, $this->control->createComponent('error'));
	}

	public function testGetWidgetManager()
	{
		Assert::type(WidgetManager::class, $this->control->getWidgetManager());
	}

}

class MyControl extends Nette\Application\UI\Control
{

}

class BarControl extends Nette\Application\UI\Control
{

}

class MyControlFactory
{

	public function create()
	{
		return new MyControl;
	}
}

class Control extends Nette\Application\UI\Control
{

	use WidgetsControlTrait {
		createComponent as cc;
	}

	public function createComponent($name)
	{
		return $this->cc($name);
	}

	protected function createComponentBar()
	{
		return new BarControl;
	}

	protected function createComponentTest()
	{
		return new BarControl;
	}

}

class Container extends Nette\DI\Container
{

	public $meta = array();

}

$testCache = new WidgetsControlTraitTest;
$testCache->run();
