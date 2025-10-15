<?php
namespace local_mailevent\form;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

class registerform extends \moodleform {

    public function definition() {
        $mform = $this->_form;
       $mform->updateAttributes(['id' => 'mailevent_register_form']); 

        $mform->addElement('text', 'name', 'Name');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text', 'email', 'Email');
        $mform->setType('email', PARAM_EMAIL);

        $mform->addElement('passwordunmask', 'password', 'Password');
        $mform->addElement('passwordunmask', 'c_password', 'Confirm Password');

        $genderarray = [];
        $genderarray[] = $mform->createElement('radio', 'gender', '', 'Male', 'male');
        $genderarray[] = $mform->createElement('radio', 'gender', '', 'Female', 'female');
        $mform->addGroup($genderarray, 'gender', 'Gender', [' '], false);

        $subjects = ['Hindi','English','Maths','Geography','History'];
        foreach ($subjects as $subject) {
            $mform->addElement('advcheckbox', 'subject_'.$subject, $subject); 
        }

        $mform->addElement('advcheckbox', 'status', 'Status', 'Active', '1');

        $mform->addElement('filepicker', 'image', 'Image');

        $mform->addElement('submit', 'submitbutton', 'Submit');
    }

    public function validation($data, $files) {
        $errors[] = parent::validation($data, $files);

        if ($data['password'] !== $data['c_password']) {
            $errors['c_password'] = 'Passwords do not match';
        }
        return $errors;
    }

}
