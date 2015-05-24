<% if $AccountList %>
	<table class="table memberslist">
		<thead>
			<th>Picture</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>E-mail</th>
			<th>Actions</th>
		</thead>
		<tbody>
			<% loop $AccountList %>
				<tr>
					<td>$ProfileImage.CroppedImage(64,80)</td>
					<td>$FirstName</td>
					<td>$Surname</td>
					<td>$Email</td>
					<td>
						<% if $canView %>
							<a href="{$Top.Link}view/$ID">view</a>
						<% end_if %>
						<% if $canEdit %>
							<a href="{$Top.Link}edit/$ID">edit</a>
						<% end_if %>
						<% if $ID==$Top.currentMemberID %>
							<a href="{$Top.Link}changepassword/">change password</a>
						<% end_if %>
						<% if $canDelete %>
							<a href="{$Top.Link}delete/$ID">remove account</a>
						<% end_if %>
					</td>
				</tr>
			<% end_loop %>
		</tbody>
	</table>
<% end_if %>