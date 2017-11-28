<?php

  $cmd = array('id' => 'plugin.quotes.quote',
               'level' => '',
               'help' => 'Display quote',
               'syntax' => '!quote [ID]');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(sizeof($this->db[$this->target]['quotes']) > 0) {
        if(isset($this->data[4]) && is_numeric(ltrim($this->data[4], '#'))) {
          if(isset($this->db[$this->target]['quotes'][(ltrim($this->data[4], '#'))])) {
            if(isset($this->db[$this->target]['quotes'][(ltrim($this->data[4], '#'))])) {
              $quote = $this->db[$this->target]['quotes'][(ltrim($this->data[4], '#'))];
              $this->say($this->target, false, '#'.ltrim($this->data[4], '#').' - "'.$quote['VALUE'].'" - ('.date('d.m.Y H:i:s', strtotime($quote['INSERTED'])).')');
            }
          }
        } else {
          $quoteid = array_rand($this->db[$this->target]['quotes']);
          $quote = $this->db[$this->target]['quotes'][$quoteid];
          $this->say($this->target, false, '#'.$quoteid.' - "'.$quote['VALUE'].'" - ('.date('d.m.Y H:i', strtotime($quote['INSERTED'])).')');
        }
      }
    }
  }
  
?>