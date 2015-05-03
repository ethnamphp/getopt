<?php
/**
 *  AddAppManager.php
 *
 *  @author     nozzzzz <nozzzzz@gmail.com>
 */
namespace Ethnam\Generator\Subcommand;

/**
 *  add-app-manager handler
 *
 *  @author     nozzzzz <nozzzzz@gmail.com>
 *  @access     public
 */
class AddAppManager extends Base
{
    /**
     */
    public function perform()
    {
        throw new \Exception('not implimented yet');
    }

    /**
     *  get handler's description
     *
     *  @access public
     */
    public function getDescription()
    {
        return <<<EOS
add new app-manager to project:
    {$this->id} [-b|--basedir=dir] [app-manager name]

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
ethna {$this->id} [-b|--basedir=dir] [app-manager name]
EOS;
    }
}
