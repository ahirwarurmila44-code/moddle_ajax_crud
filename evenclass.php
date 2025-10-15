<?php
namespace local_mailevent\event;

defined('MOODLE_INTERNAL') ||  die();

class mail_event extends core\event\base{
    protected function  init(){
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = SYSTEM;
        $this->data['objecttable'] = 'local_crudfiles';
    }

    public static function get_name(){
        return "Mail Event";
    }
     public function get_description(){
        return "Send mail when a new user registered";
    }
     public function get_url(){
        return new \moodle_url('user/registered/', ['id'=>relateduserid]);
    }
}
