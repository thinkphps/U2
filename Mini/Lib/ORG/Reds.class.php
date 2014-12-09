<?php
 //redis公共文件
class Reds{
    protected $redis;
    protected $host = '127.0.0.1';
    protected $port = 6379;
    public function __construct(){
        $this->redis = new redis();
        $this->redis->connect($this->host,$this->port);
    }
    public function set($key,$value){
       $this->redis->set($key,$value);
    }
    public function get($key){
        return $this->redis->get($key);
    }
    public function delete($key){
        return $this->redis->delete($key);
    }
}