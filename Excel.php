<?php
/**
 * Created by PhpStorm.
 * User: guowei
 * Date: 17/5/9
 * Time: 下午11:49
 */

namespace LumenTool;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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