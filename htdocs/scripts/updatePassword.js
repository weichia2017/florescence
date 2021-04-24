let userID = $("#getUserID").val();

console.log("Userid=" + userID);

//setup before functions
var typingTimer;                  //timer identifier
var doneTypingInterval  = 500;    //time in ms (500 milli)
var isBothPasswordsSame = false;
var isOldPasswordValid  = false;

//on keyup, start the countdown
$('#oldPassword').keyup(function () {
    clearTimeout(typingTimer);
    if ($('#oldPassword').val()) {
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    }else{
        document.getElementById("resetPassword").disabled = true;
    }
});

//user is "finished typing," do something
async function doneTyping() {
    let pwdErrorMsg = document.getElementById("pwdError");

    let oldPassword = $('#oldPassword').val();
    let newpw = $('#newPassword').val();
    let cfmpw = $('#cfmNewPassword').val();
    let requestParameters = "user_id=" + userID + "&password=" + oldPassword + "&checkOnly=1";
    let response = await makeRequestxwwwFormURLEncode(hostname + "/users/update/password", "POST", requestParameters);

    response = JSON.parse(response)['response'];
    console.log(response)
    if(response == "Valid Password"){
        pwdErrorMsg.innerText = "";
        isOldPasswordValid = true;

        // If both fields are empty means user only entered oldpassword so far so no need to CheckPwd()
        if(newpw == "" && cfmpw == ""){
            document.getElementById("resetPassword").disabled = true;
            return;
        }
        //Check if both passwords match again
        CheckPwd();
        if(isOldPasswordValid && isBothPasswordsSame){
            document.getElementById("resetPassword").disabled = false;
            // checkForOldNewPasswordSame();
            
        }
        return;
    }

    pwdErrorMsg.style.color = "red";
    pwdErrorMsg.innerText = "";
    document.getElementById("resetSucess").innerText = "";
    pwdErrorMsg.innerText = response;
    document.getElementById("resetPassword").disabled = true;
    isOldPasswordValid = false;
    
}


function CheckPwd() {
    var pw = $('#newPassword').val();
    var cfmpw = $('#cfmNewPassword').val();
    const pwdErrorMsg = document.getElementById("pwdError");

    if (pw == cfmpw & pw != ""  && cfmpw != "") {
        pwdErrorMsg.innerText = "";
        isBothPasswordsSame = true;

        if (!isOldPasswordValid){
            pwdError.innerText = "Invalid OLD password";
            document.getElementById("resetPassword").disabled = true;
            return;
        }

        if (isBothPasswordsSame){
            document.getElementById("resetPassword").disabled = false;
            // checkForOldNewPasswordSame();
            return;
        }
        
    }
    document.getElementById("resetSucess").innerText = "";
    pwdErrorMsg.innerText = "";
    pwdErrorMsg.innerText = "Passwords do not match";
    isBothPasswordsSame = false;
    document.getElementById("resetPassword").disabled = true;

}

// function checkForOldNewPasswordSame(){
//     var oldPw = $('#oldPassword').val();
//     var newPw = $('#newPassword').val();

//     if(oldPw == newPw){
//         document.getElementById("pwdError").innerText = "Old and New Password cannot be the same";
//         document.getElementById("resetPassword").disabled = true;
//     }

// }
  

// on keyup, start the countdown
$('#newPassword,#cfmNewPassword').keyup(function () {
//Only if the both password fields are not empty
if ($('#newPassword').val() && $('#cfmNewPassword').val()) {
    CheckPwd();
}
});


async function submitPasswordReset(){
    //One last check 
    if(isOldPasswordValid && isBothPasswordsSame){
        let pwdErrorMsg = document.getElementById("pwdError");

        let oldPassword = $('#oldPassword').val();
        let newpw = $('#newPassword').val();
        let requestParameters = "user_id=" + userID + "&password=" + oldPassword + "&newPassword=" + newpw;
        let response = await makeRequestxwwwFormURLEncode(hostname + "/users/update/password", "POST", requestParameters);

        response = JSON.parse(response)['response'];
        if(response=="Incorrect existing password"){
            pwdErrorMsg.innerText = "Incorrect existing password";
            document.getElementById("resetSucess").innerText = '';
            document.getElementById("resetPassword").disabled = false;
            document.getElementById("oldPassword").value = '';
            document.getElementById("newPassword").value = '';
            document.getElementById("cfmNewPassword").value = '';
            return;
        }
        if(response){
            document.getElementById("resetSucess").innerText = "Password Successfully Updated";
            document.getElementById("resetPassword").disabled = true;
            document.getElementById("oldPassword").value = '';
            document.getElementById("newPassword").value = '';
            document.getElementById("cfmNewPassword").value = '';
            return;
        }
    }
}