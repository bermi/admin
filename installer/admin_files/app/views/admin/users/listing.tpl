<div id="content_menu">
    <ul class="menu">
        <li class="primary"><%= link_to _('Create new User'), :controller => 'users', :action => 'add' %></li>
        <li class="active"><%= link_to _('Listing Users'), :controller => 'users', :action => 'listing' %></li>
    </ul>
    <p class="information">_{The User management area allows you to create and edit user accounts.}</p>
</div>

<div class="content">
<h1>_{Listing available Users}</h1>

{?Users}
  <div class="listing">
  <table cellspacing="0" summary="_{Listing available Users}">

  <tr>
    <th scope="col"><%= sortable_link 'login' %></th>
    <th scope="col"><%= sortable_link 'email' %></th>
    <th scope="col"><%= sortable_link 'is_enabled', {}, :link_text => _('Status') %></th>
    <th scope="col"><%= sortable_link 'last_login_at', {}, :link_text => _('Last access') %></th>
    <th scope="col"><%= sortable_link 'created_at', {}, :link_text => _('Member for') %></th>
    <th scope="col">_{Roles}</th>
    <th colspan="4" scope="col" class="operations"><span class="auraltext">_{Operations}</span></th>
  </tr>

  {loop Users}
    <tr {?User_odd_position}class="odd"{end}>
      <td class="field">{User.login}</td>
      <td class="field"><%= mail_to User.email %></td>
      <td class="field">{?User.is_enabled}_{active}{else}_{blocked}{end}</td>
      <td class="field">{?User.last_login_at}<%= _("#{time_ago_in_words(User.last_login_at)} ago") %>{else}_{never}{end}</td>
      <td class="field"><%= time_ago_in_words User.created_at %></td>
      <td class="field"><? if($User->role->load()) : ?>
                {loop User.roles}
                    {role.name}{!role_is_last}, {end}
                {end}
      <? endif; ?></td>
      <td class="operation"><%= link_to_show User %></td>
      <td class="operation"><%= link_to_edit User %></td>
      <td class="operation"><%= link_to_destroy User %></td>
    </tr>
  {end}
   </table>
  </div>
  {end}
  
    {?user_pages.links}
        <div class="paginator">
        <div class="header"><?=translate('Showing page %page of %number_of_pages',array('%page'=>$user_pages->getCurrentPage(),'%number_of_pages'=>$user_pages->pages))?></div>
        {user_pages.links?}
        </div>
    {end}
</div>
