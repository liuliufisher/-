<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/tool/PHPExcel/IOFactory.php';
/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */
class Export_lib {
    private $CI;
    public function __construct()
    {
        $this->CI  = &get_instance();
    }

    /**
     * 导出excel
     * @param array $data
     * @param array $fields_arr 字段数组 array('name' => '名字')
     */
    public function export($data, $fields_arr)
    {
        $filename = "user_list_" . date('YmdHis') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel;");
        $res = array();

        $res[] = join("\t", array_values($fields_arr)) . "\r\n";;
        $keys = array_keys($fields_arr);
        foreach ($data as $d_val) {
            $_row = array();
            foreach ($keys as $k_val) {
                $_row[] = $d_val[$k_val];
            }
            array_walk($_row, array($this, 'escape_special_char'));
            $res[] .= implode("\t", array_values($_row)) . "\r\n";
        }
        echo(chr(255).chr(254));
        echo(mb_convert_encoding(join('', $res), 'UTF-16LE', 'UTF-8'));
        exit;
    }

    /**
     * 处理 excel 字符串中的 无用符号
     * @param string $str
     */
    private function escape_special_char(&$str){
        $str = preg_replace("/\t/", "\\t", $str);
        $str = str_replace("&nbsp;", " ", $str);
        $str = strip_tags($str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }

    /**
     * 导出html格式的excel
     * @param array $data
     * @param array $fields_arr 字段数组 array('name' => '名字')
     * @param string  $excel_name
     */
    public function export_html($data, $fields_arr, $excel_name= 'user_list'){
        $title = array_values($fields_arr);
        header('Content-Type: application/octet-stream');
        $filename = $excel_name . "_" . date('YmdHis') . ".xls";
        header("Content-type: application/vnd.ms-excel;");
        header("Content-disposition: attachment; filename="  . $filename);
        $out_put ='';
        $out_put .=<<<EOT
	<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
		<style id="Classeur1_16681_Styles"></style>
	</head>
	<body>
		<div id="Classeur1_16681" align=center x:publishsource="Excel">
			<table border=1 cellpadding=0 cellspacing=0 width=100%>
EOT;
        if (is_array($title)){
            $v_title = '<tr>';
            foreach ($title as $key => $value){
                $v_title .= '<td class=xl2216681 nowrap>'.$value.'</td>';
            }
            $v_title .= '</tr>';
            $out_put .= $v_title;
        }

        if (is_array($data)){
            $keys = array_keys($fields_arr);
            $v_data = '';
            foreach ($data as $d_val){
                $v_data .= '<tr>';
                foreach ($keys as $k_val){
                    if(is_int($d_val[$k_val])) $d_val[$k_val] = intval($d_val[$k_val]);
                    $v_data .= '<td class=xl2216681 nowrap>'.$d_val[$k_val].'</td>';
                }
                $v_data .= '</tr>';
            }
            $out_put .= $v_data;
        }
        $out_put .=<<<EOF
   </table>
		</div>
	</body>
</html>
EOF;
        echo $out_put;
    }


    /**
     * 读取excel
     * @param string $file_path
     * @param array $param
     * @return array|string
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function load_excel_by_python($file_path = '', $param = array()) {
        $res = array();
        set_time_limit(500);
        if(!is_file($file_path)) {
            return $res;
        }
        $file_type = myIsset('file_type', $param);
        if(empty($file_type)) {

            $file_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        }
        $allow_type_arr = array('xls', 'xlsx', 'csv');

        if(!in_array($file_type, $allow_type_arr)) {
            return $res;
        }
        $py_load_excel_script = get_self_config('py_load_excel_path');
        ob_start();
        system("{$py_load_excel_script} {$file_path}");
        $parse_json_str = ob_get_clean();

        if(empty($parse_json_str)){
            ///$res = $this->load_excel($file_path, $param);
        }else{
            $res = $this->parse_json_data($parse_json_str, false);
        }

        return $res;
    }

    /**
     * 读取excel
     * @param string $file_path
     * @param array $param
     * @return array|string
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function load_excel($file_path = '', $param = array()) {
        $res = [];
        set_time_limit(100);
        if(!is_file($file_path)) {
            return $res;
        }

        $file_type = myIsset('file_type', $param);
        if(empty($file_type)) {

            $file_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        }
        $allow_type_arr = array('xls', 'xlsx', 'csv');

        if(!in_array($file_type, $allow_type_arr)) {
            return $res;
        }
        $skip_num = myIsset('skip_num', $param, 0);

        //html生成的excel读取会报警告
        libxml_use_internal_errors(true);

        if($file_type == 'xlsx'|| $file_type == 'xls'){
            $objPHPExcel = PHPExcel_IOFactory::load($file_path);
        }else if($file_type == 'csv'){
            $objReader = PHPExcel_IOFactory::createReader('CSV')
                ->setDelimiter(',')
                ->setInputEncoding('GBK')
                ->setEnclosure('"')
                ->setLineEnding("\r\n")
                ->setSheetIndex(0);
            $objPHPExcel = $objReader->load($file_path);

        }

        $sheet = $objPHPExcel->getSheet(0);

        $highestRowNum = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnNum = PHPExcel_Cell::columnIndexFromString($highestColumn);

        $filed = array();

        //特殊处理html类型的excel
        $file_title_num = $skip_num + 1 + intval($this->is_html_excel($file_path));
        for($i = 0; $i < $highestColumnNum; $i++){
            $cellName = PHPExcel_Cell::stringFromColumnIndex($i) . $file_title_num;
            $cellVal = $sheet->getCell($cellName)->getValue();
            if($cellVal instanceof PHPExcel_RichText) {
                $cellVal = $cellVal->getPlainText();
            }
            if(is_null($cellVal)) {
                continue;
            }
            //过滤title中的空格
            $filed []= preg_replace('#\s+#', '', $cellVal);
        }
        $has_title_num = count($filed);
        for($i = $file_title_num + 1; $i <= $highestRowNum; $i++){
            $row = array();
            for($j = 0; $j < $has_title_num; $j++){
                $cellName = PHPExcel_Cell::stringFromColumnIndex($j) . $i;
                $cellVal = $sheet->getCell($cellName)->getValue();
                $row[ $filed[$j] ] = $cellVal;
            }
            $res []= $row;
        }
        
        return $res;
    }

    /**
     * 判断excel是否是html版的
     * @param string $path
     * @return bool
     */
    public function is_html_excel($path)
    {
        $res = false;
        if(is_file($path)) {
            $data = substr(file_get_contents($path), 0, 2048);
            if (preg_match('#html#', $data)) {
                $res = true;
            }
        }
        return $res;
    }

    /**
     * 读取json数组
     * @param string $json_str
     * @param bool $is_file 是否为文件
     * @return array
     */
    public function parse_json_data($json_str, $is_file = true)
    {
        $res = [];
        $data_str = $json_str;
        if($is_file && is_file($json_str)){
            $data_str = file_get_contents($json_str);
        }
        $data_str = trim($data_str);
        $tmp_arr = json_decode($data_str, true, 512, JSON_BIGINT_AS_STRING);
        if(empty($tmp_arr)){
            return $res;
        }

        $title_arr = array_shift($tmp_arr);

        foreach ($tmp_arr as $t_key => $t_val){
            $row = [];
            foreach ($t_val as $_t_key => $_t_val) {
                $row[$title_arr[$_t_key]] = $_t_val;
            }
            $res[] = $row;
        }

        return $res;
    }

    /**
     * 导出html格式的excel
     * @param array $data  数组 键值从 0 开始的数组
     * @param array $fields_arr 字段数组 array('name' => '名字')
     * @param string  $excel_name
     */
    public function export_html_sort_data($data, $fields_arr, $excel_name= 'user_list'){
        $title = array_values($fields_arr);
        header('Content-Type: application/octet-stream');
        $filename = $excel_name . "_" . date('YmdHis') . ".xls";
        header("Content-type: application/vnd.ms-excel;");
        header("Content-disposition: attachment; filename="  . $filename);
        $out_put ='';
        $out_put .=<<<EOT
	<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
		<style id="Classeur1_16681_Styles"></style>
	</head>
	<body>
		<div id="Classeur1_16681" align=center x:publishsource="Excel">
			<table border=1 cellpadding=0 cellspacing=0 width=100%>
EOT;
        if (is_array($title)){
            $v_title = '<tr>';
            foreach ($title as $key => $value){
                $v_title .= '<td class=xl2216681 nowrap>'.$value.'</td>';
            }
            $v_title .= '</tr>';
            $out_put .= $v_title;
        }

        if (is_array($data)){
            $keys = array_keys($fields_arr);
            $v_data = '';
            $count = count($data);
            for($i = 0; $i < $count; $i++){
                $d_val  = $data[$i];
                $v_data .= '<tr>';
                foreach ($keys as $k_val){
                    if(is_int($d_val[$k_val])) $d_val[$k_val] = intval($d_val[$k_val]);
                    $v_data .= '<td class=xl2216681 nowrap>'.$d_val[$k_val].'</td>';
                }
                $v_data .= '</tr>';
                unset($data[$i]);
            }
            $out_put .= $v_data;
        }
        $out_put .=<<<EOF
   </table>
		</div>
	</body>
</html>
EOF;
        echo $out_put;
    }
}