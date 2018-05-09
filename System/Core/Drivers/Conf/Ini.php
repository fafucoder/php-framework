<?php

namespace System\Drivers\Conf;

class Ini implements Adapter {
    public function parse($config) {
        if (is_file($config)) {
            return parse_ini_file($config, true);
        } else {
            return parse_ini_string($config, true);
        }
    }
}
