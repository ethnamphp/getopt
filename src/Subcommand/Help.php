<?php
/**
 *  Plugin_Handle_Help.php
 *
 *  @author     Tomoyuki MARUTA <maru_cc@users.sourceforge.jp>
 */
namespace Ethnam\Generator\Subcommand;

/**
 *  add-action handler
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @access     public
 */
class Help extends Base
{

    /**
     *
     */
    public function perform()
    {
        $r = $this->_getopt();
        list($opt_list, $arg_list) = $r;

        // action_name
        $handle_name = array_shift($arg_list);
        if (!strlen($handle_name)) {
            $handler_list = ["help"];
            printf("usage: ethna [option] [command] [args...]\n\n");
            printf("available options are as follows:\n\n");
            printf("  -v, --version    show version and exit\n");
            printf("\navailable commands are as follows:\n\n");
            foreach ($handler_list as $handler) {
                printf("  %s\n", $handler);
            }
            return true;
        }

        // getHandler
        $handler = \Ethnam\Generator\Command::newSubcommand($handle_name);
        echo $handler->getDescription();

        return true;
    }

    /**
     *  get handler's description
     *
     *  @access public
     */
    public function getDescription()
    {
        return <<<EOS
help:
    {$this->id} [command_name]

EOS;
    }

    /**
     *  @access public
     */
    public function getUsage()
    {
        return <<<EOS
ethna {$this->id} [command_name]
EOS;
    }
}
