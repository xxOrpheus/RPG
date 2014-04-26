<div class="register">
	<form method="POST" id="registerForm" class="formee">
		<table style="margin: 0 auto">
			<tr><td>Username</td><td><input type="text" name="username" data-validation="length alphanumeric" data-validation-length="3-12"></td></tr>
			<tr><td>Password</td><td><input type="password" name="password"></td></tr>
			<tr><td>E-mail</td><td><input type="texT" name="email" data-validation="email" /></td></tr>
			<tr><td></td><td><input type="submit" name="submit" value="register"></td></tr>
		</table>
		<input type="hidden" name="q" value="register" />
	</form>
	<script>$.validate();</script>
	Already registered? <a href="/game/login/" class="load">Click here</a>.
</div>