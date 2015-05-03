<?php
/**
 *  AddProject.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 */
namespace Ethnam\Generator\Subcommand;

/**
 *  add-project handler
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 */
class AddProject extends Base
{
    /**
     *  アプリケーションIDをチェックする
     *
     *  @param  string  $id     アプリケーションID
     */
    public static function checkAppId($id)
    {
        if (strcasecmp($id, 'ethna') === 0
            || strcasecmp($id, 'app') === 0) {
            throw new \InvalidArgumentException("Application Id [$id] is reserved\n");
        }

        //    アプリケーションIDはクラス名のprefixともなるため、
        //    数字で始まっていてはいけない
        //    @see http://www.php.net/manual/en/language.variables.php
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $id) === 0) {
            $msg = (preg_match('/^[0-9]$/', $id[0]))
                 ? "Application ID must NOT start with Number.\n"
                 : "Only Numeric(0-9) and Alphabetical(A-Z) is allowed for Application Id\n";
            throw new \InvalidArgumentException($msg);
        }

    }


    /**
     *  add project:)
     *
     *  @access public
     */
    function perform()
    {
        $r = $this->_getopt(array('basedir=', 'skeldir=', 'locale=', 'encoding='));
        list($opt_list, $arg_list) = $r;

        // app_id
        $app_id = array_shift($arg_list);
        if ($app_id == null) {
            throw new \InvalidArgumentException('Application id isn\'t set.');
        }

        self::checkAppId($app_id);

        // basedir
        if (isset($opt_list['basedir'])) {
            $dir = end($opt_list['basedir']);
            $basedir = realpath($dir);
            if ($basedir === false) {  //  e.x file does not exist
                $basedir = $dir;
            }
        } else {
            $basedir = sprintf("%s/%s", getcwd(), strtolower($app_id));
        }

        // skeldir
        if (isset($opt_list['skeldir'])) {
            $selected_dir = end($opt_list['skeldir']);
            $skeldir = realpath($selected_dir);
            if ($skeldir == false || is_dir($skeldir) == false || file_exists($skeldir) == false) {
                throw new \InvalidArgumentException("You specified skeldir, but invalid : $selected_dir");
            }
        } else {
            $skeldir = null;
        }

        // locale
        if (isset($opt_list['locale'])) {
            $locale = end($opt_list['locale']);
            if (!preg_match('/^[A-Za-z_]+$/', $locale)) {
                throw new \InvalidArgumentException("You specified locale, but invalid : $locale");
            }
        } else {
            $locale = 'ja_JP';  //  default locale.
        }

        // encoding
        if (isset($opt_list['encoding'])) {
            $encoding = end($opt_list['encoding']);
            if (function_exists('mb_list_encodings')) {
                $supported_enc = mb_list_encodings();
                if (!in_array($encoding, $supported_enc)) {
                    throw new \InvalidArgumentException("Unknown Encoding : $encoding");
                }
            }
        } else {
            $encoding = 'UTF-8';  //  default encoding.
        }

        Base::generate('Project', null, $app_id, $basedir, $skeldir, $locale, $encoding);
        printf("\nproject skelton for [%s] is successfully generated at [%s]\n\n", $app_id, $basedir);
        return true;
    }

    /**
     *  get handler's description
     *
     *  @access public
     */
    function getDescription()
    {
        return <<<EOS
add new project:
    {$this->id} [-b|--basedir=dir] [-s|--skeldir] [-l|--locale] [-e|--encoding] [Application id]

EOS;
    }

    /**
     *  get usage
     *
     *  @access public
     */
    function getUsage()
    {
        return <<<EOS
ethna {$this->id} [-b|--basedir=dir] [-s|--skeldir] [-l|--locale] [-e|--encoding] [Application id]
EOS;
    }
}
// }}}
