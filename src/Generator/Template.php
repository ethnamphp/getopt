<?php
/**
 *  Template.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 */
namespace Ethnam\Generator\Generator;

use Ethna_Util;

/**
 *  スケルトン生成クラス
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 */
class Template extends Base
{
    /**
     *  テンプレートのスケルトンを生成する
     *
     *  @access public
     *  @param  string  $forward_name   テンプレート名
     *  @param  string  $skelton        スケルトンファイル名
     *  @param  string  $locale         ロケール名
     */
    public function generate($forward_name, $skelton = null, $locale)
    {
        //  ロケールが指定された場合は、それを優先する
        if (!empty($locale)) {
            $this->ctl->setLocale($locale);
        }

        //  ロケール名がディレクトリに含まれていない場合は、
        //  ディレクトリがないためなのでそれを補正
        $tpl_dir = $this->ctl->getTemplatedir();
        $tpl_path = $this->ctl->getDefaultForwardPath($forward_name);

        // entity
        $entity = $tpl_dir . '/' . $tpl_path;
        Ethna_Util::mkdir(dirname($entity), 0755);

        // skelton
        if ($skelton === null) {
            $skelton = 'skel.template.tpl';
        }

        // macro
        $macro = array();
        // add '_' for tpl and no user macro for tpl
        $macro['_project_id'] = $this->ctl->getAppId();

        // generate
        if (file_exists($entity)) {
            printf("file [%s] already exists -> skip\n", $entity);
        } elseif ($this->_generateFile($skelton, $entity, $macro) == false) {
            printf("[warning] file creation failed [%s]\n", $entity);
        } else {
            printf("template file(s) successfully created [%s]\n", $entity);
        }

    }
}
