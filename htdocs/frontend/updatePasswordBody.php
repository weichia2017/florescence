<?php
$userID = $_SESSION["userID"];
?>
<input type="hidden" value=<?= $userID?>  id="getUserID">

<div class="container border border-secondary rounded shadow col-10 col-sm-7 col-md-5 col-lg-4 p-4 text-center" style="margin-top:10%">

    <span style="font-size:28px; color: rgb(92, 92, 92)"  class="material-icons">
        password
    </span>
    <span class="headings text-center">Reset password</span>


    <!-- Old Password Field -->
    <div class="form-group mt-3">
        <input type="password" name="oldPassword" id="oldPassword" class="form-control" placeholder="Old Password">
    </div>

    <!-- New Password Field -->
    <div class="form-group">
        <input type="password" name="newPassword" id="newPassword" class="form-control" placeholder="New Password">
    </div>

    <!-- Confirm New Password Field -->
    <div class="form-group">
        <input type="password" name="cfmNewPassword" id="cfmNewPassword" class="form-control"
            placeholder="Confirm New Password">
    </div>

    <!-- Password Do Not Match Failure Message -->
    <div id="pwdError" class="form-group"  style="color: red;"></div>
    <div id="resetSucess" class="form-group" style="color: green;"></div>

    <!-- Password Reset Button -->
    <button type="submit" onclick="submitPasswordReset()" class="btn btn-success btn-block text-white" id="resetPassword" disabled>Reset
        Password</button>
</div>