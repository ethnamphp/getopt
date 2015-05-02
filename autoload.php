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

        //読み込み失敗したら死んで欲しい(´・ω・`)
        require_once __DIR__ . '/src/' . join('/', $separated) . '.php';
    }
});

spl_autoload_register(function($className){
    //単純に_区切りをディレクトリ区切りにマッピングする
    if (
        strpos($className, 'Ethna_Plugin_Subcommand') === 0 ||
        strpos($className, 'Ethna_Plugin_Generator') === 0 ||
        strpos($className, 'Ethna_Generator') === 0
    ) {
        $separated = explode('_', $className);
        array_shift($separated);  // remove first element
        //読み込み失敗しても死ぬ必要はないのでrequireではなくincludeする
        //see http://qiita.com/Hiraku/items/72251c709503e554c280
        include_once __DIR__ . '/src/' . join('/', $separated) . '.php';
    }
});
