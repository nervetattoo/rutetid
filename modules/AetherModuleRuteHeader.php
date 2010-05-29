<?php
class AetherModuleRuteHeader extends AetherModuleHeader {
    public function run() {
        $tpl = $this->sl->getTemplate();
        $config = $this->sl->get('aetherConfig');
        return $tpl->fetch('header.tpl');
    }
}
