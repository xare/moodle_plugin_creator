#!/usr/bin/env php
<?php

define('CLI_SCRIPT', true);
require(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->libdir.'/clilib.php');      // cli only functions

list($options, $unrecognized) = cli_get_params(
    array('help' => false, 'name' => ''),
    array('h' => 'help', 'n' => 'name')
);

if ($options['help'] || !$options['name']) {
    $help = "Create a new local Moodle plugin.

Options:
-n, --name         Name of the new local plugin.
-h, --help         Print out this help.

Example:
\$sudo -u www-data /usr/bin/php local/plugincreator/cli/create.php --name=newpluginname
";

    echo $help;
    die;
}

$name = clean_param($options['name'], PARAM_PLUGIN);

$plugindir = $CFG->dirroot . '/local/' . $name;

if (file_exists($plugindir)) {
    cli_error("Plugin with the name '$name' already exists.");
}

// Create the basic plugin structure
mkdir($plugindir, 0777, true);
mkdir($plugindir . '/db');
mkdir($plugindir . '/classes');
mkdir($plugindir . '/lang');
file_put_contents($plugindir . '/version.php', "<?php

defined('MOODLE_INTERNAL') || die();

\$plugin->component = 'local_$name';
\$plugin->version = " . time() . ";
\$plugin->requires = 2020061500;
\$plugin->maturity = MATURITY_STABLE;
\$plugin->release = 'v1.0.0';
");

echo "Plugin '$name' created successfully!\n";
