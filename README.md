# FaimMedia PHP Static Build generator

This repository will launch a internal webserver of your PHP project, collects pages and copy files to a directory of your chosing.

## Install

Install this library using composer:

    composer require faimmedia/static-builder 

## Example

```php
<?php

use FaimMedia\StaticBuilder\Build;
use FaimMedia\StaticBuilder\Action\{
	CombineUrl,
	Copy,
	Url,
};

$builder = new Build([
	'target' => './dist',
	'router' => './public/router.php',
	'log'    => './cache/server.log',
]);

$builder->addAction(
	new CombineUrl(
		[
			'hostname' => 'faimmedia.nl',
			'encoding' => ['gzip', 'br'],
			'path'     => 'en',
		],
		new Url([
			'url' => 'faq',
		]),
		new Url([
			'url'    => 'not-found',
			'expect' => 404,
		]),
	),
	new Url([
		'url'  => 'file/generator',
		'save' => false,
	]),
	new Copy([
		'source' => './images',
		'path'   => 'static',
	]),
);

$builder->execute();
```
