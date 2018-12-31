<?php
/**
 * excel导出导入
 */
class excel {

    public function __construct() {
        $this->excel = pc_base::load_sys_class('PHPExcel','',1);
    }

    /**
     * array $header 导出的excel头
     * array $data 数据,暂最多支持26列格式:[[],[],[]]
     * string $filename 导出的文件名
     * string $sheetname 标题名
     */
    public function export($header, $data, $filename='', $sheetname='sheet') {
        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $length = count($header);
        if ($length > count($letter) || $length == 0) {
            return false;
        }
        $filename = iconv('UTF-8', 'GB2312', $filename);
        $this->excel->setActiveSheetIndex(0);
        $sheetobj = $this->excel->getActiveSheet();
        if (!$resume) {
            $sheetobj->setTitle($sheetname);
            for ($i=0; $i < $length; $i++) {
                $this->excel->getActiveSheet()->setCellValue("$letter[$i]1", $header[$i]);
                $this->excel->getActiveSheet()->getStyle("$letter[$i]")->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $this->excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->setName('微软雅黑');
                $this->excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle("$letter[$i]1")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle($letter[$i])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle($letter[$i])->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getColumnDimension("$letter[$i]")->setWidth(20);
            }
        }
        foreach ($data as $key=>$line) {
            $num = $key + 2;
            foreach ($line as $k=>$v) {
                $this->excel->getActiveSheet()->setCellValue("$letter[$k]$num",$v);
            }
        }
        if (!$filename) {
            $filename = time();
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function import($filename, $ext) {
        if (!in_array($ext, ['xls','xlsx'])) {
            return false;
        }
        if ($ext == 'xls') {
            $objreader = PHPExcel_IOFactory::createReader('Excel5');
        } elseif ($ext == 'xlsx') {
            $objreader = PHPExcel_IOFactory::createReader('Excel2007');
        }
        $objfile = $objreader->load($filename);
        $currentSheet = $objfile->getSheet(0);
        $allColumn = $currentSheet->getHighestColumn();
        $allRow = $currentSheet->getHighestRow();
        $data = [];
        for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                $address = $currentColumn . $currentRow;
                $data[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
            }
        }
        @unlink ($filename);
        return $data;
    }



}
