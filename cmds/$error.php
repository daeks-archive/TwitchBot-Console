<?php

  $cmd = array('id' => 'tb.error',
               'level' => 'owner',
               'help' => 'Checks the last error',
               'syntax' => '$error');

  if($execute) {
    if($this->access($cmd['level'])) {
      if(file_exists(CACHE_PATH.DIRECTORY_SEPARATOR.get_class().'-'.$this->config['ID'].'.lasterror.db')) {
        $error = json_decode(file_get_contents(CACHE_PATH.DIRECTORY_SEPARATOR.get_class().'-'.$this->config['ID'].'.lasterror.db'), true);
        $this->say($this->target, true, 'Error: '.$error['message']. ' in '.basename($error['file'], ".php").' on line '.$error['line']);
        unlink(CACHE_PATH.DIRECTORY_SEPARATOR.get_class().'-'.$this->config['ID'].'.lasterror.db');
      }
    }
  }

?>