<?php

class TestAction extends Action {
    public $products ;
    public $key;
    public $app;

    public function index(){
      if(S('fg123')){
          //echo S('fg123');
          echo '<p>';
          echo date('Y-m-d H:i:s');
      }else{
      S('fg123',date('Y-m-d H:i:s'));
      }
    }

}