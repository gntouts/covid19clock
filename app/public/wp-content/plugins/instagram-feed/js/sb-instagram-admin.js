jQuery(document).ready(function($) {

    /* NEW API CODE */
    $('.sbi_admin_btn, .sbi_reconnect').click(function(event) {
        event.preventDefault();

        var today = new Date(),
            march = new Date('March 3, 2020 00:00:00'),
            oldApiURL = $(this).attr('data-old-api'),
            oldApiLink = '';
        if (today.getTime() < march.getTime()) {
            oldApiLink = 'To connect using the legacy API, <a href="'+oldApiURL+'">click here</a> (expires on March 2, 2020).';
        }

        var personalBasicApiURL = $('#sbi_config .sbi_admin_btn').attr('data-personal-basic-api'),
            newApiURL = $('#sbi_config .sbi_admin_btn').attr('data-new-api');
        $('#sbi_config').append('<div id="sbi_config_info" class="sb_get_token">' +
            '<div class="sbi_config_modal">' +
            '<p>Are you connecting a Personal or Business Instagram Profile?</p>' +
            '<div class="sbi_login_button_row">' +
            '<input type="radio" id="sbi_basic_login" name="sbi_login_type" value="basic" checked>' +
            '<label for="sbi_basic_login"><b>Personal</b> <a href="JavaScript:void(0);" class="sbi_tooltip_link"><i class="fa fa-question-circle"></i></a><div class="sbi_tooltip">Used for displaying user feeds from a "Personal" Instagram account. ' +
            oldApiLink +
            '</div></div>' +
            '<div class="sbi_login_button_row">' +
            '<input type="radio" id="sbi_business_login" name="sbi_login_type" value="business">' +

            '<label for="sbi_business_login"><b>Business</b> </label>&nbsp;<a href="JavaScript:void(0);" class="sbi_tooltip_link"><i class="fa fa-question-circle"></i></a><div class="sbi_tooltip">Used for displaying a user feed from a "Business" or "Creator" Instagram account. A Business or Creator account is required for displaying automatic avatar/bio display in the header. See <a href="https://smashballoon.com/instagram-business-profiles" target="_blank">this FAQ</a> for more info.</div>' +
            '</div>' +

            '<div class="sbi_login_button_row"><a href="JavaScript:void(0);" class="sbi_tooltip_link" style="font-size: 12px;">I\'m not sure</a><div class="sbi_tooltip" style="display: none;"><p style="margin-top: 0;">The "Personal" option can display feeds from either a Personal or Business/Creator account.</p><p style="margin-bottom: 0;"">Connecting as a Business account will allow your avatar and bio in feed headers to update automatically. If needed, you can convert a Personal account into a Business account by following the directions <a href="https://smashballoon.com/instagram-business-profiles" target="_blank">here</a>.</p></div></div>' +

            '<a href="'+personalBasicApiURL+'" class="sbi_admin_btn">Connect</a>' +
            '<a href="JavaScript:void(0);"><i class="sbi_modal_close fa fa-times"></i></a>' +
            '</div>' +
            '</div>');

        $('.sbi_modal_close').on('click', function(){
            $('#sbi_config_info').remove();
        });

        $('input[name=sbi_login_type]').change(function() {
            if ($('input[name=sbi_login_type]:checked').val() === 'business') {
                $('a.sbi_admin_btn').attr('href',newApiURL);
            } else {
                $('a.sbi_admin_btn').attr('href',personalBasicApiURL);
            }
        });
    });

    if ($('.sbi_config_modal .sbi-managed-pages').length) {
        $('#sbi_config').append($('#sbi_config_info'));
    }

    $('#sbi-select-all').change(function() {
        var status = $(this).is(':checked');
        $('.sbi-add-checkbox input').each(function() {
            $(this).attr('checked',status);
        });
        if($('.sbi-add-checkbox input:checked').length) {
            $('#sbi-connect-business-accounts').removeAttr('disabled');
        } else {
            $('#sbi-connect-business-accounts').attr('disabled',true);
        }
    });

    $('.sbi-add-checkbox input').change(function() {
        if($('.sbi-add-checkbox input:checked').length) {
            $('#sbi-connect-business-accounts').removeAttr('disabled');
        } else {
            $('#sbi-connect-business-accounts').attr('disabled',true);
        }
    });

    $('#sbi-connect-business-accounts').click(function(event) {
        if(typeof $(this).attr('disabled') === 'undefined') {
            event.preventDefault();
            var accounts = {};
            $('.sbi-add-checkbox input').each(function(index) {
                if ($(this).is(':checked')) {
                    var jsonSubmit = JSON.parse($(this).val());
                    jsonSubmit.access_token = $(this).closest('.sbi-managed-page').attr('data-token');
                    jsonSubmit.page_access_token = $(this).closest('.sbi-managed-page').attr('data-page-token');
                    accounts[index] = jsonSubmit;
                }
            });

            $('.sbi_connected_accounts_wrap,#sbi_config_info').fadeTo("slow" , 0.5);
            jQuery.ajax({
                url: sbiA.ajax_url,
                type: 'post',
                data: {
                    action: 'sbi_connect_business_accounts',
                    accounts: JSON.stringify(accounts),
                    sbi_nonce: sbiA.sbi_nonce
                },
                success: function (data) {
                    if (data.trim().indexOf('{') === 0) {
                        var connectedAccounts = JSON.parse(data);
                        $('.sbi_connected_accounts_wrap').fadeTo("slow" , 1);
                        $('#sbi_config_info').remove();
                        $.each(connectedAccounts,function(index,savedToken) {
                            console.log(savedToken);
                            sbiAfterUpdateToken(savedToken,false);

                        });
                    }

                }
            });
        }

    });

    $('.sbi_modal_close').on('click', function(){
        $('#sbi_config_info').remove();
    });
    /* NEW API CODE */
    //Autofill the token and id
    var hash = window.location.hash,
        token = hash.substring(14),
        id = token.split('.')[0];

    if (token.length > 40 && $('.sbi_admin_btn').length) {
        $('.sbi_admin_btn').css('opacity','.5').after('<div class="spinner" style="visibility: visible; position: relative;float: left;margin-top: 15px;"></div>');
        jQuery.ajax({
            url: sbiA.ajax_url,
            type: 'post',
            data: {
                action: 'sbi_after_connection',
                access_token: token,
            },
            success: function (data) {
                if (data.indexOf('{') === 0) {
                    var accountInfo = JSON.parse(data);
                    if (typeof accountInfo.error_message === 'undefined') {
                        accountInfo.token = token;

                        $('.sbi_admin_btn').css('opacity','1');
                        $('#sbi_config').find('.spinner').remove();
                        if (!$('.sbi_connected_account ').length) {
                            $('.sbi_no_accounts').remove();
                            sbSaveToken(token,true);
                        } else {
                            var buttonText = 'Connect This Account';
                            // if the account is connected, offer to update in case information has changed.
                            if ($('#sbi_connected_account_'+id).length) {
                                buttonText = 'Update This Account';
                            }
                            $('#sbi_config').append('<div id="sbi_config_info" class="sb_get_token">' +
                                '<div class="sbi_config_modal">' +
                                '<img class="sbi_ca_avatar" src="'+accountInfo.profile_picture+'" />' +
                                '<div class="sbi_ca_username"><strong>'+accountInfo.username+'</strong></div>' +
                                '<p class="sbi_submit"><input type="submit" name="sbi_submit" id="sbi_connect_account" class="button button-primary" value="'+buttonText+'">' +
                                '<a href="JavaScript:void(0);" class="button button-secondary" id="sbi_switch_accounts">Switch Accounts</a></p>' +
                                '<a href="JavaScript:void(0);"><i class="sbi_modal_close fa fa-times"></i></a>' +
                                '</div>' +
                                '</div>');

                            $('#sbi_connect_account').click(function(event) {
                                event.preventDefault();
                                $('#sbi_config_info').fadeOut(200);
                                sbSaveToken(token,false);
                            });

                            sbiSwitchAccounts();
                        }
                    } else {
                        $('.sbi_admin_btn').css('opacity','1');
                        $('#sbi_config').find('.spinner').remove();
                        var message = accountInfo.error_message;

                        $('#sbi_config').append('<div id="sbi_config_info" class="sb_get_token">' +
                            '<div class="sbi_config_modal">' +
                            '<p>'+message+'</p>' +
                            '<p class="sbi_submit"><a href="JavaScript:void(0);" class="button button-secondary" id="sbi_switch_accounts">Switch Accounts</a></p>' +
                            '<a href="JavaScript:void(0);"><i class="sbi_modal_close fa fa-times"></i></a>' +
                            '</div>' +
                            '</div>');

                        sbiSwitchAccounts();
                    }

                } else {
                    $('.sbi_admin_btn').css('opacity','1');
                    $('#sbi_config').find('.spinner').remove();
                    var message = 'There was an error connecting your account';

                    $('#sbi_config').append('<div id="sbi_config_info" class="sb_get_token">' +
                        '<div class="sbi_config_modal">' +
                        '<p>'+message+'</p>' +
                        '<p class="sbi_submit"><a href="JavaScript:void(0);" class="button button-secondary" id="sbi_switch_accounts">Switch Accounts</a></p>' +
                        '<a href="JavaScript:void(0);"><i class="sbi_modal_close fa fa-times"></i></a>' +
                        '</div>' +
                        '</div>');

                    sbiSwitchAccounts();
                }

            }
        });

        window.location.hash = '';
    }
    function sbiSwitchAccounts(){
        $('#sbi_switch_accounts').on('click', function(){
            //Log user out of Instagram by hitting the logout URL in an iframe
            $('body').append('<iframe style="display: none;" src="https://www.instagram.com/accounts/logout"></iframe>');

            $(this).text('Please wait...').after('<div class="spinner" style="visibility: visible; float: none; margin: -3px 0 0 3px;"></div>');

            //Wait a couple seconds for the logout to occur, then connect a new account
            setTimeout(function(){
                window.location.href = $('.sbi_admin_btn').attr('href');
            }, 2000);
        });

        $('.sbi_modal_close').on('click', function(){
            $('#sbi_config_info').remove();
        });
    }
    if ($('#sbi_switch_accounts').length) {
        $('.sbi_admin_btn').attr('href',$('#sbi_config .sbi_admin_btn').attr('data-personal-basic-api'));
        sbiSwitchAccounts();
    }

    function sbiAfterUpdateToken(savedToken,saveID){
        if (saveID) {
            sbSaveID(savedToken.user_id);
            $('.sbi_user_feed_ids_wrap').prepend(
                '<div id="sbi_user_feed_id_'+savedToken.user_id+'" class="sbi_user_feed_account_wrap">'+
                '<strong>'+savedToken.username+'</strong> <span>('+savedToken.user_id+')</span>' +
                '<input type="hidden" name="sb_instagram_user_id[]" value="'+savedToken.user_id+'">' +
                '</div>'
            );
        }
        if (typeof savedToken.old_user_id !== 'undefined' && $('#sbi_connected_account_'+savedToken.old_user_id).length) {

            if ($('#sbi_user_feed_id_'+savedToken.old_user_id).length) {
                $('.sbi_user_feed_ids_wrap').prepend(
                    '<div id="sbi_user_feed_id_'+savedToken.user_id+'" class="sbi_user_feed_account_wrap">'+
                    '<strong>'+savedToken.username+'</strong> <span>('+savedToken.user_id+')</span>' +
                    '<input type="hidden" name="sb_instagram_user_id[]" value="'+savedToken.user_id+'">' +
                    '</div>'
                );
                $('#sbi_user_feed_id_'+savedToken.old_user_id).remove();

                saveID = true;
            }

            $('#sbi_connected_account_'+savedToken.old_user_id).remove();
        }
        if ($('#sbi_connected_account_'+savedToken.user_id).length) {
            if (savedToken.is_valid) {
                $('#sbi_connected_account_'+savedToken.user_id).addClass('sbi_account_updated');
            } else {
                $('#sbi_connected_account_'+savedToken.user_id).addClass('sbi_account_invalid');
            }
            $('#sbi_connected_account_'+savedToken.user_id).attr('data-accesstoken',savedToken.access_token);
            if (typeof savedToken.use_tagged !== 'undefined' && savedToken.use_tagged == '1') {
                $('#sbi_connected_account_'+savedToken.user_id).attr('data-permissions','tagged');
                $('#sbi_connected_account_'+savedToken.user_id).find('.sbi_permissions_desc').text('All');
            }

            if (! $('#sbi_connected_account_'+savedToken.user_id + ' .sbi_ca_avatar').length) {
                if (savedToken.profile_picture !== '') {
                    $('#sbi_connected_account_'+savedToken.user_id + ' .sbi_ca_username').prepend('<img class="sbi_ca_avatar" src="'+savedToken.profile_picture+'">');
                }
            }
            $('#sbi_connected_account_'+savedToken.user_id + ' .sbi_ca_username').find('span').text(sbiAccountType(savedToken.type));

            $('#sbi_connected_account_'+savedToken.user_id).find('.sbi_ca_accesstoken .sbi_ca_token').text(savedToken.access_token);
            $('#sbi_connected_account_'+savedToken.user_id).find('.sbi_tooltip code').text('[instagram-feed accesstoken="'+savedToken.access_token+'"]');

        } else {
            //Check which kind of account it is
            if(typeof savedToken.type !== 'undefined'){
                var accountType = savedToken.type;
                $('.sbi_hashtag_feed_issue').removeClass('sbi_hashtag_feed_issue').find('.sbi_hashtag_feed_issue_note').hide();
            } else {
                var accountType = 'personal';
            }

            var avatarHTML = '';
            if (savedToken.profile_picture !== '') {
                avatarHTML = '<img class="sbi_ca_avatar" src="'+savedToken.profile_picture+'" />';
            }

            //Add the account HTML to the page
            var removeOrSaveHTML = saveID ? '<a href="JavaScript:void(0);" class="sbi_remove_from_user_feed button-primary"><i class="fa fa-minus-circle" aria-hidden="true"></i>Remove from Primary Feed</a>' : '<a href="JavaScript:void(0);" class="sbi_use_in_user_feed button-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i>Add to Primary Feed</a>',
                statusClass = saveID ? 'sbi_account_active' : 'sbi_account_updated',
                html = '<div class="sbi_connected_account '+statusClass+' sbi-init-click-remove" id="sbi_connected_account_'+savedToken.user_id+'" data-accesstoken="'+savedToken.access_token+'" data-userid="'+savedToken.user_id+'" data-username="'+savedToken.username+'">'+
                    '<div class="sbi_ca_info">'+

                    '<div class="sbi_ca_delete">'+
                    '<a href="JavaScript:void(0);" class="sbi_delete_account"><i class="fa fa-times"></i><span class="sbi_remove_text">Remove</span></a>'+
                    '</div>'+

                    '<div class="sbi_ca_username">'+
                    avatarHTML+
                    '<strong>'+savedToken.username+'<span>'+sbiAccountType(accountType)+'</span></strong>'+
                    '</div>'+

                    '<div class="sbi_ca_actions">'+
                    removeOrSaveHTML +
                    '<a class="sbi_ca_token_shortcode button-secondary" href="JavaScript:void(0);"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i>Add to another Feed</a>'+
                    '<a class="sbi_ca_show_token button-secondary" href="JavaScript:void(0);" title="Show access token and account info"><i class="fa fa-cog"></i></a>'+
                    '</div>'+

                    '<div class="sbi_ca_shortcode">'+
                    '<p>Copy and paste this shortcode into your page or widget area:<br>'+
                    '<code>[instagram-feed user="'+savedToken.username+'"]</code>'+
                    '</p>'+
                    '<p>To add multiple users in the same feed, simply separate them using commas:<br>'+
                    '<code>[instagram-feed user="'+savedToken.username+', a_second_user, a_third_user"]</code>'+
                    '<p>Click on the <a href="?page=sb-instagram-feed&tab=display" target="_blank">Display Your Feed</a> tab to learn more about shortcodes</p>'+
                    '</div>'+

                    '<div class="sbi_ca_accesstoken">' +
                    '<span class="sbi_ca_token_label">Access Token:</span><input type="text" class="sbi_ca_token" value="'+savedToken.access_token+'" readonly="readonly" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac)."><br>' +
                    '<span class="sbi_ca_token_label">User ID:</span><input type="text" class="sbi_ca_user_id" value="'+savedToken.user_id+'" readonly="readonly" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac)."><br>' +
                    '<span class="sbi_ca_token_label">Permissions:</span><span class="sbi_permissions_desc">All</span>' +
                    '</div>' +

                    '</div>'+
                    '</div>';
            $('.sbi_connected_accounts_wrap').prepend(html);
            var $clickRemove = $('.sbi-init-click-remove');
            sbiInitClickRemove($clickRemove.find('.sbi_delete_account'));
            if ($clickRemove.find('.sbi_remove_from_user_feed').length ) {
                $clickRemove.find('.sbi_remove_from_user_feed').off();
                sbiInitUserRemove($clickRemove.find('.sbi_remove_from_user_feed'));
            } else {
                $clickRemove.find('.sbi_use_in_user_feed').off();
                sbiInitUserAdd($clickRemove.find('.sbi_use_in_user_feed'));
            }
            $clickRemove.removeClass('sbi-init-click-remove');
        }
    }

    function sbSaveToken(token,saveID) {
        $('.sbi_connected_accounts_wrap').fadeTo("slow" , 0.5);
        jQuery.ajax({
            url: sbiA.ajax_url,
            type: 'post',
            data: {
                action: 'sbi_auto_save_tokens',
                access_token: token,
                just_tokens: true,
                sbi_nonce: sbiA.sbi_nonce
            },
            success: function (data) {
                var savedToken = JSON.parse(data);
                $('.sbi_connected_accounts_wrap').fadeTo("slow" , 1);
                sbiAfterUpdateToken(savedToken,saveID);
            }
        });
    }

    function sbiAccountType(accountType) {
        if (accountType === 'basic') {
            return 'personal (new API)';
        }
        return accountType;
    }

    function sbSaveID(ID) {
        jQuery.ajax({
            url: sbiA.ajax_url,
            type: 'post',
            data: {
                action: 'sbi_auto_save_id',
                id: ID,
                just_tokens: true,
                sbi_nonce: sbiA.sbi_nonce
            },
            success: function (data) {
            }
        });
    }

    // connect accounts
    //sbi-bus-account-error
    if (window.location.hash && window.location.hash === '#test') {
        window.location.hash = '';
        $('#sbi-bus-account-error').html('<p style="margin-top: 5px;"><b style="font-size: 16px">Couldn\'t connect an account with this access token</b><br />' +
            'Please check to make sure that the token you entered is correct.</p>')
    }

    $('.sbi_manually_connect_wrap').hide();
    $('.sbi_manually_connect').click(function(event) {
        event.preventDefault();
        if ( $('.sbi_manually_connect_wrap').is(':visible') ) {
            $('.sbi_manually_connect_wrap').slideUp(200);
        } else {
            $('.sbi_manually_connect_wrap').slideDown(200);
            $('#sb_manual_at').focus();
        }
    });

    $('#sb_manual_at').on('input',function() {
        sbiToggleManualAccountIDInput();
    });
    if ($('#sb_manual_at').length){
        sbiToggleManualAccountIDInput();
    }

    function sbiIsBusinessToken() {
        return ($('#sb_manual_at').val().trim().length > 125);
    }

    function sbiToggleManualAccountIDInput() {
        if (sbiIsBusinessToken()) {
            $('.sbi_manual_account_id_toggle').slideDown();
            $('.sbi_business_profile_tag').css('display', 'inline-block');
        } else {
            $('.sbi_manual_account_id_toggle').slideUp();
        }
    }

    var $body = $('body');
    $body.on('click', '.sbi_test_token, .sbi_ca_token_shortcode', function (event) {
        event.preventDefault();
        var $clicked = $(event.target),
            accessToken = $clicked.closest('.sbi_connected_account').attr('data-accesstoken'),
            action = false,
            atParts = accessToken.split('.'),
            username = $clicked.closest('.sbi_connected_account').attr('data-username'),
            accountID = $clicked.closest('.sbi_connected_account').attr('data-userid');
        if ($clicked.hasClass('sbi_ca_token_shortcode')) {
            jQuery(this).closest('.sbi_ca_info').find('.sbi_ca_shortcode').slideToggle(200);
        } //

    });

    $('.sbi_delete_account').each(function() {
        sbiInitClickRemove($(this));
    });

    function sbiInitClickRemove(el) {
        el.click(function() {
            if (!$(this).closest('.sbi_connected_accounts_wrap').hasClass('sbi-waiting')) {
                $(this).closest('.sbi_connected_accounts_wrap').addClass('sbi-waiting');
                var accessToken = $(this).closest('.sbi_connected_account').attr('data-accesstoken'),
                    action = false,
                    atParts = accessToken.split('.'),
                    username = $(this).closest('.sbi_connected_account').attr('data-username'),
                    accountID = $(this).closest('.sbi_connected_account').attr('data-userid');

                if (window.confirm("Delete this connected account?")) {
                    action = 'sbi_delete_account';
                    $('#sbi_user_feed_id_' + accountID).remove();
                    $('#sbi_tagged_feed_id_' + accountID).remove();
                    $('#sbi_connected_account_' + accountID).append('<div class="spinner" style="margin-top: -10px;visibility: visible;top: 50%;position: absolute;right: 50%;"></div>').find('.sbi_ca_info').css('opacity','.5');

                    jQuery.ajax({
                        url: sbiA.ajax_url,
                        type: 'post',
                        data: {
                            action: action,
                            account_id: accountID,
                            sbi_nonce: sbiA.sbi_nonce
                        },
                        success: function (data) {
                            $('.sbi-waiting').removeClass('sbi-waiting');
                            $('#sbi_connected_account_' + accountID).fadeOut(300, function() { $(this).remove(); });
                        }
                    });
                } else {
                    $('.sbi-waiting').removeClass('sbi-waiting');
                }
            }

        });
    }

    $('.sbi_remove_from_user_feed').each(function() {
        sbiInitUserRemove($(this));
    });

    function sbiInitUserRemove(el,targetClass) {
        el.click(function(event) {
            event.preventDefault();
            targetClass = $('input[name=sb_instagram_type]:checked').val();

            var $clicked = $(this),
                accountID = $clicked.closest('.sbi_connected_account').attr('data-userid');

            $('#sbi_'+targetClass+'_feed_id_'+accountID).remove();

            sbiConAccountsAddRemoveUpdater();
        });
    }



    $('.sbi_use_in_user_feed').each(function() {
        sbiInitUserAdd($(this), 'user');
    });

    function sbiInitUserAdd(el,targetClass) {
        el.click(function(event) {
            targetClass = $('input[name=sb_instagram_type]:checked').val();
            event.preventDefault();
            var $clicked = $(this),
                $closest = $clicked.closest('.sbi_connected_account'),
                username = $clicked.closest('.sbi_connected_account').attr('data-username'),
                accountID = $clicked.closest('.sbi_connected_account').attr('data-userid');

            var name = '<strong>'+accountID+'</strong>';
            if (username !== '') {
                name = '<strong>'+username+'</strong> <span>('+accountID+')</span>';
            }
            $('.sbi_'+targetClass+'_feed_ids_wrap').prepend(
                '<div id="sbi_'+targetClass+'_feed_id_'+accountID+'" class="sbi_'+targetClass+'_feed_account_wrap">'+
                name +
                '<input type="hidden" name="sb_instagram_'+targetClass+'_id[]" value="'+accountID+'">' +
                '</div>'
            );
            $('.sbi_no_accounts').hide();
            sbiConAccountsAddRemoveUpdater();
        });
    }

    function sbiConAccountsAddRemoveUpdater() {
        var targetClass = $('input[name=sb_instagram_type]:checked').val();

        var isSelected = [];
        $('.sbi_'+targetClass+'_feed_account_wrap').find('input').each(function() {
            isSelected.push($(this).val());
        });

        $('.sbi_connected_account').each(function() {
            var username = $(this).attr('data-username'),
                accountID = $(this).attr('data-userid'),
                type = $(this).attr('data-type'),
                permissions = $(this).attr('data-permissions'),
                $addRemoveButton = $(this).find('.sbi_ca_actions .button-primary').first();
            $(this).removeClass('sbi_account_updated');
            $addRemoveButton.removeAttr('disabled');

            if (targetClass === 'tagged' && (type === 'personal' || permissions !== 'tagged')) {
                $addRemoveButton.show();
                if (type === 'personal') {
                    $addRemoveButton.html('Tagged Feeds Not Supported');
                } else {
                    $addRemoveButton.html('Reconnect Account');
                }
                $addRemoveButton.attr('disabled',true).addClass('sbi_remove_from_user_feed').removeClass('sbi_use_in_user_feed');
                $(this).removeClass('sbi_account_active');
            } else if (targetClass === 'hashtag') {
                $addRemoveButton.hide();
                $addRemoveButton.attr('disabled',true).addClass('sbi_remove_from_user_feed').removeClass('sbi_use_in_user_feed');
                $(this).removeClass('sbi_account_active');
            } else {
                $addRemoveButton.show();
                if (isSelected.indexOf(accountID) > -1) {
                    $addRemoveButton.html('<i class="fa fa-minus-circle" aria-hidden="true" style="margin-right: 5px;"></i>Remove from Primary Feed');
                    $addRemoveButton.addClass('sbi_remove_from_user_feed').removeClass('sbi_use_in_user_feed');
                    $(this).addClass('sbi_account_active');
                } else {
                    $addRemoveButton.html('<i class="fa fa-plus-circle" aria-hidden="true"></i>Add to Primary Feed');
                    $addRemoveButton.removeClass('sbi_remove_from_user_feed');
                    $addRemoveButton.addClass('sbi_use_in_user_feed');
                    $(this).removeClass('sbi_account_active');
                }
            }


            if ($(this).find('.sbi_remove_from_user_feed').length ) {
                $(this).find('.sbi_remove_from_user_feed').off();
                sbiInitUserRemove($(this).find('.sbi_remove_from_user_feed'));
            } else {
                $(this).find('.sbi_use_in_user_feed').off();
                sbiInitUserAdd($(this).find('.sbi_use_in_user_feed'),'user');
            }

        });
    }sbiConAccountsAddRemoveUpdater();

    $('input[name=sb_instagram_type]').change(sbiConAccountsAddRemoveUpdater);



    $body.on('click', '.sbi_ca_show_token', function(event) {
        jQuery(this).closest('.sbi_ca_info').find('.sbi_ca_accesstoken').slideToggle(200);
    });

    $('#sbi_manual_submit').click(function(event) {
        event.preventDefault();
        var $self = $(this);
        var accessToken = $('#sb_manual_at').val(),
            error = false;
        if (sbiIsBusinessToken() && $('.sbi_manual_account_id_toggle').find('input').val().length < 3) {
            error = true;
            if (!$('.sbi_manually_connect_wrap').find('.sbi_user_id_error').length) {
                $('.sbi_manually_connect_wrap').show().prepend('<div class="sbi_user_id_error" style="display:block;">Please enter a valid User ID for this Business account.</div>');
            }
        } else {
            error = false;
        }
        if (accessToken.length < 15) {
            if (!$('.sbi_manually_connect_wrap').find('.sbi_user_id_error').length) {
                $('.sbi_manually_connect_wrap').show().prepend('<div class="sbi_user_id_error" style="display:block;">Please enter a valid access token</div>');
            }
        } else if (! error) {
            $(this).attr('disabled',true);
            $(this).closest('.sbi_manually_connect_wrap').fadeOut();
            $('.sbi_connected_accounts_wrap').fadeTo("slow" , 0.5).find('.sbi_user_id_error').remove();

            jQuery.ajax({
                url: sbiA.ajax_url,
                type: 'post',
                data: {
                    action: 'sbi_test_token',
                    access_token: accessToken,
                    account_id : $('.sbi_manual_account_id_toggle').find('input').val().trim(),
                    sbi_nonce: sbiA.sbi_nonce
                },
                success: function (data) {
                    $('.sbi_connected_accounts_wrap').fadeTo("slow" , 1);
                    $self.removeAttr('disabled');
                    if ( data.indexOf('{') > -1) {
                        var savedToken = JSON.parse(data);
                        if (typeof savedToken.url !== 'undefined') {
                            window.location.href = savedToken.url;
                        } else {
                            $(this).closest('.sbi_manually_connect_wrap').fadeOut();
                            $('#sb_manual_at, .sbi_manual_account_id_toggle input').val('');
                            sbiAfterUpdateToken(savedToken,false);
                        }

                    } else {
                        $('.sbi_manually_connect_wrap').show().prepend('<div class="sbi_user_id_error" style="display:block;">'+data+'</div>');
                    }

                }
            });
        }

    });

    //sbi_reset_resized
    // clear resized
    var $sbiClearResizedButton = $('#sbi_reset_resized');

    $sbiClearResizedButton.click(function(event) {
        event.preventDefault();

        jQuery('#sbi-clear-cache-success').remove();
        jQuery(this).prop("disabled",true);

        $.ajax({
            url : sbiA.ajax_url,
            type : 'post',
            data : {
                action : 'sbi_reset_resized'
            },
            success : function(data) {
                $sbiClearResizedButton.prop('disabled',false);
                if(data=='1') {
                    $sbiClearResizedButton.after('<i id="sbi-clear-cache-success" class="fa fa-check-circle sbi-success"></i>');
                } else {
                    $sbiClearResizedButton.after('<span>error</span>');
                }
            }
        }); // ajax call
    }); // clear_comment_cache click

    //Caching options
    if( jQuery('#sbi_caching_type_page').is(':checked') ) {
        jQuery('.sbi-caching-cron-options').hide();
        jQuery('.sbi-caching-page-options').show();
    } else {
        jQuery('.sbi-caching-page-options').hide();
        jQuery('.sbi-caching-cron-options').show();
    }

    $('input[type=radio][name=sbi_caching_type]').change(function() {
        if (this.value == 'page') {
            jQuery('.sbi-caching-cron-options').slideUp();
            jQuery('.sbi-caching-page-options').slideDown();
        }
        else if (this.value == 'background') {
            jQuery('.sbi-caching-page-options').slideUp();
            jQuery('.sbi-caching-cron-options').slideDown();
        }
    });


    //Should we show the caching time settings?
    var sbi_cache_cron_interval = jQuery('#sbi_cache_cron_interval').val(),
        $sbi_caching_time_settings = jQuery('#sbi-caching-time-settings');

    //Should we show anything initially?
    if(sbi_cache_cron_interval == '30mins' || sbi_cache_cron_interval == '1hour') $sbi_caching_time_settings.hide();

    jQuery('#sbi_cache_cron_interval').change(function(){
        sbi_cache_cron_interval = jQuery('#sbi_cache_cron_interval').val();

        if(sbi_cache_cron_interval == '30mins' || sbi_cache_cron_interval == '1hour'){
            $sbi_caching_time_settings.hide();
        } else {
            $sbi_caching_time_settings.show();
        }
    });
    sbi_cache_cron_interval = jQuery('#sbi_cache_cron_interval').val();

    if(sbi_cache_cron_interval == '30mins' || sbi_cache_cron_interval == '1hour'){
        $sbi_caching_time_settings.hide();
    } else {
        $sbi_caching_time_settings.show();
    }


    //clear backup caches
    jQuery('#sbi_clear_backups').click(function(event) {
        jQuery('.sbi-success').remove();
        event.preventDefault();
        jQuery.ajax({
            url: sbiA.ajax_url,
            type: 'post',
            data: {
                action: 'sbi_clear_backups',
                access_token: token,
                sbi_nonce : sbiA.sbi_nonce,
                just_tokens: true
            },
            success: function (data) {
                jQuery('#sbi_clear_backups').after('<span class="sbi-success"><i class="fa fa-check-circle"></i></span>');
            }
        });
    });

    //sbi_reset_log
    var $sbiClearLog = $('#sbi_reset_log');

    $sbiClearLog.click(function(event) {
        event.preventDefault();

        jQuery('#sbi-clear-cache-success').remove();
        jQuery(this).prop("disabled",true);

        $.ajax({
            url : sbiA.ajax_url,
            type : 'post',
            data : {
                action : 'sbi_reset_log'
            },
            success : function(data) {
                $sbiClearLog.prop('disabled',false);
                if(data=='1') {
                    $sbiClearLog.after('<i id="sbi-clear-cache-success" class="fa fa-check-circle sbi-success"></i>');
                } else {
                    $sbiClearLog.after('<span>error</span>');
                }
            }
        }); // ajax call
    }); // clear_comment_cache click
	
	//Tooltips
    jQuery('#sbi_admin').on('click', '.sbi_tooltip_link, .sbi_type_tooltip_link', function(){
        if( jQuery(this).hasClass('sbi_type_tooltip_link') ){
            jQuery(this).closest('.sbi_row').children('.sbi_tooltip').slideToggle();
        } else {
            jQuery(this).siblings('.sbi_tooltip').slideToggle();
        }
    });

	//Shortcode labels
	jQuery('#sbi_admin label').click(function(){
    var $sbi_shortcode = jQuery(this).siblings('.sbi_shortcode');
    if($sbi_shortcode.is(':visible')){
      jQuery(this).siblings('.sbi_shortcode').css('display','none');
    } else {
      jQuery(this).siblings('.sbi_shortcode').css('display','block');
    }  
  });
  jQuery('#sbi_admin label').hover(function(){
    if( jQuery(this).siblings('.sbi_shortcode').length > 0 ){
      jQuery(this).attr('title', 'Click for shortcode option').append('<code class="sbi_shortcode_symbol">[]</code>');
    }
  }, function(){
    jQuery(this).find('.sbi_shortcode_symbol').remove();
  });


  jQuery('#sbi_admin .sbi_lock').hover(function(){
    jQuery(this).siblings('.sbi_pro_tooltip').show();
  }, function(){
    jQuery('.sbi_pro_tooltip').hide();
  });

  


  //Add the color picker
	if( jQuery('.sbi_colorpick').length > 0 ) jQuery('.sbi_colorpick').wpColorPicker();

	//Check User ID is numeric
	jQuery("#sb_instagram_user_id").change(function() {

		var sbi_user_id = jQuery('#sb_instagram_user_id').val(),
			$sbi_user_id_error = $(this).closest('td').find('.sbi_user_id_error'),
			$sbi_other_user_error = $(this).closest('td').find('.sbi_other_user_error');

		if (sbi_user_id.match(/[^0-9, _.-]/)) {
  			$sbi_user_id_error.fadeIn();
  		} else {
  			$sbi_user_id_error.fadeOut();
  		}

  		//Check whether an ID from another account is being used
  		sbi_check_other_user_id(sbi_user_id, $sbi_other_user_error);

	});
	function sbi_check_other_user_id(sbi_user_id, $sbi_other_user_error){
		if (jQuery('#sb_instagram_at').length && jQuery('#sb_instagram_at').val() !== '' && sbi_user_id.length) {
            if(jQuery('#sb_instagram_at').val().indexOf(sbi_user_id) == -1 ){
                $sbi_other_user_error.fadeIn();
            } else {
                $sbi_other_user_error.fadeOut();
            }
		}
	}
	//Check initially when settings load
	sbi_check_other_user_id( jQuery('#sb_instagram_user_id').val(), $('td').find('.sbi_other_user_error') );

	//Mobile width
	var sb_instagram_feed_width = jQuery('#sbi_admin #sb_instagram_width').val(),
			sb_instagram_width_unit = jQuery('#sbi_admin #sb_instagram_width_unit').val(),
			$sb_instagram_width_options = jQuery('#sbi_admin #sb_instagram_width_options');

	if (typeof sb_instagram_feed_width !== 'undefined') {

		//Show initially if a width is set
		if( (sb_instagram_feed_width.length > 1 && sb_instagram_width_unit == 'px') || (sb_instagram_feed_width !== '100' && sb_instagram_width_unit == '%') ) $sb_instagram_width_options.show();

		jQuery('#sbi_admin #sb_instagram_width, #sbi_admin #sb_instagram_width_unit').change(function(){
			sb_instagram_feed_width = jQuery('#sbi_admin #sb_instagram_width').val();
			sb_instagram_width_unit = jQuery('#sbi_admin #sb_instagram_width_unit').val();

			if( sb_instagram_feed_width.length < 2 || (sb_instagram_feed_width == '100' && sb_instagram_width_unit == '%') ) {
				$sb_instagram_width_options.slideUp();			
			} else {
				$sb_instagram_width_options.slideDown();
			}
		});

	}

	//Scroll to hash for quick links
  jQuery('#sbi_admin a').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = jQuery(this.hash);
      target = target.length ? target : this.hash.slice(1);
      if (target.length) {
        jQuery('html,body').animate({
          scrollTop: target.offset().top
        }, 500);
        return false;
      }
    }
  });

	//Support tab show video
	jQuery('#sbi-play-support-video').on('click', function(e){
		e.preventDefault();
		jQuery('#sbi-support-video').show().attr('src', jQuery('#sbi-support-video').attr('src')+'&amp;autoplay=1' );
	});

	jQuery('#sbi_admin .sbi-show-pro').on('click', function(){
		jQuery(this).parent().next('.sbi-pro-options').toggle();
	});

	/* Pro 3.0 JS */
    function sbiUpdateLayoutTypeOptionsDisplay() {
        setTimeout(function(){
            jQuery('.sb_instagram_layout_settings').hide();
            jQuery('.sb_instagram_layout_settings.sbi_layout_type_'+jQuery('.sb_layout_type:checked').val()).show();
        }, 1);
    }
    jQuery('.sb_layout_type').change(sbiUpdateLayoutTypeOptionsDisplay);

    jQuery('.sbi_close_options').on('click', function(){
        jQuery('.sb_instagram_layout_settings').hide();
    });

    function sbiUpdateHighlightOptionsDisplay() {
        jQuery('.sb_instagram_highlight_sub_options').hide();
        var selected = jQuery('#sb_instagram_highlight_type').val();

        if (selected === 'pattern') {
            jQuery('.sb_instagram_highlight_pattern').show();
        } else if (selected === 'id') {
            jQuery('.sb_instagram_highlight_ids').show();
        } else {
            jQuery('.sb_instagram_highlight_hashtag').show();
        }

    }
    sbiUpdateHighlightOptionsDisplay();
    jQuery('#sb_instagram_highlight_type').change(sbiUpdateHighlightOptionsDisplay);

    //Open/close the expandable option sections
    jQuery('.sbi-expandable-options').hide();
    jQuery('.sbi-expand-button a').on('click', function(e){
        e.preventDefault();
        var $self = jQuery(this);
        $self.parent().next('.sbi-expandable-options').toggle();
        if( $self.text().indexOf('Show') !== -1 ){
            $self.text( $self.text().replace('Show', 'Hide') );
        } else {
            $self.text( $self.text().replace('Hide', 'Show') );
        }
    });

    //Selecting a post layout
    jQuery('.sbi_layout_cell').click(function(){
        var $self = jQuery(this);
        $('.sb_layout_type').trigger('change');
        $self.addClass('sbi_layout_selected').find('.sb_layout_type').attr('checked', 'checked');
        $self.siblings().removeClass('sbi_layout_selected');
    });

    setTimeout( function() {
        jQuery('.notice-dismiss').click(function() {
            if (jQuery(this).closest('.sbi-admin-notice').length) {

                if (jQuery(this).closest('.sbi-admin-notice').find('.sbi-admin-error').length) {

                    var exemptErrorType = jQuery(this).closest('.sbi-admin-notice').find('.sbi-admin-error').attr('data-sbi-type');

                    if (exemptErrorType === 'ajax') {
                        jQuery.ajax({
                            url: sbiA.ajax_url,
                            type: 'post',
                            data: {
                                action : 'sbi_on_ajax_test_trigger',
                                sbi_nonce: sbiA.sbi_nonce
                            },
                            success: function (data) {
                            }
                        });
                    }
                }
            }
        });
    },1500);

    //Load the admin share widgets
    jQuery('#sbi_admin_show_share_links').on('click', function(){
        jQuery(this).fadeOut();
        if( jQuery('#sbi_admin_share_links iframe').length == 0 ) jQuery('#sbi_admin_share_links').html('<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://wordpress.org/plugins/instagram-feed/" data-text="Display beautifully clean, customizable, and responsive Instagram feeds from multiple accounts" data-via="smashballoon" data-dnt="true">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");</script> <style type="text/css"> #twitter-widget-0{float: left; width: 82px !important;}.IN-widget{margin-right: 20px;}</style> <div id="fb-root" style="display: none;"></div><script>(function(d, s, id){var js, fjs=d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js=d.createElement(s); js.id=id; js.src="//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.0"; fjs.parentNode.insertBefore(js, fjs);}(document, "script", "facebook-jssdk"));</script> <div class="fb-like" data-href="https://wordpress.org/plugins/instagram-feed/" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true" style="display: block; float: left; margin-right: 5px;"></div><script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US </script> <script type="IN/Share" data-url="https://wordpress.org/plugins/instagram-feed/"></script></div>');

        setTimeout(function(){
            jQuery('#sbi_admin_share_links').addClass('sbi_show');
        }, 500);
    });

});