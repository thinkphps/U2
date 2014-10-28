<?php
set_time_limit(0);
class GoodsAction extends Action{
    private $client;
    private $appkey = '21805942';
    private $secretKey = 'a90b12c2a9bc72df603e90e7728abda9';
    private $products;
    private $token = '6201e24b0995f8af686fad9e7cd1d1b0ZZ5338185af9ff5196993935';
    public $dirroot;
    public function _initialize(){
        $this->dirroot = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
    }
    public function index(){
        $dirroot = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
        if(!empty($_POST)){
            import("ORG.Net.UploadFile");
            $upload = new UploadFile();
            $upload->maxSize = 512000;
            $upload->allowExts = array('csv','xlsx','xls');
            $upload->savePath = 'Upload/goodnum/';
            $upload->autoSub = true;
            $upload->subType = 'date';
            if(!$upload->upload()) {// 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            }else{// 上传成功 获取上传文件信息
                $info =  $upload->getUploadFileInfo();
            }
            $fileext = pathinfo($info[0]['name'], PATHINFO_EXTENSION);
            $good = M('Goods');
            Vendor ( 'Excel.Classes.PHPExcel' );
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d',time()));
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '货号')
                ->setCellValue('B1', '标题')
                ->setCellValue('C1', '商品链接')
                ->setCellValue('D1', '在线库存')
                ->setCellValue('E1', '一口价')
                ->setCellValue('F1', '货值');
            $baseRow = 1;
            $fileph = $dirroot.'/'.$info[0]['savepath'].$info[0]['savename'];
            switch($fileext){
                case 'csv':
                   $i = 0;
                   $fp = fopen($fileph,'r');
                   while($data = fgetcsv($fp)){
                        if($i>0){
                            $this->exportxls($data,$baseRow,$i,$objPHPExcel,$good);
                        }
                       $i++;
                   }
                    $this->headerxls();
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    $objWriter->save('php://output');
                    $objPHPExcel->disconnectWorksheets();
                    unset($objPHPExcel);fclose($fp);
                break;
                case 'xlsx':
                case 'xls':
                $PHPReader = new PHPExcel_Reader_Excel2007();
                if(!$PHPReader->canRead($fileph,'r')){
                    $PHPReader = new PHPExcel_Reader_Excel5();
                    if(!$PHPReader->canRead($fileph)){
                     $this->error('请上传文件',U('Goods/index'));
                        exit;
                    }
                }
                $PHPExcel = $PHPReader->load($fileph);
                $currentSheet = $PHPExcel->getSheet(0);
                $allColumn = $currentSheet->getHighestColumn();
                $allRow = $currentSheet->getHighestRow();
                $i = 0;
                $baseRow = 1;
                for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
                    $data = array();
                    for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
                        $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();
                        $data[] = trim($val);
                        //echo iconv('utf-8','gb2312', $val)." ";
                    }
                    $this->exportxls($data,$baseRow,$i,$objPHPExcel,$good);
                   $i++;
                }
                $this->headerxls();
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
                $objPHPExcel->disconnectWorksheets();
                unset($objPHPExcel);
                break;
            }
        }else{
            $this->display();
        }
    }
