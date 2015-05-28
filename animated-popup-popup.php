<?php

$templateString = '<div class="ap-cover">
    <div class="ap-bg">
        <div class="ap-container" style="background:';
        $templateString .= $apData['box_color'] ? $apData['box_color'] : '#1E375D';
        $templateString .= ';">
            <div id="popup-closer" class="close-button">x</div>
            <h3 class="ap-text ap-title">';
                $templateString .= $apData['header_text'] ? $apData['header_text'] : 'Sign Up Now';
            $templateString .= '</h3>
            <p class="ap-text ap-subtitle">';
            $templateString .= $apData['paragraph_text'] ? $apData['paragraph_text'] : 'Get updates in your email inbox';
            $templateString .= '</p>
            <div class="ap-form">';
                if (!is_admin()) { $templateString .= '<form>'; }
                    $templateString .= '<input class="ap-input form-control" type="text" name="email" placeholder="email" autocomplete="off">
                    <input class="ap-button btn btn-primary" type="submit" value="Sign Up" style="background:';
                    $templateString .= $apData['button_color'] ? $apData['button_color'] : '#4B67B8';
                    $templateString .= '">';
                if (!is_admin()) { $templateString .= '<form>'; }
            $templateString .= '</div>
        </div>
    </div>
</div>';
