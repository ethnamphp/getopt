<?php
/**
 * Test.php
 *
 * @author BoBpp <bobpp@users.sourceforge.jp>
 */
namespace Ethnam\Generator\Generator;

use Ethna_Util;

/**
 * Normal Test Case Generator.
 *
 * @author BoBpp <bobpp@users.sourceforge.jp>
 */
class Test extends Base
{
    /**
     * ファイル生成を行う
     *
     * @access public
     * @param string $skelfile スケルトンファイル名
     * @param string $name     テストケース名
     */
    public function generate($skelfile, $name)
    {
        // Controllerを取得
        $ctl = $this->ctl;

        // テストを生成するディレクトリがあるか？
        // なければ app/test がデフォルト。
        $dir = $ctl->getDirectory('test');
        if ($dir === null) {
            $dir = $ctl->getDirectory('app') . "/" . "test";
        }

        // ファイル名生成
        $file = preg_replace('/_(.)/e', "'/' . strtoupper('\$1')", ucfirst($name)) . "Test.php";
        $generatePath = "$dir/$file";

        // スケルトン決定
        $skelton = (!empty($skelfile))
                 ? $skelfile
                 : "skel.test.php";

        // マクロ生成
        $macro = array();
        $macro['project_id'] = ucfirst($ctl->getAppId());
        $macro['file_path'] = $file;
        $macro['name'] = preg_replace('/_(.)/e', "strtoupper('\$1')", ucfirst($name));

        $userMacro = $this->_getUserMacro();
        $macro = array_merge($macro, $userMacro);

        // 生成
        Ethna_Util::mkdir(dirname($generatePath), 0755);
        if (file_exists($generatePath)) {
            printf("file [%s] already exists -> skip\n", $generatePath);
        } elseif ($this->_generateFile($skelton, $generatePath, $macro) == false) {
            printf("[warning] file creation failed [%s]\n", $generatePath);
        } else {
            printf("test script(s) successfully created [%s]\n", $generatePath);
        }

    }
}
