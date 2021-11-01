<?php

namespace App;

use ZipArchive;

class Updater {

    public const UPDATE_FOLDER = DOCUMENT_ROOT.'/update';

    public const UPDATE_ZIP = self::UPDATE_FOLDER.'/master.zip';

    public static function download($url)
    {
        $http = new Http();

        $credentials = base64_encode('Guerinoob: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');

        $response = $http->request($url, [
            'method' => 'GET',
            'headers' => 'Authorization: Basic '.$credentials
        ]);

        if($response->getStatusCode() == 200 || $response->getStatusCode() == 302) {
            if(file_exists(self::UPDATE_FOLDER) || mkdir(self::UPDATE_FOLDER)) {
                return file_put_contents(self::UPDATE_ZIP, $response->getContent()) !== false;
            }
        }

        return false;
    }

    public static function update($backup = false)
    {
        if(!self::download('https://github.com/Guerinoob/Kaori/archive/refs/heads/master.zip'))
            return false;

        $zip = new ZipArchive();
        $zip->open(self::UPDATE_ZIP);

        if(!$zip->extractTo(self::UPDATE_FOLDER))
            return false;

        if($backup) {
            $backup = new ZipArchive();
            $path = DOCUMENT_ROOT.'/backup/'.SITENAME.'-'.date('Y-m-d_H-i-s').'.zip';
            touch($path);
            $backup->open($path, ZipArchive::CREATE);

            self::backup($backup, DOCUMENT_ROOT, '');
            $backup->close();
        }

        $path = self::UPDATE_FOLDER.'/Kaori-master';

        self::rcopy($path, DOCUMENT_ROOT);

        //TODO : deleted files to delete
        
        $zip->close();
        self::removeFolder(self::UPDATE_FOLDER);
    }

    public static function removeFolder($path)
    {
        if(!is_dir($path))
            return false;

        $dir = opendir($path);

        while(($file = readdir($dir))) {
            if($file != '.' && $file != '..') {
                $file = $path.'/'.$file;
                if(is_dir($file)) {
                    self::removeFolder($file);
                }
                else {
                    unlink($file);
                }
            }
        }

        closedir($dir);

        rmdir($path);
    }

    public static function backup($zip, $path, $current)
    {
        if(!is_dir($path))
            return false;

        if(($dir = opendir($path))) {
            while(($file = readdir($dir))) {
                if($file != '.' && $file != '..') {
                    if(is_dir($path.'/'.$file)) {
                        $zip->addEmptyDir($current.$file);
                        self::backup($zip, $path.'/'.$file, $current.$file.'/');
                    }
                    else {
                        $zip->addFile($path.'/'.$file, $current.$file);
                    }
                }
            }
        }

        closedir($dir);
        
    }

    public static function rcopy($source, $destination)
    {
        if(($dir = opendir($source))) {
            while(($file = readdir($dir))) {
                if($file != '.' && $file != '..') {
                    if(is_dir($source.'/'.$file)) {
                        if(!file_exists($destination.'/'.$file))
                            mkdir($destination.'/'.$file);
                        
                        self::rcopy($source.'/'.$file, $destination.'/'.$file);
                    }
                    else if($destination.'/'.$file != DOCUMENT_ROOT.'/README.md') {
                        copy($source.'/'.$file, $destination.'/'.$file);
                    }
                }
            }
        }
    }
}