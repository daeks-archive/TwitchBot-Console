<?php

  $cmd = array('id' => 'plugin.quotes', 
               'level' => '',
               'help' => 'Quotes Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      $db[$channelname]['quotes'] = array();
      foreach ($this->db()->select('PLUGIN_QUOTES', 'BOTID='.$this->config['ID'].' AND CHANNELID='.$channelid)->fetchAll() as $quote) {
        $db[$channelname]['quotes'][strtolower($quote['ID'])] = $quote;
      }
    }
  }

?>