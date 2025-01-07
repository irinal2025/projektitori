const projekti = "projektori";

/* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
function myTopNavFunction() {
    var x = document.getElementById("myTopnav");
    if (x.className === "topnav") {
      x.className += " responsive";
    } else {
      x.className = "topnav";
    }
  }

//Lisää aktiivisen navigointilinkin osoittaminen Javascriptillä ja CSS:llä:
document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll("nav a").forEach((link) => {
      //if (link.href === window.location.href) 
      if (link.pathname === window.location.pathname) {
          link.classList.add("active");
          link.setAttribute("aria-current", "page");
      }
  });
});


// Get the button:
const myBackToTopButton = document.getElementById("backToTopBtn");

// When the user scrolls down 20px from the top of the document, show the button
/*window.onscroll = function() {scrollFunction()};
function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}*/

// When the user clicks on the button, scroll to the top of the document
myBackToTopButton.addEventListener("click", function() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
});

/*
function backToTopFunction() {

}*/

//Field.touched for ".inputplaceholder":
document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll(".form-control:not(.form-control-notouched)").forEach((input) => {
      input.addEventListener("input", function() {
          //if (input.value || textarea.value) {
          if (input.value) {
              input.closest(".mb-3,.input-group").classList.add("touched");
              input.classList.add("touched");
          } else {
              input.closest(".mb-3,.input-group").classList.remove("touched");
              input.classList.remove("touched");
          }
      });
      input.addEventListener("textarea", function() {
        if (input.value) {
            input.closest(".mb-3").classList.add("touched");
            input.classList.add("touched");
        } else {
            input.closest(".mb-3").classList.remove("touched");
            input.classList.remove("touched");
        }
    });
  });
});

/*feedbackForm.find('input#programname').on('input', function() {
  mtvFeedbackForm.fieldTouchedForPlaceholder($(this));
});*/


if (window.location.href.indexOf("rekisteroidy.php") != -1) {
  const myRekisteroidyLink = document.getElementById("rekisteroidyLink");
  myRekisteroidyLink.addEventListener("click", function(e) {
    e.preventDefault()
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
  });
}

// Example starter JavaScript for disabling form submissions if there are invalid fields
(() => { 
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }

      form.classList.add('was-validated')
    }, false)
  })
})()


/*function ptShowPassword() {
  var x = document.getElementById("myInput");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
*/


const togglePassword = document.querySelector("#togglePassword");
const password = document.querySelector("#password");
togglePassword?.addEventListener("click", function () {
  // toggle the type attribute
  const type = password.getAttribute("type") === "password" ? "text" : "password";
  password.setAttribute("type", type);
  // toggle the eye icon
  /*this.classList.toggle('fa-eye');
  this.classList.toggle('fa-eye-slash');*/
  this.classList.toggle('icon-eye');
  this.classList.toggle('icon-eye-blocked');
});

const togglePassword2 = document.querySelector("#togglePassword2");
const password2 = document.querySelector("#password2");
togglePassword2?.addEventListener("click", function () {
  // toggle the type attribute
  const type = password2.getAttribute("type") === "password" ? "text" : "password";
  password2.setAttribute("type", type);
  // toggle the eye icon
  this.classList.toggle('icon-eye');
  this.classList.toggle('icon-eye-blocked');
});

