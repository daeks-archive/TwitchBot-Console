<?php

  $cmd = array('id' => 'plugin.loyality', 
               'level' => '',
               'help' => 'Loyality Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      $db[$channelname]['config']['@plugins']['loyality'] = array();
      if(!isset($db[$channelname]['config']['@plugins']['loyality']['unit'])) {
        $db[$channelname]['config']['@plugins']['loyality']['unit'] = 'loyality point';
      }
      if(!isset($db[$channelname]['config']['@plugins']['loyality']['units'])) {
        $db[$channelname]['config']['@plugins']['loyality']['units'] = 'loyality points';
      }
    }
  }

?>