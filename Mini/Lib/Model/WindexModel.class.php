<?php
class WindexModel extends Model{
    public function getwindex($tem=0){
        $windex = M('Windex');
        if($tem<=-10){
            $tem = -10;
        }
        $result = $windex->field('id')->where(array('stm'=>array('elt',$tem),'etm'=>array('egt',$tem)))->find();
        switch($result['id']){
            case 1 :
                $tstr = $result['id'].',2,8';
                break;
            case 7 :
                $tstr =$result['id']. ',6,8';
                break;
            default :
                $tstr = $result['id'].','.($result['id']-1).','.($result['id']+1).',8';
                break;
        }
        $arr['wid'] = $result['id'];
        $arr['str'] = $tstr;
        return $arr;
    }
    //取出传入不带字的地区
    public function getrecommend($pro){
        $goodtag = M('Goodtag');
        $rem = M('Recommend');
        switch($pro){
            case '上海' :
            case '江苏' :
            case '浙江' :
            case '福建' :
            case '台湾' :
            case '湖北' :
            case '湖南' :
            case '江西' :
            case '安徽' :
            case '广东' :
            case '广西' :
            case '海南' :
            case '重庆' :
            case '贵州' :
            case '四川' :
            case '云南' :
            case '西藏' :
            case '香港' :
            case '澳门' :
                $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//上装
                $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//上装
                break;
            case '辽宁' :
            case '吉林' :
            case '黑龙江' :
            case '内蒙古' :
            case '北京' :
            case '天津' :
            case '河北' :
            case '山西' :
            case '山东' :
            case '河南' :
            case '陕西' :
            case '宁夏' :
            case '甘肃' :
            case '青海' :
            case '新疆' :
                $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'1'))->select();//上装
                $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'2'))->select();//上装
                break;
            default :
                $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//上装
                $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//上装
        }
        foreach($reulist as $k=>$v){
            $ccateid = 	$goodtag->field('ccateid')->where(array('good_id'=>$v['id']))->find();
            $reulist[$k]['ccateid'] = $ccateid['ccateid'];
        }
        foreach($redlist as $k=>$v){
            $ccateid = 	$goodtag->field('ccateid')->where(array('good_id'=>$v['id']))->find();
            $redlist[$k]['ccateid'] = $ccateid['ccateid'];
        }
        $arr[] = $reulist;
        $arr[] = $redlist;
        return $arr;
    }
//传入的城市带省
    public function getrecommend2($pro){
        $goodtag = M('Goodtag');
        $rem = M('Recommend');
        switch($pro){
            case '上海市' :
            case '江苏省' :
            case '浙江省' :
            case '福建省' :
            case '台湾省' :
            case '湖北省' :
            case '湖南省' :
            case '江西省' :
            case '安徽省' :
            case '广东省' :
            case '广西' :
            case '海南省' :
            case '重庆市' :
            case '贵州省' :
            case '四川省' :
            case '云南省' :
            case '西藏' :
            case '香港' :
            case '澳门' :
                $uclothes = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//上装
                $dclothes = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//上装$dclothes
                break;
            case '辽宁省' :
            case '吉林省' :
            case '黑龙江省' :
            case '内蒙古' :
            case '北京市' :
            case '天津市' :
            case '河北省' :
            case '山西省' :
            case '山东省' :
            case '河南省' :
            case '陕西省' :
            case '宁夏' :
            case '甘肃省' :
            case '青海省' :
            case '新疆' :
                $uclothes = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'1'))->select();//上装
                $dclothes = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'2'))->select();//上装
                break;
            default :
                $uclothes = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//上装
                $dclothes = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//上装
        }
        foreach($uclothes as $k=>$v){
            $ccateid = 	$goodtag->field('ccateid')->where(array('good_id'=>$v['id']))->find();
            $uclothes[$k]['ccateid'] = $ccateid['ccateid'];
        }
        foreach($dclothes as $k=>$v){
            $ccateid = 	$goodtag->field('ccateid')->where(array('good_id'=>$v['id']))->find();
            $dclothes[$k]['ccateid'] = $ccateid['ccateid'];
        }
        $arr[] = $uclothes;
        $arr[] = $dclothes;
        return $arr;
    }
//去重
    public function saomo($arr1,&$arr2){
        foreach($arr2 as $k=>$v){
            foreach($arr1 as $k2=>$v2){
                if($v['id']==$v2['id']){
                    unset($arr2[$k]);
                    break;
                }
            }
        }
    }
//取得指定数据
    public function getHundred($where){
        $goodtag = M('Goodtag');
        $result = $goodtag->cache(true)->join('INNER JOIN u_beubeu_goods on u_beubeu_goods.id=u_goodtag.good_id')->field('u_beubeu_goods.id,u_beubeu_goods.num_iid,u_beubeu_goods.type,u_beubeu_goods.title,u_beubeu_goods.num,u_beubeu_goods.price,u_beubeu_goods.pic_url,u_beubeu_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_beubeu_goods.uptime desc')->limit('0,4')->select();
        return $result;
    }
    //kimi20140603 获取排序数据
    public function getOrderStr($oid=1){
           switch($oid){
               case 1 :
                   $orderStr = 'order by bg.num_iid desc';//默认排序
               break;
               case 2 :
                   $orderStr = 'order by bg.num_iid desc';//热度排序
               break;
               case 3 :
                   $orderStr = 'order by bg.num_iid desc';//新品排序
               break;
               case 4 :
                   $orderStr = 'order by bg.price asc';//价格升序
               break;
               case 5 :
                   $orderStr = 'order by bg.price desc';//价格降序
                   break;
           }
          return $orderStr;
    }
   public function colorDetail($csql){
       $insql = "select qw.num_iid from ($csql) as qw";
       $sql = "SELECT distinct u_products_beubeu.color as colorid,u_products.num_iid as num_iid,u_products.url  as colorcode,u_color.color_name as colorname,left(u_goods.item_bn,8) as uq,u_settings.value as gender FROM `u_goods` INNER JOIN u_products_beubeu on left(u_goods.item_bn,8) = u_products_beubeu.uq INNER JOIN u_settings on u_settings.key = u_goods.gender INNER JOIN u_color on u_color.id = u_products_beubeu.color INNER JOIN u_products on u_products.num_iid=u_goods.num_iid and u_products.cid=u_products_beubeu.color WHERE u_goods.num_iid in($insql) AND ( u_products_beubeu.status = '1' ) GROUP BY uq,colorid ORDER BY u_products_beubeu.id ";
    return M('Goods')->query($sql);
}

    public function doColor(&$result,$colorData){
        $productSyn = D('ProductSyn');
        foreach($result as $k1=>$v1){
            $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
            //$result[$k1]['products'] = $productSyn->GetProductColorByID($v1['num_iid']);
            $chil = array();
            foreach($colorData as $k2=>$v2){
               if($v1['num_iid']==$v2['num_iid']){
                   $chil[] = $v2;
               }
            }
            $result[$k1]['products'] = $chil;
        }
    }
}
