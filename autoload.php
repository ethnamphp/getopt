<?php
/**
 *  オートロード
 */
spl_autoload_register(function($className){
    //単純に_区切りをディレクトリ区切りにマッピングする
    if (strpos($className, 'Ethnam\\Generator\\') === 0) {
        $separated = explode('\\', $className);
        //var_dump($separated);exit;
        array_shift($separated);  // remove 'Ethnam'
        array_shift($separated);  // remove 'Generator'

        $file = __DIR__ . '/src/' . join('/', $separated) . '.php';
        if (!file_exists($file)) {
            //just for debug
            throw new Exception("classs not found: " . $className);
        }
        require_once $file;
    }
});
