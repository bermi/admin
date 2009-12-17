<div id="content_menu">
    <ul class="menu">
        <li class="primary"><%= link_to _('Create new Role'), :controller => 'roles', :action => 'add' %></li>
        <li class="active"><%= link_to _('Listing Roles'), :controller => 'roles', :action => 'listing' %></li>
        <li><%= link_to _('Manage permissions'), :controller => 'permissions', :action => 'manage' %></li>
    </ul>
    <p class="information">_{Roles group users into sets. These users can be treated as a whole by the system like when assigning permissions.}</p>
</div>

<div class="content">
<h1>_{Listing available Roles}</h1>
  {?Roles}
  <div class="listing">
  <table cellspacing="0" summary="_{Listing available Roles}">

  <tr>
    <th scope="col">_{Name}</th>
    <th scope="col">_{Description}</th>
    <th scope="col">_{Status}</th>
    <th colspan="3" scope="col"><span class="auraltext">_{Role actions}</span></th>
  </tr>

  {loop Roles}
    <tr {?Role_odd_position}class="odd"{end}>
      <td class="field">{Role.name}</td>
      <td class="field">{Role.description}</td>
      <td class="field">{?Role.is_enabled}_{active}{else}_{blocked}{end}</td>
      <td class="operation"><%= link_to_destroy Role %></td>
      <td class="operation"><%= link_to_edit Role %></td>
    </tr>
  {end}
   </table>
  </div>
  {end}
  
  
</div>
