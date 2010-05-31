<?php
class AetherModuleRuteHeader extends AetherModuleHeader {
    public function run() {
        $tpl = $this->sl->getTemplate();
        $config = $this->sl->get('aetherConfig');
        // Should be in admin module, but breaks somehow
        $this->applyCommonVariables($tpl);
        return $tpl->fetch('header.tpl');
    }
}
