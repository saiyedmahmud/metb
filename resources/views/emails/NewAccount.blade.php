<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Customer Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(242,242,242);
            padding: 20px;
        }

        .container {
            text-align: center;
            margin: 0 auto;
            background-color: rgb(242,242,242);
            padding: 20px;
        }
        .content-body{
            text-align: center;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
        }

        .logo img {
            max-width: none;
            width: 300px;
            height: 50px;
            margin-top: 20px;
        }

        .content {
            text-align: center;
            margin: 0 auto;
            font-size: 12px;
            color: #888888;
            padding: 20px;
            font-weight: bold;
        }

        .login-section {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
        }

        .login-button {
            display: inline-block;
            padding: 10px 30px;
            font-size: 16px;
            background-color: #169B7D;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }

        .login-button:hover {
            background-color: #000000;
        }

        .social-links {
            font-size: 14px;
            color: #888888;
            margin-top: 20px;
        }

        .social-links a {
            text-decoration: none;
            color: #0077B5;
            margin: 0 10px;
        }
                hr {
            width: 75%;
            margin: 20px auto;
            border: none;
            height: 1px;
            background-color: #c3c1c1;
        }
        .footer img {
            width: 40px;
            height: auto;
            margin: 0 3px;
        }

        .footer a {
            text-decoration: none;
            color: #888888;
            margin: 0 10px;
        }

        .footer {
            margin: 0 auto;
            text-align: center;
            font-size: 12px;
            color: #888888;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
       <div class="content-body">
           <!-- Header Section -->
           <div class="login-section">
               <img style="width: 200px; height: 30px;" src="https://efvubao.stripocdn.email/content/guids/CABINET_71fd4e221d4d35bc7d7926a79506654a6c2e8ad36423b0adf2860b71f4e5a36b/images/image.png" alt="">
           </div>

           <!-- Body Content -->
           <div class="content">
               <h3 style="color: #333333; text-align: center; font-size:22px; margin:35px">Hi {{$name}}!</h3>
               <p style="font-size: 16px; color: #666666; text-align: center; ">Your customer account has been created with OS Inventory.<br />Please
                   login with below credentials.</p>
               <p style="font-size: 16px; color: #000000; text-align: center;">Email: {{$email}}<br />Password: {{$password}}</p>
               <!-- Add personalized content here -->
           </div>

           <!-- Login Section -->
           <div class="login-section">
               <a href="https://demo.omega.ac/" class="login-button">Login</a>
           </div>

           <!-- Footer Section -->
           <div class="footer">
               <p>Contract Us:</p>
               <p class="social-links">
                   <a href="https://www.facebook.com/omegasolutionllc"
                   ><img
                           src="https://efvubao.stripocdn.email/content/assets/img/social-icons/logo-black/facebook-logo-black.png"
                           alt="Facebook"
                       /></a>
                   <a href="https://www.twitter.com/omegasolution1"
                   ><img
                           src="https://efvubao.stripocdn.email/content/assets/img/social-icons/logo-black/x-logo-black.png"
                           alt="Twitter"
                       /></a>
                   <a href="https://www.instagram.com/omegasolutionllc"
                   ><img
                           src="https://efvubao.stripocdn.email/content/assets/img/social-icons/logo-black/instagram-logo-black.png"
                           alt="Instagram"
                       /></a>
                   <a href="https://www.youtube.com/@omegasolutionllc"
                   ><img
                           src="https://efvubao.stripocdn.email/content/assets/img/social-icons/logo-black/youtube-logo-black.png"
                           alt="YouTube"
                       /></a>
                   <a href="https://www.linkedin.com/company/oneomegasolution/"
                   ><img
                           src="https://efvubao.stripocdn.email/content/assets/img/social-icons/logo-black/linkedin-logo-black.png"
                           alt="LinkedIn"
                       /></a>
               <hr>
               <a href="https://solution.omega.ac/">Visit Us</a> |
               <a  href="https://solution.omega.ac/privacy-policy/">Privacy Policy</a> |
               <a href="https://solution.omega.ac/services/">Services</a>
               <p><a href="https://maps.app.goo.gl/1HMGr3UaiPtthy8C8"
                     target="_blank"
                   >
                       2201 Menaul Blvd NE Suite B,
                       Albuquerque, NM 87107, United States</a
                   ></p>
               <p>&copy; 2021 Omega Solution LLC. All rights reserved.</p>
           </div>
       </div>
    </div>
</body>

</html>
