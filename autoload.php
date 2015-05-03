<?php
/**
 *  オートロード
 */
spl_autoload_register(function($className){
    if (strpos($className, 'Ethnam\\Generator\\') === 0) {
        $separated = explode('\\', $className);
        array_shift($separated);  // remove 'Ethnam'
        array_shift($separated);  // remove 'Generator'

        $file = __DIR__ . '/src/' . join('/', $separated) . '.php';

	//just for debug
        if (!file_exists($file)) {
            throw new Exception("classs not found: " . $className);
        }

        require_once $file;
    }
});
