<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Create new User'), :controller => 'users', :action => 'add' %></li>
        <li class="primary"><%= link_to _('Edit this User'), :controller => 'users', :action => 'edit', :id => user.id %></li>
        <li><%= link_to _('Show User details'), :controller => 'users', :action => 'show', :id => user.id %></li>
        <li class="active"><%= link_to _('Removing User'), :controller => 'users', :action => 'destroy', :id => user.id %></li>        <li><%= link_to _('Show available Users'), :controller => 'users', :action => 'listing' %></li>
    </ul>
    <p class="information">_{The User management area allows you to create and edit user accounts.}</p>
</div>

<div class="content">

<h1>_{Deleting User}</h1>
<p class="warning">_{Are you sure you want to delete this User?}</p>

<%= start_form_tag :action => 'destroy', :id => User.id %>
    <div class="form">  
        <dl>
        <dt>_{Login}:</dt><dd>{User.login}</dd>
        <dt>_{Email}:</dt><dd><%= mail_to User.email %></dd>
        <dt>_{Enabled}:</dt><dd>{!User.is_enabled}_{User disabled}{else}_{User enabled}{end}</dd>
        {?User.roles}
            <dt>_{Assigned Roles}:</dt> 
            <dd>
                {loop User.roles}
                    <p>{role.name} {?role.description}, <span class="information">{role.description}</span>{end}</p>
                {end}
            </dd>
        {end}
    </dl>
    </div>
    <div id="operations">
        <%= confirm_delete %> _{or} <%= cancel_link %>
    </div>
    
</form>

</div>