<?php
/**
 * 当用户离开标签页时，页面标题变为“崩溃啦XD”
 * @package 崩溃啦XD
 * @author 四折光曲 贰贰叁叁
 * @version 1.0.0
 * @link https://hello2233.top/
 */
class ItWasBadXD_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->header = array('ItWasBadXD_Plugin', 'header');
    }

    public static function deactivate(){}

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $blurTitle = new Typecho_Widget_Helper_Form_Element_Text('blurTitle', NULL, '崩溃啦XD', _t('失去焦点显示的标题'));
        $form->addInput($blurTitle);

        $focusTitle = new Typecho_Widget_Helper_Form_Element_Text('focusTitle', NULL, '骗你的啦WWW', _t('获得焦点显示的标题'));
        $form->addInput($focusTitle);

        $delayTime = new Typecho_Widget_Helper_Form_Element_Text('delayTime', NULL, '1000', _t('延迟时间（毫秒）'));
        $form->addInput($delayTime);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    public static function header()
    {
        $options = Typecho_Widget::widget('Widget_Options')->plugin('ItWasBadXD');
        echo "<script type='text/javascript'>
        var originalTitle = document.title;
        window.onblur = function() { document.title = '{$options->blurTitle}'; }
        window.onfocus = function() {
            document.title = '{$options->focusTitle}';
            setTimeout(function(){
                if (!document.hidden) {
                    document.title = originalTitle;
                }
            }, {$options->delayTime});
        }
        </script>";
    }
}
?>
