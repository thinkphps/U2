<?php
class SuitsAction extends Action{
    private $aid;
    private $nick;
    public function _initialize(){
        $this->aid = session('aid');
        $this->nick = session('nickn');
        $this->assign('aid',$this->aid);
        $this->assign('nick',$this->nick);
    }
    public function index(){
           if(!empty($this->aid)){
               $sutis = M('Suits');
               $style = M('SettingsSuitStyle');
               $where = array();
               $suitGenderID = trim($this->_post('pid'));
               $suitStyleID = trim($this->_post('stylevalue'));
               $pagestr = '';
               if(!empty($suitGenderID)){
                $where['suitGenderID'] = $suitGenderID;
                   $pagestr.="/pid/".$suitGenderID;
                   $stylelist = M('SettingsGenderStyle')->join("inner join u_settings_suit_style sss on u_settings_gender_style.styleID=sss.ID")->field('sss.ID,sss.description')->where(array('u_settings_gender_style.genderID'=>$suitGenderID))->select();
               }
               if(!empty($suitStyleID)){
                $where['suitStyleID'] = $suitStyleID;
                $pagestr.="/stylevalue/".$suitStyleID;
               }
               $gender = M('SettingsSuitGender');
               $resultGender = $gender->field('*')->select();
               import("@.ORG.Pageyu");
               $count = $sutis->count();
               $p = new Page($count,20,$pagestr);
               $list = $sutis->field('*')->where($where)->order('suitID desc')->limit($p->firstRows.','.$p->maxRows)->select();

               foreach($list as $k=>$v){
               $resutltStyle = $style->field('description')->where(array('ID'=>$v['suitStyleID']))->find();
               $list[$k]['style'] = $resutltStyle['description'];
               switch($v['suitGenderID']){
                   case '1' :
                       $sex = '女装';
                   break;
                   case '2' :
                       $sex = '男装';
                       break;
                   case '3' :
                       $sex = '女童';
                       break;
                   case '4' :
                       $sex = '童装';
                       break;
               }
                   $list[$k]['sex'] = $sex;
               }
               $page = $p->showPage();
               $this->assign('list',$list);
               $this->assign('page',$page);
               $this->assign('resultGender',$resultGender);
               $this->assign('stylelist',$stylelist);
               $this->assign('suitGenderID',$suitGenderID);
               $this->assign('suitStyleID',$suitStyleID);
              $this->display();
           }else{
               $this->display('Login/index');
           }
       }
    public function add(){
          if(!empty($this->aid)){
             $suitID = trim($this->_request('id'));
             $gender = M('SettingsSuitGender');
             $resultGender = $gender->field('*')->select();
             if(!empty($suitID)){
              $result = M('Suits')->field('*')->where(array('suitID'=>$suitID))->find();
              $style = M('SettingsSuitStyle')->field('*')->select();
              $num_iid = M('SuitsGoodsdetail')->field('*')->where(array('suitID'=>$suitID))->select();

              $this->assign('result',$result);
              $this->assign('style',$style);
              $this->assign('num_iid',$num_iid);
              $this->assign('resultGender',$resultGender);
              $this->display();
             }else{
              $this->assign('resultGender',$resultGender);
              $this->display();
             }
          }else{
              $this->display('Login/index');
          }
      }

    public function doadd(){
        if(!empty($this->aid)){
            $id = trim($this->_post('id'));
            $suitGenderID = trim($this->_post('pid'));
            if(empty($suitGenderID)){
                $this->error('类别不能为空',U('Suits/add'));
                exit;
            }
            $suitStyleID = trim($this->_post('stylevalue'));
            if(empty($suitStyleID)){
                $this->error('风格不能为空',U('Suits/add'));
                exit;
            }
            $suitImageUrl = trim($this->_post('suitImageUrl'));
            if(empty($suitImageUrl)){
                $this->error('图片不能为空',U('Suits/add'));
                exit;
            }
            $num_iid = $this->_post('num_iid');
            $flag = 0;
            foreach($num_iid as $k=>$v){
                if(empty($v)){
                    $flag = 1;
                }else{
                    $flag = 0;
                    break;
                }
            }
            if($flag==1){
                $this->error('商品id不能为空',U('Suits/add'));
                exit;
            }
          $time = date('Y-m-d H:i:s');
          $suits =  M('Suits');
          $detail =  M('SuitsGoodsdetail');
         if(!empty($id)){
             //编辑
             $data = array('suitStyleID'=>$suitStyleID,
                 'suitGenderID'=>$suitGenderID,
                 'suitImageUrl'=>$suitImageUrl,
                 'uptime'=>$time);
         $suits->where(array('suitID'=>$id))->save($data);
         $detail->where(array('suitID'=>$id))->delete();
             $str = '';
             foreach($num_iid as $k1=>$v1){
                 $str.="('".$id."','".trim($v1)."'),";
             }
             $str = rtrim($str,',');
             $sql = "insert into `u_suits_goodsdetail` (`suitID`,`num_iid`) values ".$str;
             $detail->query($sql);
             $this->success('提交成功',U('Suits/index'));
             exit;
         }else{
              //添加
           $data = array('suitStyleID'=>$suitStyleID,
                          'suitGenderID'=>$suitGenderID,
                         'suitImageUrl'=>$suitImageUrl,
                         'createtime'=>$time,
                        'uptime'=>$time);
          $resid = $suits->add($data);
          if($resid){
             $str = '';
             foreach($num_iid as $k1=>$v1){
             $str.="('".$resid."','".trim($v1)."'),";
             }
             $str = rtrim($str,',');
             $sql = "insert into `u_suits_goodsdetail` (`suitID`,`num_iid`) values ".$str;
              $detail->query($sql);
              $this->success('提交成功',U('Suits/index'));
              exit;
          }else{
              $this->error('新增失败',U('Suits/add'));
          }
         }
        }else{
            $this->display('Login/index');
        }
    }
    public function getStyle(){
        $cateid = trim($this->_post('pid'));
        if(!empty($this->aid)){
          if(!empty($cateid)){
              $list = M('SettingsGenderStyle')->join("inner join u_settings_suit_style sss on u_settings_gender_style.styleID=sss.ID")->field('sss.ID,sss.description')->where(array('u_settings_gender_style.genderID'=>$cateid))->select();
             if(!empty($list)){
                 $str = "<option value='0'>选择风格</option>";
                 foreach($list as $k=>$v){
                 $str.="<option value='".$v['ID']."'>".$v['description']."</option>";
                 }
                 $returnArr = array('code'=>1,'da'=>$str);
             }else{
                 $returnArr = array('code'=>0,'msg'=>'没有数据');
             }
          }else{
              $returnArr = array('code'=>0,'msg'=>'参数错误');
          }
        }else{
            $returnArr = array('code'=>0,'msg'=>'没有登录');
        }
        $this->ajaxReturn($returnArr,'json');
    }
public function delsuit(){
    if(!empty($this->aid)){
    $id = trim($this->_request('id'));
    if($id>0){
        M('Suits')->where(array('suitID'=>$id))->delete();
        M('SuitsGoodsdetail')->where(array('suitID'=>$id))->delete();
        $this->success('删除成功',U('Suits/index'));
    }else{
        $this->error('参数错误',U('Suits/index'));
        exit;
    }
    }else{
        $this->display('Login/index');
    }
}
}