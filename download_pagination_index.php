<?php
//defined('MOODLE_INTERNAL') || die();
require_once "../../config.php";
require_once $CFG->dirroot . "/local/crudfiles/classes/form/user_form.php";
require_once $CFG->dirroot . "/local/crudfiles/filters.php";
//require_once($CFG->dirroot.'/local/crudfiles/livesearch.php');

require_login();

$context = context_system::instance();
//require_capability('local/crudfiles:view', $context);

$PAGE->set_url(new moodle_url("/local/crudfiles/index.php"));
$PAGE->set_context($context);
$PAGE->set_title("Home ");
$PAGE->set_heading("Registred Users Table");

$output = $PAGE->get_renderer("local_crudfiles");

echo $output->header();
//echo $output->heading('Registered USer');

//   download format selector

$pluginmanager = \core_plugin_manager::instance();
$formats = $pluginmanager->get_plugins_of_type("dataformat");

$options = [];
foreach ($formats as $format => $plugin) {
    if ($plugin->is_enabled()) {
        $options[$format] = $plugin->displayname;
    }
}

if ($context) {
    echo $OUTPUT->single_button(
        new moodle_url("/local/crudfiles/add.php"),
        get_string("addUser", "local_crudfiles")
    );
}

//filters in table

\local_crudfiles\filter_form();


//Live search
echo \html_writer::empty_tag("input", [
    "type" => "text",
    "name" => "search",
    "value" => optional_param("search", "", PARAM_TEXT),
    "placeholder" => "Search ...",
]);

$search = optional_param("search", "", PARAM_ALPHA);

// JS call
$PAGE->requires->js_call_amd(
    "local_crudfiles/main", "init",
    [["search" => $search]]
);

////////  Adhoc Task
$url = new moodle_url("/local/crudfiles/index.php", ["run" => 1]);
echo $OUTPUT->single_button($url, "Queue Adhoc Task");
// If user clicks button
if (optional_param('run', 0, PARAM_INT)) {

    // Create a task instance
    $task = new \local_crudfiles\task\my_adhoc_task();

    // Add custom data (you can pass anything here)
    $data = new \stdClass();
    $data->userid = $USER->id;
    $task->set_custom_data($data);

    // Optionally associate with a user
    $task->set_userid($USER->id);

    // Queue the task
    \core\task\manager::queue_adhoc_task($task);

    echo $OUTPUT->notification('Adhoc task queued successfully! Check cron output.', 'notifysuccess');
}

//echo $output->list_users();

echo html_writer::start_tag("div", ["id" => "user-table"]);
echo $output->list_users();
echo html_writer::end_tag("div");

////Download
echo html_writer::start_tag("form", [
    "method" => "get",
    "action" => new \moodle_url("/local/crudfiles/download.php"),
]);

echo html_writer::label("select format", "format");
echo html_writer::select($options, "format", "csv", ["id" => "format"]);
echo html_writer::empty_tag("input", [
    "type" => "submit",
    "value" => "Download",
    "class" => "btn btn-primary",
]);

echo html_writer::end_tag("form");

//// template mustache
// Sample data
// $records = [
//     (object)['name' => 'Urmila'],
//     (object)['name' => 'Ahirwar'],
//     (object)['name' => 'Moodle Developer']
// ];

// Create renderable object
//$demo = new local_crudfiles\output\mustache($records);

// Render template
//echo $OUTPUT->render($demo);

//////end template mustache
echo $output->footer();
