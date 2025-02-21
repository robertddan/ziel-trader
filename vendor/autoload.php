<?php

class Autoload {

    public static $sJsonName = "ziel.json";
    public static $aDirectories = array();
    public static $aClasses = array();
    public static $aComposer = array();
/*
const DS = DIRECTORY_SEPARATOR;
const ROOT = __DIR__ . DS . '..' . DS;
const CONFIG = ROOT . DS . 'config' . DS;

define('DS', DIRECTORY_SEPARATOR);
chdir(__DIR__ . DS . '..' . DS);
define('ROOT', getcwd() . DS);
define('CONFIG', ROOT . 'config' . DS);
define('VENDOR', ROOT . 'vendor' . DS);
*/
    
    public static $i = 0;
    
    public static function autoload_custom()
    {
        foreach(self::$aClasses as $sClass)
        {
            
            if(!require($sClass['path'])) throw_exception('autoload_custom()');
            #print '<pre>';
            #set_include_path(ROOT . 'draft');
            #var_dump($sClass);
            #spl_autoload($sClass['path']);
            #return true;
            #var_dump([
                #class_exists($sClass['namespace']),
                #file_exists($sClass['path'])
            #]);
        }
        
        return true;
        #spl_autoload($sClass[0]);
        #spl_autoload_register($sClass[0]);
        #if(!require($sClass)) throw_exception('autoload_custom()');
        #print '<pre>';
    } 

    public static function autoload_json()
    {
        #var_dump(CONFIG);
        $sComposer = file_get_contents(CONFIG . self::$sJsonName);
        self::$aComposer = json_decode($sComposer, true);
        
        #for json require
        #$aComposer['autoload']['psr-4']
        #if (!isset())
        /*
        echo '<pre>';
        var_dump([
            self::$aDirectories
        ]);
        */
        
        foreach(self::$aComposer['autoload']['psr-4'] as $sNamespace => $aDirectory)
        {
            if (!is_array($aDirectory)) 
            $aDirectory = (array) $aDirectory;
            foreach($aDirectory as $sDirectory) 
            {
                if (!is_dir(ROOT . trim($sDirectory))) continue;
                self::$aDirectories[] = array('namespace' => $sNamespace, 
                    'directory' => $sDirectory, 
                    'path' => ROOT. trim($sDirectory) .DS);
            }
        }
        return true;
    }
    
    public static function autoload_namespaces($aaVendors = array())
    {
        if (empty($aaVendors)) 
        {
            $aaVendors = self::$aDirectories;
            if (empty($aaVendors)) return true;
        }
        foreach($aaVendors as $aVendor)
        {
            $aPaths = array_diff(scandir($aVendor['path']), array('.', '..', 'autoload.php'));
            
            #krsort($aPaths);
            
            foreach ($aPaths as $sPath) {
                if(is_dir($aVendor['path'] . $sPath)) {
                    self::$i++;
                    $aaVendor = array(array(
                        'namespace' => $aVendor['namespace'], 
                        'directory' => $aVendor['directory'], 
                        'path' => $aVendor['path'] .$sPath. DS
                    ));
                    self::autoload_namespaces($aaVendor);
                }
                else {
                    $aPathinfo = pathinfo($aVendor['path'] . $sPath);
                    if (isset($aPathinfo['extension']))
                    if (!in_array($aPathinfo['extension'], array('php'))) continue;
                    
                    $sVendorPath = $aVendor['path'] .$sPath;
                    $a075306 = explode($aVendor['directory'], $sVendorPath);
                    $a363174 = explode('.', array_pop($a075306));
                    $sNameSpace = array_shift($a363174);
                    $sNameSpace = @explode('/', $sNameSpace);
                    $aPath = explode('.', $sPath);
                    $aClass = array(
                        'namespace' => $aVendor['namespace'].implode("\\", array_map('ucfirst', array_filter($sNameSpace))), 
                        'class' => ucfirst(array_shift($aPath)), 
                        'path' => $sVendorPath
                    );
                    if(!array_push(self::$aClasses, $aClass)) continue;
                }
            }
        }
        return true;
    }
}

if (!Autoload::autoload_json()) throw_exception('autoload_json()');
if (!Autoload::autoload_namespaces()) throw_exception('autoload_namespaces()');
if (!Autoload::autoload_custom()) throw_exception('autoload_custom()');

?>