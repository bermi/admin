<!doctype html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>{_controller_name} &gt; {_action}</title>

    <meta name="viewport" content="width=device-width,initial-scale=1">

    {content_for_head?}
    <%= stylesheet_link_tag "#{module_name}/admin.css", :media=>'print,screen' %>
    <%= stylesheet_link_tag "#{module_name}/menu.css" %>
    <%= stylesheet_for_current_controller %>
    
    {content_for_head_after_styles?}
    <%= javascript_include_tag 'modernizr-2.0.6.min.js' %>
</head>
<body>
    <div id="container">
        <div id="layout">
            <div id="canvas">
              <header id="header">
                  {?admin_settings-application_name}
                    <div id="site_name"><h1>{admin_settings-application_name}</h1></div>
                  {end}
                  <div id="user_menu"><%= user_menu %></div>
                  <div class="cls"></div>
              </header>
              <div id="menu" class="{!controller_menu_options}simple{end}">
                  <%= admin_menu %>
                  {?content_for_controller_menu}
                      {content_for_controller_menu}
                  {else}
                    {?controller_menu_options}
                       <h2>{_controller_name}</h2>
                       <%= controller_menu %>
                    {end}
                  {end}
              </div>

              <%= flash %>
              <div id="main" role="main">
                  {content_for_layout}
              </div>
            </div>
            <div class="cls"></div>
        </div>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<%= javascript_path 'jquery-1.6.2.min.js' %>"><\/script>')</script>

    <%= javascript_include_tag %>
    <%= javascript_for_current_controller %>

    <script type="text/javascript">
    // <![CDATA[
    {content_for_script?}
    // ]]>
    </script>

    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
        <script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
    <![endif]-->

</body>
</html>