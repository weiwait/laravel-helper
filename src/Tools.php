<?php

namespace Weiwait\Helper\Tools;


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

        $content = implode($contentArray->toArray(), "\n");

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
}
