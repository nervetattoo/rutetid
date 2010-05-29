<?php
class AetherModuleRuteFooter extends AetherModuleHeader {
    public function run() {
        $tpl = $this->sl->getTemplate();
        return $tpl->fetch('footer.tpl');
    }
}
