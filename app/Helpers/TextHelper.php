<?php

namespace App\Helpers;

class TextHelper {
    public static function colorText(string $text, string $color) {
        return '<span style="color: ' . $color . '">' . $text . '</span>';
    }
}

?>