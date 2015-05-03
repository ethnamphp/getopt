<?php
/**
 *  EntryPoint.php
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 */
namespace Ethnam\Generator\Generator;

/**
 *  スケルトン生成クラス
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @access     public
 */
class EntryPoint extends Base
{
    /**
     *  エントリポイントのスケルトンを生成する
     *
     *  @access public
     *  @param  string  $skelton    スケルトンファイル名
     *  @param  int     $gateway    ゲートウェイ
     */
    public function generate($action_name, $skelton = null, $gateway = GATEWAY_WWW)
    {
        // entity
        switch ($gateway) {
        case GATEWAY_WWW:
            $entity = sprintf("%s/%s.%s", $this->ctl->getDirectory('www'),
                              $action_name, $this->ctl->getExt('php'));
            break;
        case GATEWAY_CLI:
            $entity = sprintf("%s/%s.%s", $this->ctl->getDirectory('bin'),
                              $action_name, $this->ctl->getExt('php'));
            break;
        default:
            throw new \Exception(
                'add-entry-point accepts only GATEWAY_WWW or GATEWAY_CLI.');
        }

        // skelton
        if ($skelton === null) {
            switch ($gateway) {
            case GATEWAY_WWW:
                $skelton = 'skel.entry_www.php';
                break;
            case GATEWAY_CLI:
                $skelton = 'skel.entry_cli.php';
                break;
            }
        }
        if (file_exists($entity)) {
            printf("file [%s] already exists -> skip\n", $entity);
            return;
        }

        // macro
        $macro = array();
        $macro['project_id'] = $this->ctl->getAppId();
        $macro['action_name'] = $action_name;
        $macro['dir_app'] = $this->ctl->getDirectory('app');

        // user macro
        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        // generate
        $ret = $this->_generateFile($skelton, $entity, $macro);
        if ($ret) {
            printf("action script(s) successfully created [%s]\n", $entity);
        } else {
            printf("[warning] file creation failed [%s]\n", $entity);
            return; // TODO: error handling
        }

        // chmod
        if ($gateway === GATEWAY_CLI) {
            // is needed?
            //$ret = Ethna_Util::chmod($entity, 0777);
        }
    }
}
