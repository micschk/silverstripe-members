<h1>$Title</h1>

$Content
$Form

<% include UserAccountTable %>

<% if $CurrentMember.canCreate %>
	<a href="{$Top.Link}addaccount">add account</a>
<% end_if %>