<?php
/**
 *  AddProject.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 */
namespace Ethnam\Generator\Subcommand;

use Ethnam\Generator\Generator\Project;

/**
 *  add-project handler
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 */
class AddProject extends Base
{

    /**
     *
     */
    public function perform()
    {
        $r = $this->_getopt(array('basedir=', 'skeldir=', 'locale=', 'encoding='));
        list($opt_list, $arg_list) = $r;

        // app_id
        $app_id = array_shift($arg_list);
        if ($app_id == null) {
            throw new \InvalidArgumentException('Application id isn\'t set.');
        }

        Project::checkAppId($app_id);

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

        $encoding = 'UTF-8';

        Base::generate('Project', null, $app_id, $basedir, $skeldir, $locale, $encoding);
        printf("\nproject skelton for [%s] is successfully generated at [%s]\n\n", $app_id, $basedir);
    }

    /**
     *  get handler's description
     *
     *  @access public
     */
    public function getDescription()
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
    public function getUsage()
    {
        return <<<EOS
ethna {$this->id} [-b|--basedir=dir] [-s|--skeldir] [-l|--locale] [-e|--encoding] [Application id]
EOS;
    }
}
