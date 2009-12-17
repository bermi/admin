<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Create new User'), :controller => 'users', :action => 'add' %></li>
        <li class="active"><%= link_to _('Editing User'), :controller => 'users', :action => 'edit', :id => user.id %></li>
        <li class="primary"><%= link_to _('Show User profile'), :controller => 'users', :action => 'show', :id => user.id %></li>
        <li><%= link_to _('Delete this User'), :controller => 'users', :action => 'destroy', :id => user.id %></li>   
        <li><%= link_to _('Show available Users'), :controller => 'users', :action => 'listing' %></li>
    </ul>
    <p class="information">_{The User management area allows you to create and edit user accounts.}</p>
</div>

<div class="content">
<h1>_{Editing User}</h1>
<%= start_form_tag {:action =>'edit', :id => User.id}, :id => 'user_form' %>
    <div class="form">
        <%= render :partial => 'form' %>
        
        <? if($User->id != $CurrentUser->id && !$CurrentUser->hasRootPrivileges()) : ?>
        <fieldset>
           <ul><li>
            <%= input 'user', 'is_enabled', :tabindex => '7' %> 
            <label for="user_is_enabled">
                {?User.is_enabled}
                    _{Keep this account enabled?} – <span class="information">_{Uncheck this option to revoke user access}</span>
                {else}
                    _{Enable this account?} – <span class="information">_{Check this option to grant user access}</span>
                {end}
            </label>
            </li></ul>
        </fieldset>
        <? endif; ?>
    </div>

    <div id="operations">
        <%= save_button %> _{or} <%= cancel_link %>
    </div>
</form>
</div>