<?php
$base = __DIR__ . '/../api/';

$folders = [
    'core',
    'data_access',
    'route',
];

foreach($folders as $f)
{
    foreach (glob($base . "$f/*.php") as $k => $filename)
    {
        require $filename;
    }
}