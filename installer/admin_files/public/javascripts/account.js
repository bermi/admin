
window.onload = function(){
    if($('login_login')) Field.focus('login_login');
    if($('user_login')){
        Field.focus('user_login');
        Event.observe($('user_login'), 'keyup', Account.handleLoginUniquenessCheck, true);
    }
}

var Account = {
    unavailable_logins: new Array(),
    available_names: new Array(),

    handleLoginUniquenessCheck: function(){
        var login = $('user_login').value;
        if(login.length > 3){
            if(Account.unavailable_logins.indexOf(login) == -1){
                if(Account.available_names.indexOf(login) == -1){
                    Account.checkIfLoginIsAvailable(login);
                }else{
                    Account.informLoginIsAvailable();
                }
            }else{
                Account.informLoginIsNotAvailable();
            }
        }
    },

    checkIfLoginIsAvailable: function(login){
        new Ajax.Request(LOGIN_CHECK_URL+'?login='+login, {
            method: 'get',
            onSuccess: function(transport) {
                if(transport.responseText == '1'){
                    Account.available_names.push(login);
                    Account.informLoginIsAvailable();
                }else{
                    Account.unavailable_logins.push(login);
                    Account.informLoginIsNotAvailable();
                }
            }
        });
    },

    informLoginIsAvailable: function(){
        $('login_check').hide();
    },

    informLoginIsNotAvailable: function(){
        $('login_check').show();
    }
}

