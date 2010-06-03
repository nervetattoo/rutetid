<?php
require_once("AetherModuleAuthed.php");
class AetherModuleAdmin extends AetherModuleAuthed {
    public function runAdmin() {
        $tpl = $this->sl->getTemplate();
        return $tpl->fetch('admin.tpl');
    }
}
