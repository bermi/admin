<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{_controller_name} &gt; {_action}</title>
     
    {content_for_head?}
    <%= stylesheet_link_tag "#{module_name}/admin.css", :media=>'print,screen' %>
    <%= stylesheet_link_tag "#{module_name}/menu.css" %>
    <%= stylesheet_for_current_controller %>
    
    {content_for_head_after_styles?}
    
    <%= javascript_include_tag %>
    <%= javascript_for_current_controller %>
    
    <script type="text/javascript">
    // <![CDATA[
    {content_for_script?}
    // ]]>
    </script>
    
    {content_for_head_after_scripts?}
    
</head>
<body>
<div id="layout">
    <div id="canvas">
      <div id="header">
          {?admin_settings-application_name}
            <div id="site_name"><h1>{admin_settings-application_name}</h1></div>
          {end}
          <div id="user_menu"><%= user_menu %></div>
          <div class="cls"></div>
      </div>
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
      {content_for_layout}
    </div>
    <div class="cls"></div>
</div>
</body>
</html>