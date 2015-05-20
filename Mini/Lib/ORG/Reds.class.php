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
    public function llen($key){
        return $this->redis->LLEN($key);
    }
    public function lrange($key,$start,$limit){
        return $this->redis->LRANGE($key,$start,$limit);
    }
    public function ltrim($key,$start,$limit){
        return $this->redis->LTRIM($key,$start,$limit);
    }
}