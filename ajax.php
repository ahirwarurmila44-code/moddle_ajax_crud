<?php
require_once(__DIR__ . '/../../config.php');
require_login(); // ensure the user is logged in
require_sesskey(); // secure against CSRF

// Only allow AJAX requests
if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

header('Content-Type: application/json');

global $DB ,$USER;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    die();
}

$name = required_param('name', PARAM_TEXT);
$email = optional_param('email', '', PARAM_EMAIL);
$password = optional_param('password', '', PARAM_TEXT);
$c_password = optional_param('c_password', '', PARAM_TEXT);
$gender = optional_param('gender', '', PARAM_TEXT);
$subjects = optional_param('subjects', '', PARAM_TEXT);
$status = optional_param('status', 0, PARAM_INT); 
$draftitemid = optional_param('image', 0, PARAM_INT);

// Debug check
error_log('Subjects received: ' . print_r($_POST['subjects'], true));


if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
    die();
}

if ($password !== $c_password) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
    die();
}

$record = new stdClass();
$record->name = $name;
$record->email = $email;
$record->password = password_hash($password, PASSWORD_DEFAULT);
$record->c_password = password_hash($c_password, PASSWORD_DEFAULT);
$record->gender = $gender;
$record->subjects = $subjects;
$record->status = $status;
$record->createdtime = time();

$fs = get_file_storage();

try {
    $recordid = $DB->insert_record('local_crudfiles', $record);
    
    if (!empty($draftitemid)) {
        $context = context_system::instance();
        file_save_draft_area_files(
            $draftitemid,
            $context->id,
            'local_mailevent',
            'image',
            $recordid,
            ['subdirs' => 0, 'maxfiles' => 1]
        );
    $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'local_mailevent', 'image', $recordid, 'id DESC', false);
        if (!empty($files)) {
            $file = reset($files);
            $filename = $file->get_filename();
            $filepath = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            )->out(false);

            // 4️⃣ Update DB record with image info
            $DB->set_field('local_crudfiles', 'image', $recordid, ['id' => $recordid]);
        }
    }
    echo json_encode(['status' => 'success', 'message' => 'Record inserted successfully']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
