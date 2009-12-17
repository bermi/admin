<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Create new Role'), :controller => 'roles', :action => 'add' %></li>
        <li class="primary"><%= link_to _('Edit Role'), :controller => 'roles', :action => 'edit', :id => role.id %></li>
        <li class="active"><%= link_to _('Deleting Role'), :controller => 'roles', :action => 'destroy', :id => role.id %></li>
        <li><%= link_to _('Show available Roles'), :controller => 'roles', :action => 'listing' %></li>
    </ul>
    <p class="information">_{Roles group users into sets. These users can be treated as a whole by the system like when assigning permissions.}</p>
</div>

<div class="content">
<h1>_{Deleting Role}</h1>
<p class="warning">_{Are you sure you want to delete this Role?}</p>

<%=  start_form_tag :action => 'destroy', :id => role.id %>

    <dl>
    <dt>_{Name}:</dt><dd>{role.name}</dd>
    {?role.description}<dt>_{Description}:</dt><dd>{role.description}</dd>{end}
    </dl>

    <div id="operations">
        <%= confirm_delete %> _{or} <%= cancel_link %>
    </div>
  </form>
</div>
