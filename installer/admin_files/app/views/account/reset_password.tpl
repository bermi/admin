<%= error_messages_for 'User' %>
<form action="<%= url_for :action => 'reset_password' %>" method="post">
  <h2 class="form_header">
    _{Please choose a new password for your account}
  </h2>
  <div class="form">
      <input name='token' type='hidden' value="{token}" />
      <%= render :partial => 'password_field' %>
    <p class="operations">
      <input type="submit" value="_{Get access details}" class="primary" />
    </p>
  </div>
</form>