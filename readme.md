# Venne:Widgets [![Build Status](https://secure.travis-ci.org/Venne/widgets.png)](http://travis-ci.org/Venne/widgets)


## Installation

The best way to install Venne/Widgets is using Composer:

```sh
composer require venne/widgets:@dev
```


## Automatic usage

### Register widgets

Use tag `venne.widget`:

```yaml
services:
	myControlFactory:
		class: App\MyControlFactory
		tags: [venne.widget: 'myWidget']
```

### Use widgets in presenters/controls as global component

```php
class ExamplePresenter extends Nette\Application\UI\Presenter
{
	use Venne\Widgets\WidgetsControlTrait;
}
```

Template:

```smarty
...
{control myWidget}
...
```


## Manual usage

**Register some widgets**

```php
$widgetManager = new Venne\Widgets\WidgetManager($systemContainer);
$widgetManager->addWidget('widget1', function() {
	return new MyComponent;
});
$widgetManager->addWidget('widget2', new MyFormFactory);
$widgetManager->addWidget('widget3', 'app.myFormFactory'); // service in system container
```

**Create instance and use it**

```php
if ($widgetManager->hasWidget('widget1')) {
	$widget = $widgetManager->getWidget('widget1');
	$widget->render();
}
```
