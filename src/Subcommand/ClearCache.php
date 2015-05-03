<?php
/**
 *  ClearCache.php
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 */
namespace Ethnam\Generator\Subcommand;

use Ethnam\Generator\Command as Ethna_Command;
use Ethna_Util;
use Ethna;

/**
 *  clear-cache handler
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @access     public
 */
class ClearCache extends Base
{
    /**
     *
     *  @todo   implement Ethna_Renderer::clear_cache();
     *  @todo   implement Ethna_Plugin_Cachemanager::clear_cache();
     *  @todo   avoid echo, printf
     */
    public function perform()
    {
        $r = $this->_getopt(array('basedir=',
                                   'any-tmp-files', 'smarty', 'pear', 'cachemanager'));
        list($args, ) = $r;

        $basedir = isset($args['basedir']) ? realpath(end($args['basedir'])) : getcwd();
        $controller = Ethna_Command::getAppController($basedir);
        if (Ethna::isError($controller)) {
            return $controller;
        }
        $tmp_dir = $controller->getDirectory('tmp');

        if (isset($args['smarty']) || isset($args['any-tmp-files'])) {
            echo "cleaning smarty caches, compiled templates...";
            $renderer = $controller->getRenderer();
            if (strtolower(get_class($renderer)) == "ethna_renderer_smarty") {
                $renderer->getEngine()->clear_all_cache();
                $renderer->getEngine()->clear_compiled_tpl();
            }
            echo " done\n";
        }

        if (isset($args['cachemanager']) || isset($args['any-tmp-files'])) {
            echo "cleaning Ethna_Plugin_Cachemanager caches...";
            $cache_dir = sprintf("%s/cache", $tmp_dir);
            Ethna_Util::purgeDir($cache_dir);
            echo " done\n";
        }

        if (isset($args['any-tmp-files'])) {
            echo "cleaning tmp dirs...";
            // purge only entries in tmp.
            if ($dh = opendir($tmp_dir)) {
                while (($entry = readdir($dh)) !== false) {
                    if ($entry === '.' || $entry === '..') {
                        continue;
                    }
                    Ethna_Util::purgeDir("{$tmp_dir}/{$entry}");
                }
                closedir($dh);
            }
            echo " done\n";
        }

        return true;
    }

    /**
     *  @access public
     */
    public function getDescription()
    {
        return <<<EOS
clear project's cache files:
    {$this->id} [-b|--basedir=dir] [-a|--any-tmp-files] [-s|--smarty] [-p|--pear] [-c|--cachemanager]

EOS;
    }

    /**
     *  @access public
     */
    public function getUsage()
    {
        return <<<EOS
ethna {$this->id} [-b|--basedir=dir] [-a|--any-tmp-files] [-s|--smarty] [-p|--pear] [-c|--cachemanager]
EOS;
    }
}
