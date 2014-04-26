<div class="login">
	<form method="POST" id="loginForm" class="formee">
		<table style="margin: 0 auto">
			<tr><td>Username</td><td><input type="text" name="username" data-validation="length alphanumeric" data-validation-length="3-12"></td></tr>
			<tr><td>Password</td><td><input type="password" name="password"></td></tr>
			<tr><td></td><td><input type="submit" name="submit" value="Login"></td></tr>
		</table>
		<input type="hidden" name="q" value="login" />
	</form>
	<script>$.validate();</script>
	Not registered? <a href="/game/register/" class="load">Click here</a>.
</div>