<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
      {settings-application_name}
    </title>
    <%= javascript_include_tag %>
    <%= javascript_for_current_controller %>
    <%= stylesheet_link_tag "account.css", :media=>'print,screen' %>
  </head>
  <body>
    <div id="layout">
      <div id="canvas" class="login">

      <div id="header">
        <div id="site_name">
          <h1>
            {settings-application_name}
          </h1>
        </div>
         <div id="user_menu">
              <%= link_to _'sign in', :action => 'sign_in' %>
              {?settings-account_settings-allow_sign_up}
              _{or}
                  <%= link_to _'sign up', :action => 'sign_up' %>
              {end}
          </div>
          <div class="cls"></div>
      </div>

        <div class="cls"></div>
        <div id="content">
          <%= flash %>
          {content_for_layout?}
        </div>
      </div>
    </div>
  </body>
</html>
