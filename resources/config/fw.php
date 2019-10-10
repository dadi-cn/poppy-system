<?php

// config/sami.php
// php ./resources/sami/sami.phar update ./resources/sami/config.php

$baseDir          = dirname(dirname(__DIR__));
$frameworkFolders = glob($baseDir . '/{framework}/src', GLOB_BRACE);

$folders = $frameworkFolders;

$excludes = [];
foreach ($folders as $folder) {
	$excludes[] = $folder . '/database/seeds';
	$excludes[] = $folder . '/database/migrations';
	$excludes[] = $folder . '/database/factories';
	$excludes[] = $folder . '/update';
}

$iterator = Symfony\Component\Finder\Finder::create()
	->files()
	->name('*.php')
	->exclude('database')
	->exclude('update')
	->in($folders);

$options = [
	'theme'     => 'default',
	'title'     => 'Poppy Framework API Documentation',
	'build_dir' => $baseDir . '/public/docs/fw',
	'cache_dir' => $baseDir . '/storage/sami/framework',
];

return new \Sami\Sami($iterator, $options);