#!/usr/bin/env php
<?php

define('CLI_SCRIPT', true);
require(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->libdir.'/clilib.php');      // cli only functions

list($options, $unrecognized) = cli_get_params(
    array('help' => false, 'pluginname' => '', 'controllername' => ''),
    array('h' => 'help', 'pn' => 'pluginname', 'cn' => 'controllername')
);

if ($options['help'] || !$options['pluginname'] || !$options['controllername']) {
    $help = "Create a Controller Moodle plugin.

Options:
-pn, --pluginname         Name of the new local plugin.
-cn, --controllername     Name of new controller
-l, --languages    Comma separated list of language ISO Codes to create lang files for.
-h, --help         Print out this help.

Example:
\$sudo -u www-data /usr/bin/php local/plugincreator/cli/create_controller.php --pluginname=pluginname
";

    echo $help;
    die;
}

$pluginname = clean_param($options['pluginname'], PARAM_PLUGIN);
$controllername = clean_param($options['controllername'], PARAM_PLUGIN);

$plugindir = $CFG->dirroot . '/local/' . $pluginname;

if (!file_exists($plugindir)) {
    cli_error("Plugin with the name '$pluginname' does not exist exists.");
}

if (file_exists($plugindir."/templates/".$controllername.".mustache")) {
    cli_error("Plugin with the name '$pluginname' does not exist exists.");
}

file_put_contents($plugindir . '/templates/' . $controllername . '.mustache',"
    <p>{{controllerText}}</p>
");

// Sample associative array with language codes as keys and respective controller texts as values
$languagecodes = array(
    'en' => 'Welcome to your new controller',
    'fr' => 'Bienvenue dans votre nouveau contrôleur',
    'es' => 'Bienvenidos a su nuevo controlador',
    // ... Add more languages and texts as needed
);

// List all directories inside the $plugindir/lang directory
$all_dirs = scandir($plugindir . '/lang');

// Filter out unwanted entries, keep only directories
$actual_lang_dirs = array_filter($all_dirs, function ($dir) use ($plugindir) {
    return is_dir($plugindir . '/lang/' . $dir) && $dir != "." && $dir != "..";
});

foreach ($actual_lang_dirs as $langcode){
    if (isset($languagecodes[$langcode])) {
        $content = file_get_contents($plugindir . '/lang/'.$langcode.'/local_'.$pluginname.'.php');
        // The content you wish to add
        $new_content = "\$string['controllerText'] = '". $languagecodes[$langcode] ."';\n";

        // Replace the placeholder with itself plus the new content
        $updated_content = $content . $new_content;
        file_put_contents($plugindir . "/lang/" . $langcode . "/local_" . $pluginname . ".php", $updated_content);
    }
}
    file_put_contents($plugindir . '/' . $controllername . '.php', "<?php

/**
 * @package     local_$pluginname
 * @author      Juan
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass \$plugin
 */

require_once(__DIR__ . '/../../config.php');
\$context = \context_system::instance();

// PAGE SETUP
\$PAGE->set_url( new moodle_url('/local/$pluginname/$controllername.php'));
\$PAGE->set_context( \$context );
\$PAGE->set_title(get_string('$controllername', 'local_$pluginname'));

// PAGE RENDERING
echo \$OUTPUT->header();
\$templatecontext = (object)[
    'controllerText' => get_string( 'ControllerText', 'local_$pluginname' ),
];
echo \$OUTPUT->render_from_template('local_$pluginname/user_created', \$templatecontext);
echo \$OUTPUT->footer();

");

echo "Controller '$controllername' for plugin '$pluginname' created successfully!\n";