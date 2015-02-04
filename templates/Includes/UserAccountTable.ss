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
						<a href="{$Top.Link}view/$ID">view</a>
						<a href="{$Top.Link}edit/$ID">edit</a>
						<% if $ID==$Top.currentMemberID %>
							<a href="{$Top.Link}changepassword/">change password</a>
						<% end_if %>
						<a href="{$Top.Link}delete/$ID">remove account</a>
					</td>
				</tr>
			<% end_loop %>
		</tbody>
	</table>
<% end_if %>