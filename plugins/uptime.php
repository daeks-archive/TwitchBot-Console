<?php

  $cmd = array('id' => 'plugin.uptime', 
               'level' => '',
               'help' => 'Live uptime Check Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      $tmp = $this->tmp;
      $tmp['uptime'][$channelname] = array();
      $this->tmp = $tmp;
    } else {
      if($this->access($cmd['level'])) {
        $tmp = $this->tmp;
        switch($this->mode) {  
          case 'JOIN':
            if(ltrim($this->data[0], ':') == strtolower($this->config['IRC'])) {
                  if($this->target == '#'.strtolower($this->config['NAME'])) {
                    if(sizeof($tmp['uptime']['#'.strtolower($this->config['NAME'])]) == 0) {
                      $tmp['uptime']['#'.strtolower($this->config['NAME'])] = array('chat' => time());
                    }
                  } else {
                    if($this->target == '#'.strtolower($this->username)) {
                      if(in_array(ltrim($this->target,'#'), array_map('strtolower', $this->tmp['moderator'][$this->target]))) {
                        if(sizeof($tmp['uptime'][$this->target]) == 0) {
                          $tmp['uptime'][$this->target] = array('chat' => time());
                        }
                        $this->say(null, true, 'Possible live stream detected - '.ltrim($this->target,'#').' found in '.$this->target);
                      }
                    }
                  }
            } else {
              if($this->username == ltrim($this->target,'#')) {
                //$this->say($this->target, true, 'Welcome, '.$this->username);
                if(sizeof($tmp['uptime'][$this->target]) == 0) {
                  $tmp['uptime'][$this->target] = array('chat' => time());
                }
                $this->say(null, true, 'Possible live stream detected - '.$this->username.' joined '.$this->target);
              }
            }
            break;
          case 'PART':
            if(ltrim($this->data[0], ':') != strtolower($this->config['IRC'])) {
              if($this->username == ltrim($this->target,'#')) {
                $tmp['uptime'][$this->target] = array();
              }
            }
            break;
          case '353':
            // Getting NAMES list
            $start = array_search($this->data[4], $this->data)+1;
            for($i=$start;$i<sizeof($this->data);$i++) {
              if(strtolower(ltrim($this->data[$i], ':')) == strtolower(ltrim($this->target,'#')) && strtolower(ltrim($this->data[$i], ':')) != strtolower($this->config['NAME'])) {
                if(sizeof($tmp['uptime'][$this->target]) == 0) {
                  $tmp['uptime'][$this->target] = array('chat' => time());
                }
                $this->say(null, true, 'Possible live stream detected - '.ltrim($this->data[$i], ':').' found in '.$this->target);
              }
            }
            if(in_array(strtolower(ltrim($this->target,'#')), array_map('strtolower', $tmp['moderator'][$this->target]))) {
              if(sizeof($tmp['uptime'][$this->target]) == 0) {
                $tmp['uptime'][$this->target] = array('chat' => time());
                  if(ltrim($this->target,'#') != strtolower($this->config['NAME'])) {
                    $this->say(null, true, 'Possible live stream detected - '.ltrim($this->target,'#').' found in '.$this->target);
                  }
              }
            }
            break;
          case 'PRIVMSG':
            if($this->username == ltrim($this->target,'#') && strtolower($this->username) != strtolower($this->config['NAME'])) {
              if(sizeof($tmp['uptime'][$this->target]) == 0) {
                $tmp['uptime'][$this->target] = array('chat' => time());
                $this->say(null, true, 'Possible live stream detected - '.strtolower(ltrim($this->target,'#')).' found in '.$this->target);
              }
            }
          break;
          default:
        }
        $this->tmp = $tmp;
      }
    }
  }

?>