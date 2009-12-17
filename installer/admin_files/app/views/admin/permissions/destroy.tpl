<div id="content_menu">
    <ul class="menu">
        <li class="primary"><%= link_to _('Managing permissions'), :controller => 'permissions', :action => 'manage' %></li>
        <li class="active"><%= link_to _('Deleting permissions'), :controller => 'permissions', :action => 'destroy', :id => Permission.id %></li>

    </ul>
    <p class="information">_{Roles group users into sets. These users can be treated as a whole by the system like when assigning permissions.}</p>
</div>

<div class="content">
<h1>_{Deleting Permission}</h1>
<p class="warning">_{Are you sure you want to delete this Permission?}</p>

<%=  start_form_tag :action => 'destroy', :id => Permission.id %>

    <dl>
    <dt>_{Extension}:</dt><dd>{Permission.extension.name}</dd>
    <dt>_{Name}:</dt><dd>{Permission.name}</dd>
    </dl>

    <div id="operations">
        <%= confirm_delete %> _{or} <%= link_to _('Cancel'), {:action => 'manage'}, :class => 'action' %>
    </div>
  </form>
</div>
