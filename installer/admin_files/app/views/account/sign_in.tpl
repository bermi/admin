<form action="<%= url_for settings-authentication_url %>" method="post">
  <h2 class="form_header">
    _{Please Sign in}
  </h2>
  <p class="form_info">
    _{Please insert your username and password}
  </p>
  <div class="form">
    <fieldset>
      <label for="login_username" id="login_username_label" class="required">_{User name or email}</label>
      <input id="login_username" name="ak_login[login]" size="30" type="text" value="{ak_login-username?}" />
    </fieldset>
    <fieldset>
      <label for="login_password" id="login_password_label" class="required">_{Password}</label>
      <input id="login_password" name="ak_login[password]" size="30" type="password" value="{ak_login-password?}" />
    </fieldset>
    </div>
    
    <div id="operations">
      <input type="submit" value="_{Sign in}" class="primary" />
      {?settings-account_settings-allow_sign_up}
      _{or} <%= link_to _'sign up for an account', :action => 'sign_up' %>
      {end}
    </div>
</form>


{?flash-error}                    
<p id="password_reminder" class="notice">
    <%= link_to _'Forgot your user name or password?', :action => 'password_reminder' %>
</p>
{end}