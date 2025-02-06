<?php
/**
 * 当用户离开标签页时，页面标题变为“崩溃啦XD”
 * @package 崩溃啦XD
 * @author 四折光曲 贰贰叁叁
 * @version 1.1.0
 * @link https://hello2233.top/
 */
class ItWasBadXD_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        // 确保工厂方法调用正确
        Typecho_Plugin::factory('Widget_Archive')->header = array(__CLASS__, 'header');
        return _t('插件已启用');
    }

    public static function deactivate()
    {
        return _t('插件已禁用');
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        // 添加输入验证规则
        $blurTitle = new Typecho_Widget_Helper_Form_Element_Text(
            'blurTitle',
            NULL,
            '崩溃啦XD',
            _t('失去焦点显示的标题'),
            _t('支持HTML特殊字符，但会被转义')
        );
        $form->addInput($blurTitle->addRule('xssCheck', _t('请勿包含非法字符')));

        $focusTitle = new Typecho_Widget_Helper_Form_Element_Text(
            'focusTitle',
            NULL,
            '骗你的啦WWW',
            _t('获得焦点显示的标题'),
            _t('支持HTML特殊字符，但会被转义')
        );
        $form->addInput($focusTitle->addRule('xssCheck', _t('请勿包含非法字符')));

        // 使用数字输入类型并添加验证
        $delayTime = new Typecho_Widget_Helper_Form_Element_Text(
            'delayTime',
            NULL,
            '1000',
            _t('延迟时间（毫秒）'),
            _t('请输入大于0的整数')
        );
        $form->addInput($delayTime->addRule('isInteger', _t('必须为整数'))->addRule('min', _t('不能小于1'), 1));
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}

    public static function header()
    {
        // 确保插件配置加载正确
        $options = Typecho_Widget::widget('Widget_Options')->plugin('ItWasBadXD');
        if (!$options) {
            return;
        }

        // 使用JSON_HEX_APOS防止单引号问题
        $blurTitle = json_encode($options->blurTitle, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
        $focusTitle = json_encode($options->focusTitle, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
        $delayTime = intval($options->delayTime) ?: 1000;

        // 输出JavaScript代码
        echo <<<HTML
<script type='text/javascript'>
(function(){
    let timeoutId;
    const originalTitle = document.title;
    
    function handleBlur() {
        document.title = {$blurTitle};
    }
    
    function handleFocus() {
        document.title = {$focusTitle};
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            if (!document.hidden) {
                document.title = originalTitle;
            }
        }, {$delayTime});
    }

    // 同时支持传统事件和Page Visibility API
    const supportsVisibility = typeof document.hidden !== 'undefined';
    
    if(supportsVisibility) {
        document.addEventListener('visibilitychange', () => {
            document.hidden ? handleBlur() : handleFocus();
        });
    } else {
        window.addEventListener('blur', handleBlur);
        window.addEventListener('focus', handleFocus);
    }
})();
</script>
HTML;
    }
}
