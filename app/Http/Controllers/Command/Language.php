<?php

namespace App\Http\Controllers\Command;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Language extends Controller
{
    public function index($that, $module)
    {
        $timer_start = microtime(true) * 10000;
        $that->info('Start');

        $header = '<?php'.PHP_EOL.'return'.PHP_EOL.'['.PHP_EOL;
        $footer = '];'.PHP_EOL.'?>';

        $eng_content = '';
        $chinese_content = '';
        $malay_content = '';

        $data = file_get_contents(resource_path('language_'.$module.'.txt'));
        $lines = explode("\n", $data);
        $row_count = 0;

        foreach($lines as $line)
        {
            // skip the first and last row
            $row_count++;
            if($row_count == 1) continue;
            if($row_count == count($lines)) continue;

            // read each column
            $columns = explode('	', $line);
            $parameter = $columns[0];
            $english = $columns[1];
            $chinese = $columns[2];
            $malay = $columns[3];

            //trim
            $parameter = trim($parameter);
            $english = trim($english);
            $chinese = trim($chinese);
            $malay = trim($malay);

            //variable
            $english = str_replace('XXXX', ':var', $english);
            $chinese = str_replace('XXXX', ':var', $chinese);
            $malay = str_replace('XXXX', ':var', $malay);

            //single quotes
            $english = str_replace("'", "\'", $english);
            $chinese = str_replace("'", "\'", $chinese);
            $malay = str_replace("'", "\'", $malay);

            if(strlen($english) > 0)
            {
                $eng_content .= "'$parameter' => '$english',";
                $eng_content .= PHP_EOL;
            }

            if(strlen($chinese) > 0)
            {
                $chinese_content .= "'$parameter' => '$chinese',";
                $chinese_content .= PHP_EOL;
            }

            if(strlen($malay) > 0)
            {
                $malay_content .= "'$parameter' => '$malay',";
                $malay_content .= PHP_EOL;
            }
        }

        $eng_translation = $header.$eng_content.$footer;
        $chinese_translation = $header.$chinese_content.$footer;
        $malay_translation = $header.$malay_content.$footer;

        $filepath = resource_path('lang/en/'.$module.'.php');
        $file = fopen($filepath, 'w');
        fwrite($file, $eng_translation);

        $filepath = resource_path('lang/cn/'.$module.'.php');
        $file = fopen($filepath, 'w');
        fwrite($file, $chinese_translation);

        $filepath = resource_path('lang/ml/'.$module.'.php');
        $file = fopen($filepath, 'w');
        fwrite($file, $malay_translation);

        $that->info(round(((microtime(true) * 10000) - $timer_start) / 10000, 4).' secs');
        $that->info('Done');
    }
}
