<?php
/**
 *  AddEntryPoint.php
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 */
namespace Ethnam\Generator\Subcommand;

/**
 *  add-action handler
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @access     public
 */
class AddEntryPoint extends AddAction
{
    /**
     *
     */
    public function perform()
    {
        $r = $this->_getopt(array('basedir=', 'skelfile=', 'gateway='));
        list($opt_list, $arg_list) = $r;

        // action_name
        $action_name = array_shift($arg_list);
        if ($action_name == null) {
            throw new \Exception('action name isn\'t set.');
        }

        Base::checkActionName($action_name);

        // add entry point
        $this->_perform('EntryPoint', $action_name, $opt_list);

        // add action (no effects if already exists.)
        $this->_perform('Action', $action_name, $opt_list);
    }

    /**
     *  get handler's description
     *
     *  @access public
     */
    public function getDescription()
    {
        return <<<EOS
add new action and its entry point to project:
    {$this->id} [-b|--basedir=dir] [-s|--skelfile=file] [-g|--gateway=www|cli] [action]

EOS;
    }

    /**
     *  @access public
     */
    public function getUsage()
    {
        return <<<EOS
ethna {$this->id} [-b|--basedir=dir] [-s|--skelfile=file] [-g|--gateway=www|cli] [action]
EOS;
    }
}
