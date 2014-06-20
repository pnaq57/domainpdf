<?php
namespace Ipmpdf\Service;

/**
 * Description of FileService
 *
 * @author aqnguyen
 */
class FileService
{
    const FILE_PATH = 'data/domains/';
    
    public static function getFileContent($fileName, $delimiter = ';')
    {
        $fileName = self::FILE_PATH . $fileName;
        $str = file_get_contents($fileName); 
        // -------- remove the utf-8 BOM ----
        $str = str_replace("\xEF\xBB\xBF",'',$str);
        file_put_contents($fileName, $str);
        if(!file_exists($fileName) || !is_readable($fileName)) {
            return FALSE;
        }        

        $header = NULL;
        $data = array();
        if (($handle = fopen($fileName, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 2000, $delimiter)) !== FALSE) {
                if(!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }
}
