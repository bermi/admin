<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Create new Role'), :controller => 'roles', :action => 'add' %></li>
        <li><%= link_to _('Show available Roles'), :controller => 'roles', :action => 'listing' %></li>
        <li class="active"><%= link_to _('Managing permissions'), :controller => 'permissions', :action => 'manage' %></li>
    </ul>
    <p class="information">_{Permissions let you control what users can do on your site. Each user role has its own set of permissions. You can use permissions to reveal new features to privileged users (those with subscriptions, for example).}</p>
</div>

<div class="content">
<h1>_{Manage permissions}</h1>

<%= start_form_tag {:action =>'manage'}, :id => 'permissions_form' %>

<div class="listing">
  <table cellspacing="0" summary="_{Managing permissions}">
  
{loop Extensions} {?Extension.permissions}
  <tr class="multiple">
    <th scope="col">{_Extension.name}</th>
    {loop Roles}
        <th scope="col"{!Role.is_enabled} class="disabled"{end}>{_Role.name}</th>
    {end}
  </tr>

  {loop Extension.permissions}
    <tr {?permission_odd_position}class="odd"{end}>
      <td class="field">{_permission.name} <%= link_to_destroy permission %></td>
    {loop Roles}
        <td class="centered"><%= permission_check_box permission, Extenssion, Role %></td>
    {end}
    </tr>
    
    
  {end}
     <tr><td colspan="<?=$Roles_available+1?>">&nbsp;</td></tr>   
{end}{end}

    </table>
</div>

    <div id="operations">
        <%= save_button %>
    </div>
</form>


</div>