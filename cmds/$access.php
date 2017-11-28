<?php

  $cmd = array('id' => 'tb.access',
               'level' => 'moderator broadcaster owner',
               'help' => 'Access Configuration',
               'syntax' => '$access [regular|moderator] ([add|delete] <username>)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[6])) {
        if($this->data[4] == 'regular') {
          if($this->data[5] == 'add') {
            if($this->db[$this->target]['ID'] > 0) {
              if(!in_array(strtolower($this->data[6]), array_map('strtolower', $this->tmp['regular'][$this->target]))) {
                $this->db()->insert('REGULARS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => strtolower($this->data[6]), 'INSERTBY' => strtolower($this->username)));
                $this->say($this->target, true, '@'.$this->username.' - Added '.$this->data[6].' as REGULAR to '.$this->target);
                $this->reinit($this->target);
              } else {
                $this->say($this->target, true, '@'.$this->username.' - '.$this->data[6].' already added as REGULAR to '.$this->target);
              }
            } else {
              if(!in_array(strtolower($this->data[6]), array_map('strtolower', $this->tmp['regular'][$this->target]))) {
                $tmp = $this->tmp;
                array_push($tmp['regular'][$this->target], strtolower($this->data[6]));
                $this->tmp = $tmp;
                $this->say($this->target, true, '@'.$this->username.' - Added '.$this->data[6].' as REGULAR to '.$this->target);
              } else {
                $this->say($this->target, true, '@'.$this->username.' - '.$this->data[6].' already added as REGULAR to '.$this->target);
              }
            }
          } else if ($this->data[5] == 'delete') {
            if($this->db[$this->target]['ID'] > 0) {
              if(in_array(strtolower($this->data[6]), array_map('strtolower', $this->tmp['regular'][$this->target]))) {
                $this->db()->delete('REGULARS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".strtolower($this->data[6])."'");
                $this->say($this->target, true, '@'.$this->username.' - Removed '.$this->data[6].' as REGULAR from '.$this->target);
                $this->reinit($this->target);
              }
            } else {
              if(in_array(strtolower($this->data[6]), array_map('strtolower', $this->tmp['regular'][$this->target]))) {
                $tmp = $this->tmp;
                unset($tmp['regular'][$this->target][array_search(strtolower($this->data[6]), $this->tmp['regular'][$this->target])]);
                $this->tmp = $tmp;
                $this->say($this->target, true, '@'.$this->username.' - Removed '.$this->data[6].' as REGULAR from '.$this->target);
              } 
            }
          }
        } else if($this->data[4] == 'moderator') {
          if($this->data[5] == 'add') {
            if(!in_array(strtolower($this->data[6]), $this->tmp['moderator'][$this->target])) {
              $tmp = $this->tmp;
              array_push($tmp['moderator'][$this->target], strtolower($this->data[6]));
              $this->tmp = $tmp;
              $this->say($this->target, true, '@'.$this->username.' - Temporary added '.$this->data[6].' as MODERATOR to '.$this->target);
            } else {
              $this->say($this->target, true, '@'.$this->username.' - '.$this->data[6].' already added as MODERATOR to '.$this->target);
            }
          } else if ($this->data[5] == 'delete') {
            if(($key = array_search(strtolower($this->data[6]), $this->tmp['moderator'][$this->target])) !== false) {
              $tmp = $this->tmp;
              unset($tmp['moderator'][$this->target][$key]);
              $this->tmp = $tmp;
              $this->say($this->target, true, '@'.$this->username.' - Removed '.$this->data[6].' as MODERATOR from '.$this->target);
            }
          }
        }
      } else {
        if(isset($this->data[4])) {
          if(isset($this->tmp[$this->data[4]][$this->target])) {
            $this->say($this->target, false, '@'.$this->username.' - '.strtoupper($this->data[4]).' for channel '.$this->target.': '.implode(', ', $this->tmp[trim($this->data[4])][$this->target]));
          }
        }
      }
    }
  }
  
?>