<?php

namespace Weiwait\Helper;


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Tools
{
    public static function alterEnv(array $data): void
    {
        $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';

        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));

        $contentArray->transform(function ($item) use (&$data){
            foreach ($data as $key => $value){
                if(Str::contains($item, $key)){
                    unset($data[$key]);
                    return $key . '=' . $value;
                }
            }

            return $item;
        });

        $content = implode("\n", $contentArray->toArray());

        $content .= "\n";
        if ($data) {
            $content .= "\n\n";

            foreach ($data as $key => $value) {
                $content .= $key . '=' . $value . "\n";
            }
        }

        File::put($envPath, $content);

        if (file_exists(app()->getCachedConfigPath())) {
            Artisan::call('config:cache');
        }
    }


    public static function imagesUrl($images): array
    {
        if (!$images) {
            return [];
        }
        $images = collect($images)->map(function ($image) {
            return Storage::disk()->url($image);
        });
        return $images->toArray();
    }

    /**
     * 将数字索引转换为Excel字母索引
     *
     * @param $num
     * @return string
     */
    public static function numToExcelChr(int $num): string
    {
        $front = '';
        $floor = floor($num / 26);

        if ($floor > 0) {
            $front = self::numToExcelChr($floor - 1);
            $num = $num % 26;
        }

        $last = chr(65 + $num);

        return $front . $last;
    }

    /**
     * 将字母索引转换为数字索引
     *
     * @param string $chr
     * @return int
     */
    public static function excelChrToNum(string $chr): int
    {
        $chr = array_reverse(str_split($chr));

        $num = 0;
        foreach ($chr as $key => $item) {

            if ($key > 0) {
                $ord = ord($item) - 64;
                $pos = pow(26, $key);
                $num += $ord * $pos;
            } else {
                $num += ord($item) - 65;
            }
        }

        return $num;
    }
}
