<?php

namespace Admin\Service;

class ExcelService extends BaseService{
    
    private $cellNames;
    
    public function __construct(){
        
        parent::__construct();
        
        $this->cellNames == null;

        ini_set('max_execution_time', '0');
        Vendor('PHPExcel.PHPExcel');
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
        $cacheSettings = array();
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $this->phpexcel = new \PHPExcel();
        $this->phpexcel->getProperties()
        ->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
        $this->phpexcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    }
    
    public function setTitle($title){
        $dataArray = array($title);
        $this->phpexcel->getActiveSheet()->fromArray($dataArray,null,'A1');
        $this->phpexcel->getActiveSheet()->setTitle('Sheet1');
        $this->phpexcel->setActiveSheetIndex(0);
    }
    
    public function setCellNames($names){
        
        $this->cellNames = $names;
    }
    
    public function setCeils($data,$start){
        $start += 2;
        if($this->cellNames == null){
            $this->phpexcel->getActiveSheet()->fromArray($data,null,'A'.$start);
        }else{
            $row = sizeof($data);
            $col = sizeof($this->cellNames);
            for($i = 0; $i < $row; $i++){
                for($j = 0; $j < $col; $j++){
                    $this->phpexcel->getActiveSheet()->setCellValueByColumnAndRow($j, $start + $i, $data[$i][$this->cellNames[$j]]);
                }
            }
        }
    }
    
    public function downloadFile($filename){
        $filename=str_replace('.xls', '', $filename).'.xls';
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objwriter = \PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objwriter->save('php://output');
        exit;
    }
}

