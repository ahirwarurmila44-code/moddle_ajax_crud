<?php

require_once('../../config.php');

defined('MOODLE_INTERNAL') || die(); 

$format = required_param('format', PARAM_ALPHA);

$filename = 'Registered Users';
$columns = ['Image','Name', 'Email', 'Mobile', 'Gender', 'Sujects', 'Created Time'];

$records = $DB->get_records('local_crudfiles'); 

$rows = [];
foreach($records as $user){

  $fs = get_file_storage();
  $files = $fs->get_area_files(
        \context_system::instance()->id, 
        'local_crudfiles',
        'image' , 
        $user->id ,
        'filename', 
        false
  );

  if($files){
      $file = reset($files);
      $imgurl = \moodle_url::make_pluginfile_url(
          $file->get_contextid(),
          $file->get_component(),
          $file->get_filearea(),
          $file->get_itemid(),
          $file->get_filepath(),
          $file->get_filename()
      );
  }

  //$img = $user->image ? \html_writer::empty_tag('img', ['src'=>$imgurl, 'width'=> 50, 'height'=>50]) : '';

    $rows[] = [
      $imgurl,
      $user->name,
      $user->email,
      $user->mobile,
      $user->gender,
      $user->subjects,
      userdate($user->createdtime)
    ];
  }

$callback = [];

core\dataformat::download_data($filename, $format, $columns, $rows);
