<?php
require_once(__DIR__ . '/../../config.php');
require_login();

$context = context_system::instance();

$PAGE->set_url(new moodle_url('/local/mailevent/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname','local_mailevent'));
$PAGE->set_heading(get_string('pluginname','local_mailevent'));

$userform = new \local_mailevent\form\registerform();

$PAGE->requires->js_call_amd('local_mailevent/registerform', 'init');

// $event = \core\event\mail_event::create(
//     'eventname'=> 'mailevent',
//     'context'=> $context,
//     'userid'=> $USER,
// );
global $DB, $USER;
//get_records(string $table, array $conditions = null, string $sort = '', string $fields = '*', int $limitfrom = 0, int $limitnum = 0)
$userlist = $DB->get_records('local_crudfiles', null, '', 'name, email, mobile, gender, subjects');
$files = get_file_storege();

foreach ($userlist as $user) {
   //$file = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);
    $file = $fs->get_file($context->id, 'local_mailevent', 'image', $user->id, '/', 'profile.jpg');
    if ($file) {
        // Generate URL
        $user->imageurl = moodle_url::make_pluginfile_url(
            $context->id,
            'local_mailevent',
            'image',
            $user->id,
            '/',
            'profile.jpg'
        )->out();
    } else {
        // Default placeholder
        $user->imageurl = $OUTPUT->image_url('u/f1')->out();
    }
}

//$DB->set_field($table, $field, $newvalue, $conditions);
$DB->set_field('local_crudfiles', 'image', $imageurl->out(), ['id' => $record->id]);

$templatecontext = [
    'users'=> array_values($userlist)
];
echo $OUTPUT->header();
echo $userform->display();
echo $OUTPUT->render_from_template('local_mailevent/users_table', $templatecontext);
echo html_writer::tag('h1', get_string('mailevent','local_mailevent'));
echo $OUTPUT->footer();
