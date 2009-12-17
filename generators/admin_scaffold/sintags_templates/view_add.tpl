<div id="content_menu">
    <ul class="menu">
        <li class="active"><?php  echo '<%='?> link_to _('Creating new <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'add' %></li>
        <li class="primary"><?php  echo '<%='?> link_to _('Show available <?php  echo AkInflector::titleize($plural_name)?>'), :action => 'listing' %></li>
    </ul>
    <p class="information">{_controller_information}</p>
</div>


<div id="content">
  <h1>_{Creating new <?php  echo AkInflector::titleize($singular_name)?>}</h1>
  
  <?php  echo '<%='?> start_form_tag :action => 'add' %>

    <div class="form">
      <?php  echo '<%='?>  render :partial => 'form' %>
    </div>

    <div id="operations">
      <?php  echo '<%='?> save_button %> _{or}  <?php  echo '<%='?> cancel_link %>
    </div>

  </form>
</div>

