<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 15-5-15
 * Time: 下午3:28
 * 3D试衣间webservice模型
 */
class FittingModel extends Model{
    public function CkeckApp($uname,$upass){
        if($uname==C('FITTU') && $upass==C('FITTP')){
            $flag = 1;
        }else{
            $flag = 0;
        }
        return $flag;
    }
    public function GetSellercats(){
        $sell = M('Sellercats');
        $result = $sell->field('*')->where(array())->select();
        if(!empty($result) && count($result)>0){
          $arr = array('code'=>1,'data'=>$result);
          unset($result);
        }else{
          $arr = array('code'=>0,'data'=>'','msg'=>'没有数据');
        }
        return $arr;
    }
    public function GetCateGoodData($page){
        $limit = 1000;
        $offset = ($page-1)*$limit;
        $CateModel = M('Catesgoods');
        $count = $CateModel->count();
        $num_page = ceil($count/$limit);
        if($page>$num_page){
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据','page'=>0);
            return $arr;
        }
        $list = $CateModel->field('*')->where(array())->limit($offset.','.$limit)->select();
        if(!empty($list) && count($list)>0){
            $arr = array('code'=>1,'data'=>$list,'page'=>$page+1);
            unset($list);
        }else{
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据');
        }
        return $arr;
    }
    public function GetGoodsData($page){
        $limit = 500;
        $offset = ($page-1)*$limit;
        $good = M('Goods');
        $count = $good->count();
        $num_page = ceil($count/$limit);
        if($page>$num_page){
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据','page'=>0);
            return $arr;
        }
        $list = $good->field('id,num_iid,type,gender,isud,approve_status,item_bn,outer_id,title,num,price,pic_url,detail_url,list_time,delist_time,istag,isdoubt,sort,isdisplay')->where(array())->limit($offset.','.$limit)->select();
        if(!empty($list) && count($list)>0){
            foreach($list as $k=>$v){
                $list[$k]['pic_url'] = C('UNIQLOURL').$v['pic_url'];
            }
            $arr = array('code'=>1,'data'=>$list,'page'=>$page+1);
            unset($list);
        }else{
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据');
        }
        return $arr;
    }
    public function GetSkuData($page){
        $limit = 500;
        $offset = ($page-1)*$limit;
        $products = M('Products');
        $count = $products->count();
        $num_page = ceil($count/$limit);
        if($page>$num_page){
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据','page'=>0);
            return $arr;
        }
        $list = $products->field('id,goods_id,num_iid,sku_id,cid,cvalue,properties_name,quantity,url,modified')->where(array())->limit($offset.','.$limit)->select();
        if(!empty($list) && count($list)>0){
            foreach($list as $k=>$v){
                $list[$k]['url'] = C('UNIQLOURL').$v['url'];
            }
            $arr = array('code'=>1,'data'=>$list,'page'=>$page+1);
            unset($list);
        }else{
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据');
        }
        return $arr;
    }
}