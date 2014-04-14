<?php
class GetinfoAction extends Action{
        public function sexToStyle(){
            $sid = trim($this->_request('sid'));
            $sid = $sid?$sid:0;
            if(S('sid'.$sid)){
             $result = unserialize(S('sid'.$sid));
            }else{
            switch($sid){
                case 3 :
                $where['u_settings_gender_style.genderID'] = array('exp','IN(3,4)');
                $result = $this->getGS($where);
                break;
                case 4 :

                break;
                default :
                $where['u_settings_gender_style.genderID'] = $sid;
                $result = $this->getGS($where);
                break;
            }
            $recomodel = D('Reco');
            foreach($result as $k=>$v){
            $result[$k]['pid'] = $recomodel->pageToDataStyle($v['ID']);
            }
            S('sid'.$sid,serialize($result),array('type'=>'file'));
          }
            $this->ajaxReturn($result, 'JSON');
        }
    public function getGS($where){
           $list = M('SettingsGenderStyle')->cache(true)->join('inner join u_settings_suit_style as sss on u_settings_gender_style.styleID=sss.ID')->distinct(true)->field('sss.ID')->where($where)->select();
           return $list;
    }
    //mini左侧点击风格获取数据
    public function styleToData(){
            $sid = trim($this->_post('sid'));
            $fid = trim($this->_post('fid'));
            if(S('sf'.$sid.$fid)){
               $result = unserialize(S('sf'.$sid.$fid));
            }else{
               $recomodel = D('Reco');
            switch($sid){
                case 3 :
                    $where['suitStyleID'] = $fid;
                    $where['suitGenderID'] = array('exp','IN(3,4)');
                    $result = $recomodel->getBeubeu($where);
                    break;
                case 4 :

                    break;
                default :
                    $where['suitStyleID'] = $fid;
                    $where['suitGenderID'] = $sid;
                    $result = $recomodel->getBeubeu($where);
                    break;
            }
          S('sf'.$sid.$fid,serialize($result),array('type'=>'file'));
          }
          $this->ajaxReturn($result, 'JSON');
    }
}