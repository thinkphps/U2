<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 14-2-25
 * Time: 上午11:19
 */

class WeatherModel extends Model{
    public function GetWeatherInfoByID($id)
    {
        $weathers = M('weather w');

        //由于客户需要展示图片，所以将所有的颜色改成图片地址。
        $weatherInfo =  $weathers
            ->join('INNER JOIN u_weathercity wct on w.cityid = wct.commoncityid')
            ->field('w.cityid,wct.commoncityname,w.weather1,w.weather2 ,w.weather3 ,w.weather4 ,w.weather5 ,w.weather6')
            ->where(array('w.cityid'=>$id))
            ->limit('0,1')
            ->select();

        return $weatherInfo;

    }
} 