<?php

  $cmd = array('id' => 'plugin.quotes.quotes',
               'level' => 'moderator broadcaster owner',
               'help' => 'Quotes Configuration',
               'syntax' => '$quotes [add|delete] (<text>|<ID>)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[5])) {
        if($this->data[4] == 'add') {
          $submessage = '';
          for($i=5;$i<sizeof($this->data);$i++) {
            $submessage .= $this->data[$i].' ';
          }
          
          if(strlen(trim($output)) < $this->db[$this->target]['config']['limit']) {
            if ($this->db[$this->target]['ID'] > 0) {
              $dbid = $this->db()->insert('PLUGIN_QUOTES', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'VALUE' => $submessage, 'INSERTBY' => strtolower($this->username)));
              $this->reinit($this->target);
              $this->say($this->target, true, '@'.$this->username.' quote added with ID #'.$dbid);
            }
          }
        } else if($this->data[4] == 'delete') {
          if($this->db[$this->target]['ID'] > 0) {
            if(isset($this->db[$this->target]['quotes'][$this->data[5]])) {
              $stmt = $this->db()->delete('PLUGIN_QUOTES', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and ID=".$this->data[5]);
              $this->reinit($this->target);
              $this->say($this->target, true, '@'.$this->username.' quote deleted');
            }
          }
        }
      } else {
        if(sizeof($this->db[$this->target]['quotes']) > 0) {
          $this->say($this->target, false, '@'.$this->username.' - Available quotes are: #'.implode(' #', array_keys($this->db[$this->target]['quotes'])));
        }
      }
    } else {
      if(sizeof($this->db[$this->target]['quotes']) > 0) {
        $this->say($this->target, false, '@'.$this->username.' - Available quotes are: #'.implode(' #', array_keys($this->db[$this->target]['quotes'])));
      }
    }
  }
  
?>