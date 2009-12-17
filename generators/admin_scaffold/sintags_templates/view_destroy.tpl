<div id="content_menu">
    <ul class="menu">
        <li><?php  echo '<%='?> link_to _('Create new <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'add' %></li>
        <li class="primary"><?php  echo '<%='?> link_to _('Edit <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'edit', :id => <?php  echo $model_name?>.id %></li>
        <li><?php  echo '<%='?> link_to _('Show <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'show', :id => <?php  echo $model_name?>.id %></li>
        <li class="active"><?php  echo '<%='?> link_to _('Deleting <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'destroy', :id => <?php  echo $model_name?>.id %></li>
        <li><?php  echo '<%='?> link_to _('Show available <?php  echo AkInflector::titleize($plural_name)?>'), :action => 'listing' %></li>
    </ul>
    <p class="information">{_controller_information}</p>
</div>


<div class="content">
<h1>_{Deleting <?php  echo AkInflector::titleize($singular_name)?>}</h1>
<p class="warning">_{Are you sure you want to delete this <?php  echo AkInflector::titleize($singular_name)?>?}</p>

<?php  echo '<%='?>  start_form_tag :action => 'destroy', :id => <?php  echo $model_name?>.id %>

    <dl>
        <?php echo '<?php  '?>$content_columns = array_keys($<?php  echo $model_name?>->getContentColumns()); ?>
        {loop content_columns}
          <dt><?php  echo '<%='?> translate( titleize( content_column ) ) %>:</dt>
          <dd><?php  echo '<?php  echo '?> $<?php  echo AkInflector::camelize($singular_name)?>->get($content_column) ?>&nbsp;</dd>
        {end}
    </dl>

    <div id="operations">
        <?php  echo '<%='?> confirm_delete %> _{or} <?php  echo '<%='?> cancel_link %>
    </div>
  </form>
</div>
