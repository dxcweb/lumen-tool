<?php
/**
 * Created by PhpStorm.
 * User: guowei
 * Date: 17/5/9
 * Time: 下午11:49
 */

namespace LumenTool;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use \PhpOffice\PhpSpreadsheet\Cell\DataType;

class Excel
{
    private function demo()
    {
        $data = [
            ['order_id' => "1", "phone" => "2"],
            ['order_id' => "1", "phone" => "2"],
        ];
        $columns = [
            ['title' => '手机号', 'key' => 'phone'],
            ['title' => '订单编号', 'key' => 'order_id'],
        ];
        Excel::export('订单', $data, $columns);
    }

    public static function import($startRow = 1, $pFilename = null, $InputEncoding)
    {
        Cell::setValueBinder(new CustomValueBinder());
        if (empty($pFilename)) {
            foreach ($_FILES as $value) {
                $pFilename = $value['tmp_name'];
                break;
            }
        }
        if (empty($pFilename)) {
            return _output('pFilename参数错误！', false);
        }
        try {
            $inputFileType = IOFactory::identify($pFilename);
            $excelReader = IOFactory::createReader($inputFileType);
            if (!empty($InputEncoding)) {
                $excelReader->setInputEncoding($InputEncoding);
//                $excelReader->setInputEncoding('GBK');
            }
            $spreadsheet = $excelReader->load($pFilename);

        } catch (\Exception $e) {
            return _output('文件格式不正确，请打手动打开后点击"文件->另存为"存储一个新的Excel后上传！', false);
        }


        $sheet = $spreadsheet->getActiveSheet();
//        $sheetdata = $sheet->toArray(); // 没有尝试到
        $res = array();
        foreach ($sheet->getRowIterator($startRow) as $row) {
            $tmp = array();
            foreach ($row->getCellIterator() as $cell) {
                $tmp[] = $cell->getFormattedValue();
            }
            $res[$row->getRowIndex()] = $tmp;
        }
        return _output($res);
    }

    public static function export($file_name, $excel_data, $columns, $excel_type = 'xls')
    {

        $spreadSheet = new Spreadsheet();
        $workSheet = $spreadSheet->getActiveSheet();

        list($startColumn, $startRow) = Coordinate::coordinateFromString('A1');

        $currentColumn = $startColumn;
        foreach ($columns as $val) {
            $workSheet->getColumnDimension($currentColumn)->setAutoSize(true);
            $workSheet->setCellValueExplicit($currentColumn . $startRow, $val['title'], DataType::TYPE_STRING);
            ++$currentColumn;
        }
        ++$startRow;

        foreach ($excel_data as $rowData) {
            $currentColumn = $startColumn;
            foreach ($columns as $val) {
                $key = $val['key'];
                if (!empty($rowData[$key])) {
                    $workSheet->setCellValueExplicit($currentColumn . $startRow, $rowData[$key], DataType::TYPE_STRING);
                }
                ++$currentColumn;
            }
            ++$startRow;
        }

//        $writer = new Xlsx($spreadSheet);
//        $writer->save('hello world.xlsx');
        $file_name = $file_name . ".xlsx";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadSheet, 'Xlsx');
        // 注意createWriter($spreadsheet, 'Xls') 第二个参数首字母必须大写
        $writer->save('php://output');
    }
}