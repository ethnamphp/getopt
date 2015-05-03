<?php
/**
 *  AddEntryPoint.php
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 */

/**
 *  add-action handler
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @access     public
 */
class Ethna_Subcommand_AddEntryPoint extends Ethna_Subcommand_AddAction
{
    /**
     *  add action entry point
     *
     *  @access public
     */
    function perform()
    {
        $r = $this->_getopt(array('basedir=', 'skelfile=', 'gateway='));
        if (Ethna::isError($r)) {
            return $r;
        }
        list($opt_list, $arg_list) = $r;

        // action_name
        $action_name = array_shift($arg_list);
        if ($action_name == null) {
            return Ethna::raiseError('action name isn\'t set.', 'usage');
        }
        $r = Ethna_Controller::checkActionName($action_name);
        if (Ethna::isError($r)) {
            return $r;
        }

        // add entry point
        $ret = $this->_perform('EntryPoint', $action_name, $opt_list);
        if (Ethna::isError($ret) || $ret === false) { 
            return $ret;
        }

        // add action (no effects if already exists.)
        $ret = $this->_perform('Action', $action_name, $opt_list);
        if (Ethna::isError($ret) || $ret === false) { 
            return $ret;
        }

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
add new action and its entry point to project:
    {$this->id} [-b|--basedir=dir] [-s|--skelfile=file] [-g|--gateway=www|cli] [action]

EOS;
    }

    /**
     *  @access public
     */
    function getUsage()
    {
        return <<<EOS
ethna {$this->id} [-b|--basedir=dir] [-s|--skelfile=file] [-g|--gateway=www|cli] [action]
EOS;
    }
}
// }}}
