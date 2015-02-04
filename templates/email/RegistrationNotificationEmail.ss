<p>Newly registered account: $UserAccount.Name</p>

<p>
First name: $UserAccount.FirstName <br />
Last name: $UserAccount.LastName <br />
E-mail: $UserAccount.Email <br />

Phone numbers: <br />
<% loop $UserAccount.PhoneNumbers.Items %>$Value<% if not $Last %>, <% end_if %><% end_loop %>

</p>