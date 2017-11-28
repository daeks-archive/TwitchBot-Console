<?php

  $cmd = array('id' => 'tb.check',
               'level' => 'owner',
               'help' => 'Checks <command>',
               'syntax' => '$check <command>');

  if(isset($execute) && $execute == true) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        if(file_exists(CMDS_PATH.DIRECTORY_SEPARATOR.$this->data[4].'.php')) {
          $output = shell_exec('php -l "'.CMDS_PATH.DIRECTORY_SEPARATOR.$this->data[4].'.php"');
          $output = str_replace(array(chr(10), chr(13)), ' ', $output);
          $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);
          $this->say($this->target, true, ' - '.trim(str_replace('.php', '', $output)));
        } else {
          foreach (scandir(PLUGINS_PATH) as $key => $value) { 
            if (!in_array($value,array(".",".."))) { 
              if(is_dir(PLUGINS_PATH.DIRECTORY_SEPARATOR.$value)) {
                if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.$this->data[4].'.php')) {
                  $output = shell_exec('php -l "'.PLUGINS_PATH.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.$this->data[4].'.php"');
                  $output = str_replace(array(chr(10), chr(13)), ' ', $output);
                  $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);
                  $this->say($this->target, true, ' - '.trim(str_replace('\\', '/', str_replace('.php', '', $output))));
                }
              } else {
                if($this->data[4] == rtrim($value, '.php')) {
                  $output = shell_exec('php -l "'.PLUGINS_PATH.DIRECTORY_SEPARATOR.$value.'"');
                  $output = str_replace(array(chr(10), chr(13)), ' ', $output);
                  $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);
                  $this->say($this->target, true, ' - '.trim(str_replace('\\', '/', str_replace('.php', '', $output))));
                }
              }           
            }
          }
        }
      }
    }
  }

?>