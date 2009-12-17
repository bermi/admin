<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Create new Role'), :controller => 'roles', :action => 'add' %></li>
        <li class="active"><%= link_to _('Editing Role'), :controller => 'roles', :action => 'edit', :id => role.id %></li>
        <li><%= link_to _('Delete Role'), :controller => 'roles', :action => 'destroy', :id => role.id %></li>
        <li class="primary"><%= link_to _('Show available Roles'), :controller => 'roles', :action => 'listing' %></li>
    </ul>
    <p class="information">_{Roles group users into sets. These users can be treated as a whole by the system like when assigning permissions.}</p>
</div>

<div class="content">
<h1>_{Editing Role}</h1>

  <%= start_form_tag :action => 'edit', :id => role.id %>

  <div class="form">
    <%= render :partial => 'form' %>
  </div>
    
    <div id="operations">
        <%= save_button %> _{or} <%= cancel_link %>
    </div>

  </form>
</div>
