<?php

  $cmd = array('id' => 'tb.help',
               'level' => '',
               'help' => 'Shows command help',
               'syntax' => '!help <command>');
  
  if(isset($execute) && $execute == true) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        $subcommand = str_ireplace($this->config['NAME'], 'BOTNAME', strtolower($this->data[4]));
        $found = false;
        foreach($this->db[$this->target]['@plugins'] as $name => $plugin) {
          if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$subcommand.'.php')) {
            if($this->verify(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$subcommand.'.php')) {
              $found = true;
              try {
                $execute = false;
                include(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$subcommand.'.php');
                $levels = explode(' ', $cmd['level']);
                if(isset($cmd['id'])) {
                  $this->say($this->target, false, ' - '.$this->nls($cmd['id'], $cmd['help']).(isset($cmd['syntax']) ? ' - Syntax: '.$cmd['syntax'] : '').($levels[0] != '' ? ' ('.$this->levels[$levels[0]].' cmd)' : ''));
                } else {
                  $this->say($this->target, false, ' - '.$cmd['help'].(isset($cmd['syntax']) ? ' - Syntax: '.$cmd['syntax'] : '').($levels[0] != '' ? ' ('.$this->levels[$levels[0]].' cmd)' : ''));
                }
              } catch (Exception $e) {
                $this->error($e);
              }
            }
          }
        }
        if(!$found) {
          if(file_exists(CMDS_PATH.DIRECTORY_SEPARATOR.$subcommand.'.php')) {
            if($this->verify(CMDS_PATH.DIRECTORY_SEPARATOR.$subcommand.'.php')) {
              try {
                $execute = false;
                include(CMDS_PATH.DIRECTORY_SEPARATOR.$subcommand.'.php');
                $levels = explode(' ', $cmd['level']);
                if(isset($cmd['id'])) {
                  $this->say($this->target, false, ' - '.$this->nls($cmd['id'], $cmd['help']).(isset($cmd['syntax']) ? ' - Syntax: '.$cmd['syntax'] : '').($levels[0] != '' ? ' ('.$this->levels[$levels[0]].' cmd)' : ''));
                } else {
                  $this->say($this->target, false, ' - '.$cmd['help'].(isset($cmd['syntax']) ? ' - Syntax: '.$cmd['syntax'] : '').($levels[0] != '' ? ' ('.$this->levels[$levels[0]].' cmd)' : ''));
                }
              } catch (Exception $e) {
                $this->error($e);
              }
            }
          }
        }
      } else {
        $this->say($this->target, false, ' - Syntax: '.$cmd['syntax']);
      }
    }
  }
  
?>