<?php
/**
 *  AppManager.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 */
namespace Ethnam\Generator\Generator;

use Ethna_Util;

/**
 *  スケルトン生成クラス
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 */
class AppManager extends Base
{
    /**
     *  アプリケーションマネージャのスケルトンを生成する
     *
     *  @access public
     *  @param  string  $manager_name    アプリケーションマネージ名
     *  @return bool    true:成功 false:失敗
     */
    public function generate($manager_name)
    {
        $class_name = $this->ctl->getManagerClassName($manager_name);
        $app_dir = $this->ctl->getDirectory('app');
        $app_path = "${class_name}.php";

        $macro = array();
        $macro['project_id'] = $this->ctl->getAppId();
        $macro['app_path'] = $app_path;
        $macro['app_manager'] = $class_name;

        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        $path = "$app_dir/$app_path";
        Ethna_Util::mkdir(dirname($path), 0755);
        if (file_exists($path)) {
            printf("file [%s] already exists -> skip\n", $path);
        } elseif ($this->_generateFile("skel.app_manager.php", $path, $macro) == false) {
            printf("[warning] file creation failed [%s]\n", $path);
        } else {
            printf("app-manager script(s) successfully created [%s]\n", $path);
        }
    }
}
