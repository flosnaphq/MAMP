<?php

trait CommonShortcodeFunctions {

    public static function fatShortcodeAtts($pairs, $atts) {
        $atts = (array) $atts;
        $out = array();
        foreach ($pairs as $name => $default) {
            $name = trim($name);
            if (array_key_exists($name, $atts)) {
                $out[$name] = $atts[$name];
            } else {
                $out[$name] = $default;
            }
        }
        // print_r($out);exit;
        return $out;
    }

    public function fat_sociallinks($params = array()) {
        $atts = self::fatShortcodeAtts(
                        array(
                    'list' => 'facebook, twitter, instagram, pinterest, snapchat, youtube, google',
                    'heading' => Info::t_lang('HANG_OUT_WITH_US')
                        ), $params);
        $list = str_replace(' ', '', $atts['list']);

        $list = explode(',', $list);

        if (!$slinkList = $this->getSocialLinkList($list)) {
            return false;
        }
        // Info::test($slinkList);exit;
        ob_start();
        ?>
        <h6 class="f__block__heading"><?php echo $atts['heading']; ?></h6>
        <nav class="menu f__social-menu">
            <ul class="list list--horizontal">
                <?php
                foreach ($slinkList as $key => $val) {
                    if (empty($val)) {
                        continue;
                    }
                    ?>
                    <li>
                        <a target="_blank" href="<?php echo $val; ?>">
                            <span class="f__social__icon"><svg class="icon"><use xlink:href="#icon-<?php echo $key; ?>" /></svg></span>
                            <span class="hidden-on--mobile"><?php echo Info::t_lang(strtoupper($key)) ?></span></a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </nav>
        <?php
        $html = ob_get_contents();
        ob_get_clean();
        return $html;
    }

    private function getSocialLinkList($list = array()) {
        
        if (count($list) < 1) {
            return false;
        }

        $slist = array();
        foreach ($list as $key => $val) {
            $slist[$val] = FatApp::getConfig('conf_' . trim($val) . '_url', FatUtility::VAR_STRING, '');
        }

        return $slist;
    }

}
?>