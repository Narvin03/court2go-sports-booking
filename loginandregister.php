
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title> Login page </title>
    <link rel="stylesheet" href="Styles/loginstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
<body>
  <div class="bg-wrapper"></div>
    <div class="site-header">Court2Go</div>
    <div class="container">
      <input type="checkbox" id="flip">
        <div class="cover">
          <div class="front">
            <img src="Assets/images/sport.jpg" alt="">
        <div class="text">
          <span class="text-1">Every game is a<br> new story</span>
          <span class="text-2">Let’s play, let’s connect.</span>
        </div>
      </div>
      <div class="back">
        <img class="backImg" src="Assets/images/Selby3.jpg" alt="">
        <div class="text">
          <span class="text-1">Complete miles of passion<br> with just one click.</span>
          <span class="text-2">Reserve your spot now</span>
        </div>
      </div>
    </div>
    <div class="forms">
  <div class="form-content">
    <div class="login-form">
      <div class="title">Login</div>
      <form id="loginForm">
        <div class="input-boxes">
          <div class="input-box">
            <i class="fas fa-envelope"></i>
            <input type="email" id="loginEmail" placeholder="Enter your email" required>
          </div>
          <div class="input-box">
            <i class="fas fa-lock"></i>
            <input type="password" id="loginPassword" placeholder="Enter your password" required>
          </div>
          <div class="text"><a href="#" id="forgotLink">Forgot password?</a></div>
          <div class="button input-box">
            <input type="submit" value="Login">
          </div>
          <div class="text sign-up-text">Don't have an account? <label for="flip">Register now</label></div>
        </div>
      </form>
    </div>
    <div class="signup-form">
      <div class="title">Register</div>
      <form id="signupForm">
        <div class="input-boxes">
          <div class="input-box">
            <i class="fas fa-user"></i>
            <input type="text" id="signupName" placeholder="Enter your name" required>
          </div>
          <div class="input-box">
            <i class="fas fa-envelope"></i>
            <input type="email" id="signupEmail" placeholder="Enter your email" required>
          </div>
          <div class="input-box">
            <i class="fas fa-lock"></i>
            <input type="password" id="signupPassword" placeholder="Enter your password" required>
          </div>
          <div class="button input-box">
            <input type="submit" value="Register">
          </div>
          <div class="text sign-up-text">Already have an account? <label for="flip">Login now</label></div>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="forgotPopup" class="popup">
  <div class="popup-content">
    <span class="close">&times;</span>
    <h3>Reset Password</h3>
    <p>Enter your email to reset password:</p>
    <input type="email" id="forgotEmail" placeholder="Enter your email">
    <button id="forgotSubmit">Send Reset Link</button>
    <p id="forgotMessage" style="color:green; display:none;"></p>
  </div>
</div>

<style>
.popup {
  display: none; 
  position: fixed; 
  z-index: 1000; 
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  align-items: center; 
  justify-content: center;
}
.popup-content {
  background: #fff;
  padding: 30px 25px;
  border-radius: 12px;
  width: 360px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  animation: popupFade 0.4s ease;
  font-family: "Poppins", sans-serif;
}

@keyframes popupFade {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}
.close {
  position: absolute;
  top: 12px; right: 18px;
  font-size: 22px;
  font-weight: bold;
  color: #22c1e9;
  cursor: pointer;
  transition: 0.2s;
}
.close:hover {
  color: #224b97;
}

.popup-content input[type="email"] {
  width: 100%;
  padding: 12px;
  margin: 15px 0;
  border: none;
  border-bottom: 2px solid rgba(0,0,0,0.2);
  outline: none;
  font-size: 16px;
  transition: border-color 0.3s ease;
}
.popup-content input[type="email"]:focus {
  border-color: #22c1e9;
}

.popup-content button {
  width: 100%;
  padding: 12px;
  background: #22c1e9;
  border: none;
  border-radius: 6px;
  color: #fff;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.3s ease;
}
.popup-content button:hover {
  background: #224b97;
}
</style>

  </div>
  <script src="JavaScripts/script.js"></script>
</body>
<div>
</div>
</html>
