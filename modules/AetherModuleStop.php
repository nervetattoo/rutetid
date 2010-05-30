<?php
class AetherModuleStop extends AetherModuleHeader {
    public function run() {
        if ($this->sl->has("stopId")) {
            $out = "Yey";
        }
        else {
            $out = "Ney";
        }
        return $out;
    }
}
