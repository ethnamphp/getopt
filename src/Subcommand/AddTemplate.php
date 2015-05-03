<?php
/**
 *  AddTemplate.php
 *
 *  @author     nnno <nnno@nnno.jp>
 */
namespace Ethnam\Generator\Subcommand;

/**
 *  add-template handler
 *
 *  @author     nnno <nnno@nnno.jp>
 *  @access     public
 */
class AddTemplate extends AddView
{
    /**
     *
     */
    public function perform()
    {
        $r = $this->_getopt(
                  array('basedir=',
                        'skelfile=',
                        'locale=',
                        'encoding=',
                  )
              );
        list($opt_list, $arg_list) = $r;

        // template
        $template = array_shift($arg_list);
        if ($template == null) {
            throw new \Exception('template name isn\'t set.');
        }
        Base::checkViewName($template); // XXX: use checkViewName().

        // add template
        $this->_performTemplate($template, $opt_list);
    }

    /**
     *  get handler's description
     *
     *  @access public
     */
    public function getDescription()
    {
        return <<<EOS
add new template to project:
    {$this->id} [-b|--basedir=dir] [-s|--skelfile=file] [-l|--locale=locale] [-e|--encoding] [template]

EOS;
    }

    /**
     *  @access public
     */
    public function getUsage()
    {
        return <<<EOS
ethna {$this->id} [-b|--basedir=dir] [-s|--skelfile=file] [-l|--locale=locale] [-e|--encoding] [template]
EOS;
    }
}
