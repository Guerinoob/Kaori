<?php
/**
 * Updater class
 */

namespace App;

use App\Http\Http;
use ZipArchive;

/**
 * This class is used to update the framework from the github repository (https://github.com/Guerinoob/Kaori)
 */
class Updater {

    /**
     * The path to the temporary folder that will contain the updated version
     */
    public const UPDATE_FOLDER = DOCUMENT_ROOT.'/update';

    /**
     * The name of the downloaded zip
     */
    public const UPDATE_ZIP = self::UPDATE_FOLDER.'/master.zip';
    
    /**
     * Performs a HTTP request and puts the content into the destination file
     *
     * @param  mixed $url The url of the resource to download
     * @param  mixed $destination The file destination
     * @param  string|false $credentials A string in the form of "username:password" to add a Authorization: Basic header to the request, or false if no authentication is needed
     * @return bool Returns true if the resource has been download, false otherwise
     */
    protected static function download($url, $destination, $credentials = false)
    {
        $http = new Http();

        $headers = '';

        if($credentials) {
            $credentials = base64_encode($credentials);
            $headers = 'Authorization: Basic '.$credentials;
        }

        $response = $http->request($url, [
            'method' => 'GET',
            'headers' => $headers
        ]);

        if($response->getStatusCode() == 200 || $response->getStatusCode() == 302) {
            if(file_exists(self::UPDATE_FOLDER) || mkdir(self::UPDATE_FOLDER)) {
                return file_put_contents($destination, $response->getContent()) !== false;
            }
        }

        return false;
    }
    
    /**
     * Performs the update
     *
     * @param  bool $backup Whether to backup the project in the /Backup folder or not
     * @return bool Returns true if Kaori has been updated, false otherwise
     */
    public static function update($backup = false)
    {
        if(!self::download('https://github.com/Guerinoob/Kaori/archive/refs/heads/master.zip', self::UPDATE_ZIP))
            return false;

        $zip = new ZipArchive();
        $zip->open(self::UPDATE_ZIP);

        if(!$zip->extractTo(self::UPDATE_FOLDER))
            return false;

        if($backup) {
            $backup = new ZipArchive();
            $path = DOCUMENT_ROOT.'/Backup/'.SITENAME.'-'.date('Y-m-d_H-i-s').'.zip';
            $backup->open($path, ZipArchive::CREATE);

            self::backup($backup, DOCUMENT_ROOT, '');
            $backup->close();
        }

        $path = self::UPDATE_FOLDER.'/Kaori-master';

        if(file_exists(DOCUMENT_ROOT.'/custom_defines.php')) //Delete the Installer Controller if Kaori is already installed
            unlink($path.'/Controllers/InstallerController.php');

        self::rcopy($path, DOCUMENT_ROOT);

        //TODO : deleted files to delete
        
        $zip->close();
        self::removeFolder(self::UPDATE_FOLDER);

        return true;
    }

    protected static function removeFolder($path)
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

    protected static function backup($zip, $path, $current)
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
                    else if((!str_starts_with($path.'/'.$file, DOCUMENT_ROOT.'/Backup/') || $file == '.gitignore'))  {
                        $zip->addFile($path.'/'.$file, $current.$file);
                    }
                }
            }
        }

        closedir($dir);
        
    }

    protected static function rcopy($source, $destination)
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