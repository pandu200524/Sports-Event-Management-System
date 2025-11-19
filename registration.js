let captchaCode = "";

function generateCaptcha() {
  const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  const numbers = "0123456789";

  let captcha = letters.charAt(Math.floor(Math.random() * letters.length));
  for (let i = 0; i < 4; i++) {
    captcha += numbers.charAt(Math.floor(Math.random() * numbers.length));
  }

  captchaCode = captcha;
  document.getElementById("captcha").textContent = captchaCode;
}

function validateCaptcha() {
  const userInput = document.getElementById("captchaInput").value;
  const message = document.getElementById("captchaMessage");

  if (userInput === captchaCode) {
    message.textContent = "Captcha validation successful!";
    message.style.color = "green";
    return true;
  } else {
    message.textContent = "Captcha validation failed. Please try again.";
    message.style.color = "red";
    generateCaptcha(); // Regenerate captcha on invalid input
    return false;
  }
}

function validatePassword() {
  const password = document.getElementById("password").value;
  const passwordMessage = document.getElementById("passwordMessage");

  // Regular expressions for password criteria
  const regexUpperCase = /[A-Z]/;
  const regexLowerCase = /[a-z]/;
  const regexSpecialChar = /[!@#$%^&*(),.?":{}|<>]/;
  const minLength = 8;

  // Reset message
  passwordMessage.textContent = "";

  if (password.length < minLength) {
    passwordMessage.textContent = "Password must be at least 8 characters long.";
    passwordMessage.style.color = "red";
    return false;
  }
  if (!regexUpperCase.test(password)) {
    passwordMessage.textContent = "Password must contain at least one uppercase letter.";
    passwordMessage.style.color = "red";
    return false;
  }
  if (!regexLowerCase.test(password)) {
    passwordMessage.textContent = "Password must contain at least one lowercase letter.";
    passwordMessage.style.color = "red";
    return false;
  }
  if (!regexSpecialChar.test(password)) {
    passwordMessage.textContent = "Password must contain at least one special character.";
    passwordMessage.style.color = "red";
    return false;
  }

  passwordMessage.textContent = "Password validation successful!";
  passwordMessage.style.color = "green";
  return true;
}

document.addEventListener("DOMContentLoaded", () => {
  generateCaptcha();

  const form = document.querySelector("form");
  form.addEventListener("submit", (event) => {
    const isCaptchaValid = validateCaptcha();
    const isPasswordValid = validatePassword();

    if (!isCaptchaValid || !isPasswordValid) {
      event.preventDefault(); // Prevent form submission if validation fails
    }
  });
});