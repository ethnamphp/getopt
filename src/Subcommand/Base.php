<?php
/**
 *  Handle.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 */
namespace Ethnam\Generator\Subcommand;

use \Ethnam\Generator\Getopt;
use \Ethnam\Generator\Command as Ethna_Command;

/**
 *  コマンドラインハンドラプラグインの基底クラス
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 */
abstract class Base
{
    /** @protected    handler's id */
    protected $id;

    /** @protected    command line arguments */
    protected $arg_list;

    /** @protected    object  Ethna_Controller    Controller Object */
    public $controller;
    public $ctl; /* Alias */

    /** @protected    object  Ethna_Backend       Backend Object */
    public $backend;

    /** @protected    object  Ethna_Config        設定オブジェクト */
    public $config;

    /** @protected    object  Ethna_Logger        ログオブジェクト */
    public $logger;

    /**
     *
     *
     *  @access public
     */
    public function __construct($controller, $void, $name)
    {
        $this->controller = $controller;
        $this->ctl = $this->controller;

        $this->backend = $this->controller->getBackend();

        $this->logger = $controller->getLogger();
        $this->config = $controller->getConfig();

        $id = $name;
        $id = preg_replace_callback('/^([A-Z])/', function ($matches) {
                return strtolower($matches[1]);
            }, $id);
        $id = preg_replace_callback('/([A-Z])/', function ($matches) {
                return '-' . strtolower($matches[1]);
                    }, $id);
        $this->id = $id;
    }

    /**
     *  get handler-id
     *
     *  @access public
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *  get handler's description
     *
     *  @access public
     */
    public function getDescription()
    {
        return "description of " . $this->id;
    }

    /**
     *  get handler's usage
     *
     *  @access public
     */
    public function getUsage()
    {
        return "usage of " . $this->id;
    }

    /**
     *  set arguments
     *
     */
    public function setArgList($arg_list)
    {
        $this->arg_list = $arg_list;
    }

    /**
     * easy getopt :)
     *
     * @param   array   $lopts  long options
     * @return  array   list($opts, $args)
     * @access  protected
     */
    public function _getopt($lopts = array())
    {
        // create opts
        // ex: $lopts = array('foo', 'bar=');
        $lopts = to_array($lopts);
        $sopts = '';
        $opt_def = array();
        foreach ($lopts as $lopt) {
            if ($lopt{strlen($lopt) - 2} === '=') {
                $opt_def[$lopt{0}] = substr($lopt, 0, strlen($lopt) - 2);
                $sopts .= $lopt{0}
                . '::';
            } elseif ($lopt{strlen($lopt) - 1} === '=') {
                $opt_def[$lopt{0}] = substr($lopt, 0, strlen($lopt) - 1);
                $sopts .= $lopt{0}
                . ':';
            } else {
                $opt_def[$lopt{0}] = $lopt;
                $sopts .= $lopt{0};
            }
        }

        // do getopt
        // ex: $sopts = 'fb:';
        $opt = new Getopt();
        $opts_args = $opt->getopt($this->arg_list, $sopts, $lopts);

        // parse opts
        // ex: "-ff --bar=baz" gets
        //      $opts = array('foo' => array(true, true),
        //                    'bar' => array('baz'));
        $opts = array();
        foreach ($opts_args[0] as $opt) {
            $opt[0] = $opt[0]{0}
            === '-' ? $opt_def[$opt[0]{2}] : $opt_def[$opt[0]{0}];
            $opt[1] = $opt[1] === null ? true : $opt[1];
            if (isset($opts[$opt[0]]) === false) {
                $opts[$opt[0]] = array($opt[1]);
            } else {
                $opts[$opt[0]][] = $opt[1];
            }
        }
        $opts_args[0] = $opts;

        return $opts_args;
    }

    /**
     *
     */
    public function perform()
    {
    }

    /**
     *
     */
    public function usage()
    {
        echo "usage:\n";
        echo $this->getUsage() . "\n\n";
    }

    /**
     *  スケルトンを生成する
     *
     *  @access public
     *  @param  string  $name       Generatorプラグインの名前
     *  @param  string  $app_dir    アプリケーションのディレクトリ
     *                              (nullのときはアプリケーションを特定しない)
     *  @param  mixed   residue     プラグインのgenerate()にそのまま渡す
     *  @static
     */
    public static function generate()
    {
        $arg_list   = func_get_args();
        $name       = array_shift($arg_list);
        $app_dir    = array_shift($arg_list);

        if ($app_dir === null) {
            $ctl = Ethna_Command::getEthnaController();
        } else {
            $ctl = Ethna_Command::getAppController($app_dir);
        }

        $className = '\\Ethnam\\Generator\\Generator\\' . $name;
        $generator = new $className($ctl);

        // 引数はプラグイン依存とする
        call_user_func_array(array($generator, 'generate'), $arg_list);
    }

    /**
     *  スケルトンを削除する
     *
     *  @access public
     *  @param  string  $name       生成する対象
     *  @param  string  $app_dir    アプリケーションのディレクトリ
     *                              (nullのときはアプリケーションを特定しない)
     *  @param  mixed   residue     プラグインのremove()にそのまま渡す
     *  @static
     */
    public static function remove()
    {
        $arg_list   = func_get_args();
        $name       = array_shift($arg_list);
        $app_dir    = array_shift($arg_list);

        if ($app_dir === null) {
            $ctl = Ethna_Command::getEthnaController();
        } else {
            $ctl = Ethna_Command::getAppController($app_dir);
        }
        if (Ethna::isError($ctl)) {
            return $ctl;
        }

        $className = '' . $name;
        $generator = new $className($ctl);

        // 引数はプラグイン依存とする
        $ret = call_user_func_array(array($generator, 'remove'), $arg_list);
        return $ret;
    }

    /**
     *  アクション名をチェックする
     *
     *  @access public
     *  @param  string  $action_name    アクション名
     *  @static
     */
    public static function checkActionName($action_name)
    {
        if (preg_match('/^[a-zA-Z\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/',
                       $action_name) === 0) {
            throw new \Exception("invalid action name [$action_name]");
        }
    }

    /**
     *  ビュー名をチェックする
     *
     *  @access public
     *  @param  string  $view_name    ビュー名
     *  @static
     */
    public static function checkViewName($view_name)
    {
        if (preg_match('/^[a-zA-Z\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/',
                       $view_name) === 0) {
            throw new \Exception("invalid view name [$view_name]");
        }
    }
}
// }}}
