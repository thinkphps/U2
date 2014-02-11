<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14-1-27
 * Time: 下午6:41
 */

class SettingsModel extends Model{

    public function getKeyValue($key){
        $settings = M('Settings');
        $value = $settings->cache(true)->field('value')->where(array('key'=>$key))->find();
        return $value['value'];
    }

    public function getMaleValue(){
        return $this->getKeyValue("male");
    }

    public function getFemaleValue(){
        return $this->getKeyValue("female");
    }

    public function getBoyValue(){
        return $this->getKeyValue("boy");
    }

    public function getGirlValue(){
        return $this->getKeyValue("girl");
    }

    public function getBaiyiTouchId(){
        return $this->getKeyValue("baiyi_touchid");
    }

    public function getBaiyiTouchIdKey(){
        return $this->getKeyValue("baiyi_touchid_key");
    }

    public function getTableRowCounts(){
        return (int)$this->getKeyValue("TableRowCounts");
    }

    public function getAPIInvokeCounts(){
        return (int)$this->getKeyValue("APIInvokeCounts");
    }
} 