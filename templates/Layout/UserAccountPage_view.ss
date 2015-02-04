<h1>$Title_ViewAccount</h1>

$Content
$Form

<h2 class="pagetile">Account: $Account.Name</h2>
<% if $Account %>
	<div class="memberdetails">
		<% with $Account %>
			<div class="memberdetails_profileimage">
				$ProfileImage.CroppedImage(120,160)
			</div>
			<p>
				$Name<br />
				$Email<br />
				<% if Created %>Member Since : $Created.Nice<br /><% end_if %>
				<% if Email %>Email: $Email<br /><% end_if %>
				<a href="{$Top.Link}edit/$ID">edit</a>
				<% if $ID==$Top.currentMemberID %><a href="{$Top.Link}changepassword/">change password</a><% end_if %>
			</p>
		<% end_with %>
	</div>
<% end_if %>