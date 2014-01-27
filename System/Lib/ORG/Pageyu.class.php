<?php
/**
 * PHPTD 留言板 FOR ThinkPHP 分页类
 * 此类是跟据 ThinkPHP 官方分页类修改而来
 * 其风格是仿照 Discuz! 分页的风格
 */
if (!defined('THINK_PATH')) exit('Access Error');

class Page {
    Public $maxRows; // 每页显示多少条记录
    public $maxPages = 500; // 最多显示多少页
    public $firstRows; // 起始行数
    public $endRows; // 当前页面结束信息行数的实际值
    public $parameter = 'p'; // 页数跳转时要带的参数
    public $rollPage = 10; // 分页栏可以显示的总页数
    public $coolPages; // 分页的栏实际显示的总页数
    public $totalRows; // 总行数( 总记录数)
    public $totalPages; // 共有多少页
    public $nowPage; // 当前页数
    public $urlDepr; // 各参数之间的分割符号
    public $str; // 锚点位置标记

    public function __construct($counts,$maxRows = 10,$str=''){
        $this->maxRows = $maxRows; // 每个页面显示多少条记录
        $this->totalRows = $counts; // 计算总记录数
        $this->totalPages = ceil($this->totalRows/$this->maxRows); // 计算总页数
        $this->nowPage = $this->getPageNum(); // 返回当前页码
        $this->firstRows = $this->nowPage * $this->maxRows; // 计算当前页面信息的开始行数
        $this->endRows = $this->getEndRows(); // 计算当前页面信息的结束行数
        $this->urlDepr = C('URL_PATHINFO_DEPR'); // 各参数之间的分割符号
        $this->str = $str;
    }

    // 获取当前页码
    function getPageNum(){
        $pageNum = 0;
        // 获取当前分页页码的整数值
        $getPage = intval($_GET[$this->parameter]);
        if (isset($getPage) && $getPage >= 1 && $getPage <= $this->totalPages) {
            $pageNum = $getPage - 1;
        }elseif(isset($getPage) && $getPage > $this->totalPages){
            $pageNum = $this->totalPages - 1;
        }
		//echo $pageNum;
		//echo '<p>';
        return $pageNum;
    }
    // 当前页面结束信息行数的实际值
    function getEndRows(){
        $endRows = $this->firstRows + $this->maxRows;
        if($endRows >= $this->totalRows){
            $endRows = $this->totalRows;
        }
        return $endRows;
    }
    // 输出分页
    public function showPage($set = array()){
        $pages=$page_1=$page_2=$page_3=$page_4='';
        if($set['css']){
            $css .= ' class="'.$set['css'].'"';
        }else{
            $css .= ' class="pages"';
        }
        if ($set['id']){
            $id .= ' id="'.$set['id'].'"';
        }else{
            $id .= ' id="pages"';
        }
        $url = trim(__ACTION__);
        $p .= $this->urlDepr.$this->parameter.$this->urlDepr;
        if(strchr($url, $p)){
            $b = strchr($url, $p);
            $urlPar =  str_replace($b, '', $url).$p;
            $url =  str_replace($b, '', $url);
        }else{
            $urlPar =  $url.$p;
            $url =  $url;
        }

        $nowPage = intval($_GET[$this->parameter]);
        $nowPage = $nowPage <= 1 ? 1 : $nowPage;
        $pages = '';
        $realpages = 1; // 当前页码
        if ($this->totalRows > $this->maxRows){
            $offset = 4; // 页码偏移量
            $realpages = @ceil($this->totalRows / $this->maxRows);//总页数
            if ($this->rollPage > $realpages) {
                $form = 1;
                $to = $realpages;
            }else {
                $form = $nowPage - $offset;
                $to = $form + $this->rollPage - 1;
                if ($form < 1) {
                    $form = 1;
                    //$to = $curpage + 1 - $form;
                    if ($to - $form < $this->rollPage) {
                        $to = $this->rollPage;
                    }
                }
            }
            $to = $to >= $realpages ? $realpages : $to;
            //$pages = ($nowPage >= 2 ? '<a href="'.$urlPar.'1'.'"> 首 页</a> ' : '');
            $pages .= ($nowPage > 1 ? '<li><a href="'.$urlPar.($nowPage - 1).$this->str.'" class="pager-prev"> 上一页</a> </li>' : '');
			if($form>1){
                 $pages .='...';
			}
            for ($i = $form; $i <= $to; $i++) {
                $pages .= ($i == $nowPage ? '<li class="active"><a class="pager-curr">'.$i.'</a> </li>' : '<li><a href="'.$urlPar.$i.$this->str.'">'.$i.'</a> </li>');
            }
			if($to<$realpages){
                $pages .='...';
			    $pages .='<li><a href="'.$urlPar.$realpages.$this->str.'">'.$realpages.'</a> ';
			}
            $pages .= ($nowPage < $realpages ? '<li><a href="'.$urlPar.($nowPage + 1).$this->str.'" class="pager-next"> 下一页</a> </li>' : '');
            //$pages .= (($nowPage < $realpages && $nowPage >= 0)? '<a href="'.$urlPar.$realpages.'"> 尾 页</a> ' : '');
        }

        return $pages;
    }
}
?>