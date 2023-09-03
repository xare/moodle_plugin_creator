#!/usr/bin/env php
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('CLI_SCRIPT', true);
require(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->libdir.'/clilib.php');      // cli only functions

list($options, $unrecognized) = cli_get_params(
    array('help' => false, 'name' => '','languages'=>''),
    array('h' => 'help', 'n' => 'name', 'l'=>'languages')
);

if ($options['help'] || !$options['name'] || !$options['languages']) {
    $help = "Create a new local Moodle plugin.

Options:
-n, --name         Name of the new local plugin.
-l, --languages    Comma separated list of language ISO Codes to create lang files for.
-h, --help         Print out this help.

Example:
\$sudo -u www-data /usr/bin/php local/plugincreator/cli/create.php --name=newpluginname --languages=en,es,fr
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

file_put_contents($plugindir . '/version.php', "<?php

defined('MOODLE_INTERNAL') || die();

\$plugin->component = 'local_$name';
\$plugin->version = " . time() . ";
\$plugin->requires = 2020061500;
\$plugin->maturity = MATURITY_STABLE;
\$plugin->release = 'v1.0.0';
");

file_put_contents($plugindir. '/README.md'," 
#PLUGIN ".$name."#");
mkdir($plugindir . '/lang');
if ($options['languages']) {
    $langs = explode(',', $options['languages']);
    foreach ($langs as $lang) {
        $lang = trim($lang);
        $langdir = $plugindir . "/lang/".$lang;
        mkdir($langdir, 0777, true);
        
        // Sample language string file content.
        $langcontent = "<?php
\$string['pluginname'] = 'My $name plugin';
// Add more language strings as needed.
";
        $result = file_put_contents($langdir . "/local_$name.php", $langcontent);
        if ($result === false) {
            echo "Failed to write to $langdir/local_$name.php\n";
        } else {
            echo "The file for the ".$lang." language was created";
        }
    }
}

mkdir($plugindir . '/db');
mkdir($plugindir . '/classes');
mkdir($plugindir . '/templates');
echo "Plugin '$name' created successfully!\n";