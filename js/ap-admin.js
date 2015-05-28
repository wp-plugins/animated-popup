var apContainer = jQuery(".ap-container");
var apInput = jQuery(".ap-input");
var apButton = jQuery(".ap-button");
var apForm = jQuery(".ap-form");

jQuery(function () {
    jQuery('.ap-bg').css('height', jQuery(".ap-container").css('height'));

    setAnimation(jQuery('#animation-select').find('option:selected').val(), 0);

    jQuery('#animation-select').change(function (e) {
        var optionValue = jQuery(this).find('option:selected').val();
        setAnimation(optionValue, 0);
    });
    jQuery(".ap-button").click(function (e) {
        e.preventDefault();
    });

    checkBoxColor();
    checkButtonColor();
    checkWpmail();
    // checkMandrill();
    checkMailchimp();
    checkAweber();
    checkMailgun();
    checkMadmimi();

    jQuery('input').click(function (e) {
        e = e || window.event;
        var target = e.target || e.srcElement;
        switch (jQuery(target).prop('name')) {
            case 'box_color': checkBoxColor(e); break;
            case 'button_color': checkButtonColor(); break;
            case 'wpmail_enabled': checkWpmail(); break;
            // case "mandrill_enabled": checkMandrill(); break;
            case "mailchimp_enabled": checkMailchimp(); break;
            case "aweber_enabled": checkAweber(); break;
            case "mailgun_enabled": checkMailgun(); break;
            case "madmimi_enabled": checkMadmimi(); break;
        }
    });

    function checkBoxColor(e) {
        var bg;
        var checked = !jQuery('.custom-box-color').prop('checked');
        jQuery('.custom-box-color-text').prop('disabled', checked);
        jQuery('input[name="box_color"]').each(function () {
            if (jQuery(this).prop('checked')) {
                bg = jQuery(this).val();
            }
        });
        if (bg === 'on') { bg = jQuery('.custom-box-color-text').val(); }
        jQuery('.ap-container').css('background', bg);
    }
    function checkButtonColor() {
        var bg;
        var checked = !jQuery('.custom-button-color').prop('checked');
        jQuery('.custom-button-color-text').prop('disabled', checked);
        jQuery('input[name="button_color"]').each(function () {
            if (jQuery(this).prop('checked')) {
                bg = jQuery(this).val();
            }
        });
        if (bg === 'on') { bg = jQuery('.custom-button-color-text').val(); }
        jQuery('.ap-button').css('background', bg);
    }

    function checkWpmail() {
        var checked = !jQuery('input[name="wpmail_enabled"]').prop('checked');
        // jQuery('input[name="wpmail_alt_email"]').prop('disabled', checked);
    }
    // function checkMandrill() {
    //     var checked = !jQuery('input[name="mandrill_enabled"]').prop('checked');
    //     jQuery('input[name="mandrill_username"]').prop('disabled', checked);
    //     jQuery('input[name="mandrill_api_key"]').prop('disabled', checked);
    //     jQuery('input[name="mandrill_list_id"]').prop('disabled', checked);
    // }

    function checkMailchimp() {
        var checked = !jQuery('input[name="mailchimp_enabled"]').prop('checked');
        jQuery('input[name="mailchimp_api_key"]').prop('disabled', checked);
        jQuery('input[name="mailchimp_list_id"]').prop('disabled', checked);
        jQuery('input[name="mailchimp_double_optin"]').prop('disabled', checked);
        jQuery('input[name="mailchimp_welcome"]').prop('disabled', checked);
    }
    function checkAweber() {
        var checked = !jQuery('input[name="aweber_enabled"]').prop('checked');
        jQuery('input[name="aweber_list_id"]').prop('disabled', checked);
    }
    function checkMailgun() {
        var checked = !jQuery('input[name="mailgun_enabled"]').prop('checked');
        jQuery('input[name="mailgun_api_key"]').prop('disabled', checked);
        jQuery('input[name="mailgun_list_id"]').prop('disabled', checked);
    }
    function checkMadmimi() {
        var checked = !jQuery('input[name="madmimi_enabled"]').prop('checked');
        jQuery('input[name="madmimi_username"]').prop('disabled', checked);
        jQuery('input[name="madmimi_api_key"]').prop('disabled', checked);
        jQuery('input[name="madmimi_list_id"]').prop('disabled', checked);
    }
}); // onload
