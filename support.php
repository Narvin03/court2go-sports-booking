<html>
<head>
    <title>Support | Court2Go</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./Styles/support.css"/>
    <script src="./JavaScripts/support.js" defer></script>
    <script src="./JavaScripts/main.js"></script>
    <script src="https://kit.fontawesome.com/1af9345e5c.js" crossorigin="anonymous"></script>
</head>

<!-- Thank You Popup -->
<div id="thankyou-popup" class="popup-overlay" aria-hidden="true">
  <div class="popup-box" role="dialog" aria-modal="true" aria-labelledby="ty-title">
    <h2 id="ty-title">Thank you!</h2>
    <p>Thank you for your response. Our team will contact you soon.</p>
    <button id="close-popup" class="popup-close">Close</button>
  </div>
</div>

<body>
    <?php include('./Includes/header.php'); ?>

    <div class="support-background">
        <div class="support-header">
            <h1>Contact Us</h1>
            <p>Any questions or remarks? Just write us a message.</p>
        </div>

        <div class="support-main">
    
            <div class="support-content">
                <h2>Contact Us</h2>
                <p>Fill up the form and our team will get back to you within 24 hours.</p>

                <p id="number">
                    <i class="fa-solid fa-phone"></i>
                    <a href="https://web.whatsapp.com/">+6012-3588-2182</a>
                </p>

                <p id="mail">
                    <i class="fa-solid fa-envelope"></i>
                    <a href="mailto:court2go@gmail.com">court2go@gmail.com</a>
                </p>
            </div>

            <form method="post" class="contact-form" id="contact-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="f-name">First Name</label>
                        <input type="text" id="f-name" name="f-name" placeholder="First Name">
                        <div id="msg_fname" class="msg"></div>
                    </div>

                    <div class="form-group">
                        <label for="l-name">Last Name</label>
                        <input type="text" id="l-name" name="l-name" placeholder="Last Name">
                        <div id="msg_lname" class="msg"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="text" id="email" name="email" placeholder="Email">
                        <div id="msg_email" class="msg"></div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" placeholder="Number">
                        <div id="msg_phone" class="msg"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea name="message" id="message" rows="5" placeholder="Message"></textarea>
                    <div id="msg_message" class="msg"></div>
                </div>

                <div class="submit-group">
                    <button type="submit" class="btn-submit" id="btn-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <?php include('./Includes/footer.php'); ?>
</body>
</html>