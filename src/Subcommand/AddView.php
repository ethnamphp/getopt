<?php
/**
 *  AddView.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 */
namespace Ethnam\Generator\Subcommand;

use Ethnam\Generator\Command;

/**
 *  add-view handler
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 */
class AddView extends AddAction
{
    /**
     *
     */
    public function perform()
    {
        //
        //  '-w[with-unittest]' and '-u[unittestskel]' option
        //  are not intuisive, but I dare to define them because
        //  -t and -s option are reserved by add-[action|view] handle
        //  and Ethna_Getopt cannot interpret two-character option.
        //
        $r = $this->_getopt(
                  array('basedir=',
                        'skelfile=',
                        'with-unittest',
                        'unittestskel=',
                        'template',
                        'locale=',
                        'encoding=',
                  )
              );
        list($opt_list, $arg_list) = $r;

        // view_name
        $view_name = array_shift($arg_list);
        if ($view_name == null) {
            throw new \Exception('view name isn\'t set.');
        }
        Base::checkViewName($view_name);

        // add view(invoke parent class method)
        $ret = $this->_perform('View', $view_name, $opt_list);

        // add template
        if (isset($opt_list['template'])) {
            $ret = $this->_performTemplate($view_name, $opt_list);
        }
    }

    /**
     *  Special Function for generating template.
     *
     *  @param  string $target_name Template Name
     *  @param  array  $opt_list    Option List.
     *  @access protected
     */
    public function _performTemplate($target_name, $opt_list)
    {
        // basedir
        if (isset($opt_list['basedir'])) {
            $basedir = realpath(end($opt_list['basedir']));
        } else {
            $basedir = getcwd();
        }

        // skelfile
        if (isset($opt_list['skelfile'])) {
            $skelfile = end($opt_list['skelfile']);
        } else {
            $skelfile = null;
        }

        // locale
        $ctl = Command::getAppController(getcwd());
        if (isset($opt_list['locale'])) {
            $locale = end($opt_list['locale']);
            if (!preg_match('/^[A-Za-z_]+$/', $locale)) {
                throw new \Exceptionxo("You specified locale, but invalid : $locale");
            }
        } else {
            if (\Ethna::isError($ctl)) {
                $locale = 'ja_JP';
            } else {
                $locale = $ctl->getLocale();
            }
        }

        Base::generate('Template', $basedir,
                                        $target_name, $skelfile, $locale);
    }

    /**
     *  get handler's description
     *
     *  @access public
     */
    public function getDescription()
    {
        return <<<EOS
add new view to project:
    {$this->id} [options... ] [view name]
    [options ...] are as follows.
        [-b|--basedir=dir] [-s|--skelfile=file]
        [-w|--with-unittest] [-u|--unittestskel=file]
        [-t|--template] [-l|--locale] [-e|--encoding]
    NOTICE: "-w" and "-u" options are ignored when you specify -t option.
            "-l" and "-e" options are enabled when you specify -t option.

EOS;
    }

    /**
     *  @access public
     */
    public function getUsage()
    {
        return <<<EOS
ethna {$this->id} [options... ] [view name]
    [options ...] are as follows.
        [-b|--basedir=dir] [-s|--skelfile=file]
        [-w|--with-unittest] [-u|--unittestskel=file]
        [-t|--template] [-l|--locale] [-e|--encoding]
    NOTICE: "-w" and "-u" options are ignored when you specify -t option.
            "-l" and "-e" options are enabled when you specify -t option.
EOS;
    }
}
