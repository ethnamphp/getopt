<?php
/**
 *  AddAction.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 */
namespace Ethnam\Generator\Subcommand;

/**
 *  add-action handler
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 */
class AddAction extends Base
{
    /**
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
                        'gateway=',
                        'with-unittest',
                        'unittestskel=',
                  )
             );

        list($opt_list, $arg_list) = $r;

        // action_name
        $action_name = array_shift($arg_list);
        if ($action_name == null) {
            throw new \Exception('action name isn\'t set.');
        }
        Base::checkActionName($action_name);

        $this->_perform('Action', $action_name, $opt_list);
    }

    /**
     */
    protected function _perform($target, $target_name, $opt_list)
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

        // gateway
        if (isset($opt_list['gateway'])) {
            $gateway = 'GATEWAY_' . strtoupper(end($opt_list['gateway']));
            if (defined($gateway)) {
                $gateway = constant($gateway);
            } else {
                throw new \Exception('unknown gateway');
            }
        } else {
            $gateway = GATEWAY_WWW;
        }

        //  possible target is Action, View.
        $r = Base::generate($target, $basedir,
                                        $target_name, $skelfile, $gateway);

        //
        //  if specified, generate corresponding testcase,
        //  except for template.
        //
        if ($target != 'Template' && isset($opt_list['with-unittest'])) {
            $testskel = (isset($opt_list['unittestskel']))
                      ? end($opt_list['unittestskel'])
                      : null;
            Base::generate("{$target}Test", $basedir, $target_name, $testskel, $gateway);
        }
    }

    /**
     *  get handler's description
     *
     *  @access public
     */
    public function getDescription()
    {
        return <<<EOS
add new action to project:
    {$this->id} [-b|--basedir=dir] [-s|--skelfile=file] [-g|--gateway=www|cli] [-w|--with-unittest] [-u|--unittestskel=file] [action]

EOS;
    }

    /**
     *  @access public
     */
    public function getUsage()
    {
        return <<<EOS
ethna {$this->id} [-b|--basedir=dir] [-s|--skelfile=file] [-g|--gateway=www|cli] [-w|--with-unittest] [-u|--unittestskel=file] [action]

EOS;
    }
}
