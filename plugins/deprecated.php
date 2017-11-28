<?php

  $cmd = array('id' => 'plugin.deprecated', 
               'level' => 'moderator broadcaster owner',
               'help' => 'Deprecated Command Help Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      $tmp = $this->tmp;
      $tmp['deprecated'][$channelname] = array('!addcmd' => '$commands add <command> <text>', 
                                           '!editcmd' => '$commands edit <command> <text>',
                                           '!delcmd' => '$commands delete <command>',
                                           '!addquote' => '$quotes add <text>',
                                           '!delquote' => '$quotes delete <ID>',
                                           '!cmds' => '$commands');
      $this->tmp = $tmp;
    } else {
      if($this->access($cmd['level'])) {
        switch($this->mode) {
          case 'PRIVMSG':
            if(substr($this->command, 0, 1) == '!') {
              if(isset($this->tmp['deprecated'][$this->target][$this->command])) {
                $this->say($this->target, true, $this->nls('plugin.deprecated.command', '@{0} - This command is deprecated! Please use {1}', $this->username, $this->tmp['deprecated'][$this->target][$this->command]));
              }
            }
          break;
          default:
        }
      }
    }
  }

?>