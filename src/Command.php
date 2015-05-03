<?php
/**
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 */

namespace Ethnam\Generator;

/**
 *  Ethna Command
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 */
use \Ethna_Controller;
use \Ethna_Util;
use \Ethna;
use \Base;

class Command
{
    private $version = <<<EOD
Ethnam %s (using PHP %s)
Copyright (c) 2004-%s, @DQNEO and Ethna commiters

EOD;

    /**
     * コマンドを実行する
     */
    public function run()
    {

        // fetch arguments
        $opt = new Getopt();
        $arg_list = $opt->readPHPArgv();
        if (Ethna::isError($arg_list)) {
            echo $arg_list->getMessage()."\n";
            exit(2);
        }
        array_shift($arg_list);  // remove "command.php"

        //  はじめの引数に - が含まれていればそれを分離する
        //  含まれていた場合、それは -v|--version でなければならない
        list($my_arg_list, $arg_list) = $this->separateArgList($arg_list);
        $r = $opt->getopt($my_arg_list, "v", array("version"));
        if (Ethna::isError($r)) {
            $subCommand = 'help';
        } else {
            // ad-hoc:(
            foreach ($r[0] as $opt) {
                if ($opt[0] == "v" || $opt[0] == "--version") {
                    printf($this->version, ETHNA_VERSION, PHP_VERSION, date('Y'));
                    exit(2);
                }
            }
        }

        if (count($arg_list) == 0) {
            $subCommand = 'help';
        } else {
            $subCommand = array_shift($arg_list);
        }

        $subCommandObj = self::newSubcommand($subCommand);

        // don't know what will happen:)
        $subCommandObj->setArgList($arg_list);
        $r = $subCommandObj->perform();
        if (Ethna::isError($r)) {
            echo $r->getMessage();
            exit(1);
        }
    }

    /**
     *  get handler object
     *
     *  @access public
     */
    public static function newSubcommand($subCommand)
    {
        $name = preg_replace_callback('/\-(.)/', function ($matches) {
                return strtoupper($matches[1]);
                    }, ucfirst($subCommand));

        $ctl = new Ethna_Controller(GATEWAY_CLI);
        Ethna::clearErrorCallback();

        $class = '\\Ethnam\\Generator\\Subcommand\\' . $name;
        $obj = new $class($ctl, null, $name);
        return $obj;
    }
    // }}}


    /**
     *  sort callback method
     */
    public static function _handler_sort_callback(Base $a, Base $b)
    {
        return strcmp($a->getId(), $b->getId());
    }
    // }}}

    /**
     *  Ethna_Controllerのインスタンスを取得する
     *  (Ethna_Commandの文脈で呼び出されることが前提)
     *
     *  @access public
     *  @static
     */
    public static function getEthnaController()
    {
        return Ethna_Controller::getInstance();
    }
    // }}}

    /**
     *  アプリケーションのコントローラファイル/クラスを検索する
     *
     *  @access public
     *  @static
     */
    public static function getAppController($app_dir = null)
    {
        static $app_controller = array();

        if (isset($app_controller[$app_dir])) {
            return $app_controller[$app_dir];
        } elseif ($app_dir === null) {
            throw new \Exception('$app_dir not specified.');
        }

        $ini_file = null;
        while (is_dir($app_dir)) {
            if (is_file("$app_dir/.ethna")) {
                $ini_file = "$app_dir/.ethna";
                break;
            }
            $app_dir = dirname($app_dir);
            if (Ethna_Util::isRootDir($app_dir)) {
                break;
            }
        }

        if ($ini_file === null) {
            throw new \Exception('no .ethna file found');
        }

        $macro = parse_ini_file($ini_file);
        if (isset($macro['controller_file']) == false
            || isset($macro['controller_class']) == false) {
            throw new \Exception('invalid .ethna file');
        }
        $file = $macro['controller_file'];
        $class = $macro['controller_class'];

        $controller_file = "$app_dir/$file";
        if (is_file($controller_file) == false) {
            throw new \Exception("no such file $controller_file");
        }

        include_once $controller_file;
        if (class_exists($class) == false) {
            throw new \Exception("no such class $class");
        }

        $global_controller = $GLOBALS['_Ethna_controller'];
        $app_controller[$app_dir] = new $class(GATEWAY_CLI);
        $GLOBALS['_Ethna_controller'] = $global_controller;
        Ethna::clearErrorCallback();

        return $app_controller[$app_dir];
    }
    // }}}

    /**
     *  Ethna 本体の設定を取得する (ethnaコマンド用)
     *
     *  @param  $section    ini ファイルの section
     *  @access public
     */
    public static function getMasterSetting($section = null)
    {
        static $setting = null;
        if ($setting === null) {
            $ini_file = ETHNA_BASE . "/.ethna";
            if (is_file($ini_file) && is_readable($ini_file)) {
                $setting = parse_ini_file($ini_file, true);
            } else {
                $setting = array();
            }
        }

        if ($section === null) {
            return $setting;
        } elseif (array_key_exists($section, $setting)) {
            return $setting[$section];
        } else {
            $array = array();
            return $array;
        }
    }
    // }}}

    public function separateArgList($arg_list)
    {
        $my_arg_list = array();

        //  はじめの引数に - が含まれていたら、
        //  それを $my_arg_list に入れる
        //  これは --version 判定のため
        for ($i = 0; $i < count($arg_list); $i++) {
            if ($arg_list[$i]{0} == '-') {
                // assume this should be an option for myself
                $my_arg_list[] = $arg_list[$i];
            } else {
                break;
            }
        }
        $arg_list = array_slice($arg_list, $i);

        return array($my_arg_list, $arg_list);
    }

    public static function getSkelDir()
    {
        return dirname(__DIR__) . '/skel';
    }
}
// }}}
