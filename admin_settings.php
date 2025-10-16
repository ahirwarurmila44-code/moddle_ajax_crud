<?php

defined('MOODLE_INTERNAL') || die();

if($hassiteconfig){
    $settings = new admin_settingpage(
        'local_crudfiles',
        get_string('pluginname','local_crudfiles'));
    $perpagerecordnum = [
        1=>'1',
        2=>'2',
        5=>'5',
        10=>'10'
    ];

    $settings->add(new admin_setting_configselect(
        'local_crudfiles/perpage',
        get_string('perpage', 'local_crudfiles'),
        get_string('perpage_desc', 'local_crudfiles'),
        2,
        $perpagerecordnum
    ));

    $ADMIN->add('localplugins', $settings);
}

