<div id="content_menu">
    <ul class="menu">
        <li class="active"><%= link_to _('Creating new User'), :controller => 'users', :action => 'add' %></li>
        <li class="primary"><%= link_to _('Show available Users'), :controller => 'users', :action => 'listing' %></li>
    </ul>
    <p class="information">_{The User management area allows you to create and edit user accounts.}</p>
</div>

<div class="content">
<h1>_{Creating a new User}</h1>
<%= start_form_tag {:action =>'add'}, :id => 'user_form' %>
    <div class="form">

    <%= render :partial => 'form' %>
   <fieldset>
       <ul><li>
        <%= input 'user', 'is_enabled', :tabindex => '8' %> 
        <label for="user_is_enabled">
            _{Enable account?} – <span class="information">_{Uncheck this option to revoke user access}</span>
        </label>
        </li></ul>
    </fieldset>
  </div>

  <div id="operations">
    <%= save_button %> _{or} <%= cancel_link %>
  </div>
</form>
</div>