<?php
require_once(__DIR__.'/../../config.php');
require_once($CFG->dirroot . '/mod/simplenotes/lib.php');

// from url
$id = required_param('id', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'simplenotes');
// $cm = get_coursemodule_from_id('simplenotes', $id, 0, false, MUST_EXIST);
// $course = $DB->get_record('course', ['id'=>$cm->course], '*', MUST_EXIST);

$simplenotes = $DB->get_record('simplenotes', ['id'=>$cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/simplenotes:view', $context);

$PAGE->set_url(new moodle_url("/mod/simplenotes/view.php", ['id' => $cm->id]));
$PAGE->set_context($context);
$PAGE->set_title("Home ");
$PAGE->set_heading("simplenotes");

echo $OUTPUT->header();
echo $OUTPUT->heading($simplenotes->name);

// Display the note content if available
if (!empty($simplenotes->intro)) {
    echo format_text($simplenotes->intro, $simplenotes->introformat, ['context' => $context]);
} else {
    echo html_writer::div(get_string('nonotecontent', 'mod_simplenotes', 'No notes found.'));
}

echo $OUTPUT->footer();