public function exportxls($data,$baseRow,$i,$objPHPExcel,$good){
    $sql = "select `num`,`approve_status`,`title`,`price`,`detail_url` from `u_goods` where `item_bn` ='UQ".$data[0]."'";
    $re = $good->query($sql);
    $row = $baseRow + $i;
    $hz = $re[0]['num']*$re[0]['price'];
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$row, '"'.$data[0].'"'."\t")
        ->setCellValue('B'.$row, $re[0]['approve_status'])
        ->setCellValue('C'.$row, $re[0]['title'])
        ->setCellValue('D'.$row, $re[0]['detail_url'])
        ->setCellValue('E'.$row, $re[0]['num'])
        ->setCellValue('F'.$row, $re[0]['price'])
        ->setCellValue('G'.$row, $hz);
}
public function headerxls(){
    $filename = "goods_num_".date('Y-m-d H:i',time()).'.xlsx';
    header ( 'Content-Disposition: attachment;filename="'.$filename);
    header ( 'Content-Type: application/vnd.ms-excel' );
    header ( 'Cache-Control: max-age=0' );
}
public function AddFile(){
    $file = $_FILES['myfile'];$good = M('Goods');
    $ext = pathinfo($file['name'],PATHINFO_EXTENSION);
    $fp = fopen($file['tmp_name'],'r');
    $this->inittaobao();
    switch($ext){
        case 'csv':
           $i = 0;
           while($data = fgetcsv($fp)){
                if($i>0){
                    $this->upu($data,$good);
                }
               $i++;
           }
           $rarr['msg'] = '更新成功';
           $rarr['k'] = 1;
        break;
        case 'xlsx':
        case 'xls':
        Vendor ( 'Excel.Classes.PHPExcel' );
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($file['tmp_name'],'r')){
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($file['tmp_name'])){
                $rarr['msg'] = '请上传文件';
                $rarr['k'] = 0;
            }
        }
        $PHPExcel = $PHPReader->load($file['tmp_name']);
        $currentSheet = $PHPExcel->getSheet(0);
        $allColumn = $currentSheet->getHighestColumn();
        $allRow = $currentSheet->getHighestRow();
        for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
            $data = array();
            for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
                $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();
                $data[] = trim($val);
            }
            $this->upu($data,$good);
        }
        $rarr['msg'] = '更新成功';
        $rarr['k'] = 1;
        break;
    }
    fclose($fp);
    echo json_encode($rarr);
}
public function inittaobao(){
    Vendor('Taobao/TopSdk');
    $this->client = new TopClient;
    $this->client->format = 'json';
    $this->client->appkey = $this->appkey;
    $this->client->secretKey = $this->secretKey;
    $this->products = new ItemGetRequest;//获取商品详细信息
    $this->products->setFields('num,approve_status');
}
public function upu($data,$good){
    $data[0] = trim($data[0]);
    $sql = "select `num_iid` from `u_goods` where `item_bn` ='UQ".$data[0]."'";
    $re = $good->query($sql);
    if(!empty($re[0]['num_iid'])){
        $this->products->setNumIid($re[0]['num_iid']);
        $pro = $this->client->execute($this->products, $this->token);
        $product_arr = (array)$pro->item;
        unset($sql);
        $good->where(array('num_iid'=>$re[0]['num_iid']))->save(array('approve_status'=>$product_arr['approve_status'],'num'=>$product_arr['num']));
    }
}
public function SynNum(){
        import("ORG.Net.UploadFile");
        $good = M('Goods');
        $upload = new UploadFile();
        $upload->maxSize = 512000;
        $upload->allowExts = array('csv','xlsx','xls');
        $upload->savePath = 'Upload/goodnum/';
        $upload->autoSub = true;
        $upload->subType = 'date';
        if(!$upload->upload()) {// 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
        }
        $ext = pathinfo($info[0]['name'], PATHINFO_EXTENSION);
        $fileph = $this->dirroot.'/'.$info[0]['savepath'].$info[0]['savename'];
        M('NumRecord')->add(array('url'=>$info[0]['savepath'].$info[0]['savename'],'createtime'=>date('Y-m-d H:i:s')));
        $this->inittaobao();
        switch($ext){
            case 'csv':
               $fp = fopen($fileph,'r');
                $i = 0;
                while($data = fgetcsv($fp)){
                    if($i>0){
                        $this->upu($data,$good);
                    }
                    $i++;
                }
                $rarr['msg'] = '更新成功';
                $rarr['k'] = 1;
               fclose($fp);
            break;
            case 'xlsx':
            case 'xls':
            Vendor ( 'Excel.Classes.PHPExcel' );
            $PHPReader = new PHPExcel_Reader_Excel2007();
            if(!$PHPReader->canRead($fileph,'r')){
                $PHPReader = new PHPExcel_Reader_Excel5();
                if(!$PHPReader->canRead($fileph)){
                    $this->error('请上传文件',U('Goods/index'));
                    exit;
                }
            }
            $PHPExcel = $PHPReader->load($fileph);
            $currentSheet = $PHPExcel->getSheet(0);
            $allColumn = $currentSheet->getHighestColumn();
            $allRow = $currentSheet->getHighestRow();
            for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
                $data = array();
                for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
                    $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();
                    $data[] = trim($val);
                }
                $this->upu($data,$good);
            }
            break;
        }
        $this->success('库存同步成功',U('Goods/index'));
}
public function DownNum(){
        $result = M('NumRecord')->field('url')->order('id desc')->limit('0,1')->find();
        if(empty($result)){
            $this->error('没有提交商品数据',U('Goods/index'));
            exit;
        }
        $result['url'] = trim($result['url']);
        $ext = pathinfo($result['url'],PATHINFO_EXTENSION);
        $fileph = $this->dirroot.'/'.$result['url'];
        $good = M('Goods');
        Vendor ( 'Excel.Classes.PHPExcel' );
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d',time()));
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', '货号')
        ->setCellValue('B1', '状态')
        ->setCellValue('C1', '标题')
        ->setCellValue('D1', '商品链接')
        ->setCellValue('E1', '在线库存')
        ->setCellValue('F1', '一口价')
        ->setCellValue('G1', '货值');
        $baseRow = 1;
        switch($ext){
            case 'csv':
            $fp = fopen($fileph,'r');
            $i = 0;
            while($data = fgetcsv($fp)){
                if($i>0){
                    $this->exportxls($data,$baseRow,$i,$objPHPExcel,$good);
                }
                $i++;
            }
            $this->headerxls();
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);fclose($fp);
            break;
            case 'xlsx':
            case 'xls':
            $PHPReader = new PHPExcel_Reader_Excel2007();
            if(!$PHPReader->canRead($fileph,'r')){
                $PHPReader = new PHPExcel_Reader_Excel5();
                if(!$PHPReader->canRead($fileph)){
                    $this->error('请上传文件',U('Goods/index'));
                    exit;
                }
            }
            $PHPExcel = $PHPReader->load($fileph);
            $currentSheet = $PHPExcel->getSheet(0);
            $allColumn = $currentSheet->getHighestColumn();
            $allRow = $currentSheet->getHighestRow();
            $i = 0;
            $baseRow = 1;
            for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
                $data = array();
                for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
                    $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();
                    $data[] = trim($val);
                    //echo iconv('utf-8','gb2312', $val)." ";
                }
                $this->exportxls($data,$baseRow,$i,$objPHPExcel,$good);
                $i++;
            }
            $this->headerxls();
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);
            break;
        }
}
}