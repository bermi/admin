<%= error_messages_for 'User' %>

<fieldset>
    <label class="required" for="user_login">_{Login}</label>
    <%= input 'user', 'login', :tabindex => '2' %>
</fieldset>

<fieldset>
    <label class="required" for="user_email">_{Email}</label>
    <%= input 'user', 'email', :tabindex => '3' %>
</fieldset>

<? if($admin_helper->can('Set roles', 'Admin::Users')) : ?>
{?Roles}
<fieldset>
    <legend class="required">_{Please select at least one Role}:</legend>

    <ul>
    {loop Roles}
            <? $is_checked = (!empty($params['roles'][$Role->getId()])) ? true : in_array($Role->id, $User->collect($User->roles, 'id','id')); ?>
        <li>
            <input type="hidden" value="0" name="roles[{Role.id}]"/>
            <input tabindex="4" type="checkbox" id="roles_id-{Role.id}" name="roles[{Role.id}]" {?is_checked}checked="checked"{end} />
            <label for="roles_id-{Role.id}">
                {Role.name}
                {?Role.description}– <span class="information">{Role.description}</span>{end}
            </label>
        </li>
    {end}
    </ul>
</fieldset>
{end}

<? endif; ?>

<fieldset>
    <p>
        <label {!User.id}class="required"{end} for="user_password">_{Password}</label>
        <input id="user_password" name="user[password]" size="30" tabindex="5" type="password" /> {?User.id}<span class="information">_{leave empty in order to keep previous password}</span>{end}
    </p>
    <p>
        <label {!User.id}class="required"{end} for="user_password_confirmation">_{Password confirmation}</label>
        <input id="user_password_confirmation" name="user[password_confirmation]" size="30" tabindex="6" type="password" />
    </p>
</fieldset>
