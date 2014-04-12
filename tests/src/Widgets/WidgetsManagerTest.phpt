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

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tester\Assert;
use Tester\TestCase;
use Venne\Widgets\WidgetManager;

require __DIR__ . '/../bootstrap.php';

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class WidgetManagerTest extends TestCase
{

	/** @var WidgetManager */
	private $widgetManager;

	public function setUp()
	{
		$container = new Container;
		$container->addService('myService', new MyControlFactory);
		$container->addService('badService', new BadControlFactory);
		$container->addService('badService2', new BadControlFactory2);
		$container->meta[$container::TAGS]['venne.widget']['myService'] = 'foo';
		$container->meta[$container::TAGS]['venne.widget']['badService'] = 'bad';
		$container->meta[$container::TAGS]['venne.widget']['badService2'] = 'bad2';
		$this->widgetManager = new WidgetManager($container);
		$this->widgetManager->addWidget('foo', 'myService');
		$this->widgetManager->addWidget('bad', 'badService');
		$this->widgetManager->addWidget('bad2', 'badService2');
	}

	public function testAddWidget()
	{
		Assert::exception(function(){
			$this->widgetManager->addWidget(1, 2);
		}, 'Nette\InvalidArgumentException', 'Name of widget must be string');

		Assert::exception(function(){
			$this->widgetManager->addWidget('foo', 2);
		}, 'Nette\InvalidArgumentException', 'Second argument must be string or factory or callable');

		Assert::type('Venne\Widgets\WidgetManager', $this->widgetManager->addWidget('foo', 'myService'));
		Assert::exception(function(){
			Assert::type('Venne\Widgets\WidgetManager', $this->widgetManager->addWidget('foo', 'myService2'));
		}, 'Nette\InvalidArgumentException', 'Service \'myService2\' does not exist');
		Assert::type('Venne\Widgets\WidgetManager', $this->widgetManager->addWidget('foo', function() { return new MyControl; }));
		Assert::type('Venne\Widgets\WidgetManager', $this->widgetManager->addWidget('foo', new MyControlFactory));
	}


	public function testGetWidget()
	{
		$this->widgetManager->addWidget('my', function() {
			return new MyControl;
		});

		Assert::type(__NAMESPACE__ . '\MyControl', $this->widgetManager->getWidget('foo'));
		Assert::type(__NAMESPACE__ . '\MyControl', $this->widgetManager->getWidget('my'));
		Assert::exception(function(){
			$this->widgetManager->getWidget('bad');
		}, 'Nette\InvalidStateException', "Widget is not instance of 'Nette\Application\UI\Control'.");
		Assert::exception(function(){
			$this->widgetManager->getWidget('bad2');
		}, 'Nette\InvalidStateException', "Service is not factory.");
		Assert::exception(function(){
			$this->widgetManager->getWidget('fooo');
		}, 'Nette\InvalidArgumentException', "Widget fooo does not exists");
	}


	public function testGetWidgets()
	{
		Assert::same(array('foo' => 'myService', 'bad' => 'badService', 'bad2' => 'badService2'), $this->widgetManager->getWidgets());
	}

}

class MyControl extends Control {}

class MyControlFactory {
	public function create(){
		return new MyControl;
	}
}

class BadControlFactory {
	public function create(){
		return 'aa';
	}
}

class BadControlFactory2 {

}

class Container extends \Nette\DI\Container {

	public $meta = array();

}

$testCache = new WidgetManagerTest;
$testCache->run();
