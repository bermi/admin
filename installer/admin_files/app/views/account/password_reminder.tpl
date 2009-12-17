
<form action="<%= url_for :action => 'password_reminder' %>" method="post">
  <h2 class="form_header">
    _{User name & password reminder}
  </h2>
  <div class="form">
    <p>
      <label for="email" id="email" class="required">_{The email address you used to sign up on %settings-application_name}:</label><br />
      <input id="email" name="email" size="30" type="text" value="{email?}" />
    </p>
    <p class="operations">
      <input type="submit" value="_{Get access details}" class="primary" />
    </p>
  </div>
</form>
