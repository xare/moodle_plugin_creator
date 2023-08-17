<?php

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_plugincreator';  // Full name of the plugin (used for diagnostics).
$plugin->version   = 2023081200;             // The current module version (Date: YYYYMMDDXX).
$plugin->release   = 'v1.0.0';               // Human-readable version name.
$plugin->maturity  = MATURITY_STABLE;        // The current code maturity level.
$plugin->requires  = 2022061500;             // Requires this Moodle version (4.2).

// The above date is just a placeholder (year-month-day and two extra digits). 
// Typically, when you update your plugin, you'd update this date to the current date.
