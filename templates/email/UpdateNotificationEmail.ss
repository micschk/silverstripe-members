<p>Edited account: $UserAccount.Name</p>

<p>
Changed: <br />
<% if $UserAccount.isChanged('FirstName') %>First name: $UserAccount.FirstName <br /><% end_if %>
<% if $UserAccount.isChanged('LastName') %>Last name: $UserAccount.LastName <br /><% end_if %>
<% if $UserAccount.isChanged('Email') %>E-mail: $UserAccount.Email <br /><% end_if %>
<%-- alas MultiValueField always seems to report to have been changed... --%>
<% if $UserAccount.isChanged('PhoneNumbers') %>
	Phone numbers: <br />
	<% loop $UserAccount.PhoneNumbers.Items %>$Value<% if not $Last %>, <% end_if %><% end_loop %>
<% end_if %>

<% loop $UserAccount.AllFields() %>$key $value <% end_loop %>
</p>