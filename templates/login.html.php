<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
  
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
        text-decoration: none;
        list-style: none;
    }

    body{
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: linear-gradient(90deg, #0F1C2C, #2E3D54);
    }

    .container{
        position: relative;
        width: 850px;
        height: 550px;
        background: #0F1C2C;
        margin: 20px;
        border-radius: 30px;
        box-shadow: 0 0 30px rgba(0, 0, 0, .2);
        overflow: hidden;
    }

        .container h1{
            font-size: 36px;
            margin: -10px 0;
        }

        .container p{
            font-size: 14.5px;
            margin: 15px 0;
        }

    form{ width: 100%; }

    .form-box{
        position: absolute;
        right: 0;
        width: 50%;
        height: 100%;
        background: #2E3D54;
        display: flex;
        align-items: center;
        color: #fff;
        text-align: center;
        padding: 40px;
        z-index: 1;
        transition: .6s ease-in-out 1.2s, visibility 0s 1s;
    }

        .container.active .form-box{ right: 50%; }

        .form-box.register{ visibility: hidden; }
            .container.active .form-box.register{ visibility: visible; }

    .input-box{
        position: relative;
        margin: 30px 0;
    }

        .input-box input{
            width: 100%;
            padding: 13px 50px 13px 20px;
            background: #eee;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

            .input-box input::placeholder{
                color: #888;
                font-weight: 400;
            }
        
        .input-box i{
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

    .forgot-link{ margin: -15px 0 15px; }
        .forgot-link a{
            font-size: 14.5px;
            color: #fff;
        }

    .btn{
        width: 100%;
        height: 48px;
        background: #FF004D;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, .1);
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: #fff;
        font-weight: 600;
    }

    .social-icons{
        display: flex;
        justify-content: center;
    }

        .social-icons a{
            display: inline-flex;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 24px;
            color: #333;
            margin: 0 8px;
        }

    .toggle-box{
        position: absolute;
        width: 100%;
        height: 100%;
    }

        .toggle-box::before{
            content: '';
            position: absolute;
            left: -250%;
            width: 300%;
            height: 100%;
            background: #69B7FF;
            /* border: 2px solid red; */
            border-radius: 150px;
            z-index: 2;
            transition: 1.8s ease-in-out;
        }

            .container.active .toggle-box::before{ left: 50%; }

    .toggle-panel{
        position: absolute;
        width: 50%;
        height: 100%;
        /* background: seagreen; */
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 2;
        transition: .6s ease-in-out;
    }

        .toggle-panel.toggle-left{ 
            left: 0;
            transition-delay: 1.2s; 
        }
            .container.active .toggle-panel.toggle-left{
                left: -50%;
                transition-delay: .6s;
            }

        .toggle-panel.toggle-right{ 
            right: -50%;
            transition-delay: .6s;
        }
            .container.active .toggle-panel.toggle-right{
                right: 0;
                transition-delay: 1.2s;
            }

        .toggle-panel p{ margin-bottom: 20px; }

        .toggle-panel .btn{
            width: 160px;
            height: 46px;
            background: transparent;
            border: 2px solid #fff;
            box-shadow: none;
        }

    @media screen and (max-width: 650px){
        .container{ height: calc(100vh - 40px); }

        .form-box{
            bottom: 0;
            width: 100%;
            height: 70%;
        }

            .container.active .form-box{
                right: 0;
                bottom: 30%;
            }

        .toggle-box::before{
            left: 0;
            top: -270%;
            width: 100%;
            height: 300%;
            border-radius: 20vw;
        }

            .container.active .toggle-box::before{
                left: 0;
                top: 70%;
            }

            .container.active .toggle-panel.toggle-left{
                left: 0;
                top: -30%;
            }

        .toggle-panel{ 
            width: 100%;
            height: 30%;
        }
            .toggle-panel.toggle-left{ top: 0; }
            .toggle-panel.toggle-right{
                right: 0;
                bottom: -30%;
            }

                .container.active .toggle-panel.toggle-right{ bottom: 0; }
    }

    @media screen and (max-width: 400px){
        .form-box { padding: 20px; }

        .toggle-panel h1{font-size: 30px; }
    }

    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #0F1C2C;
            image: url('img_bg/Logo.png');
            animation:  gradientBG 15s ease infinite;
            overflow: hidden;
        }
        @keyframes gradientBG {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        .container {
            position: relative;
            width: 800px;
            height: 500px;
            background: #2E3D54;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, #ff6b6b, #f06595, #845ef7, #339af0, #22b8cf, #20c997, #51cf66, #94d82d, #fcc419, #ff922b);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            z-index: -1;
        }
        .container:hover::before {
            animation-play-state: paused;
        }
  </style>
</head>
<body>
  
  <div class="container">
          <div class="form-box login">
              <form action="../login.php" method= "POST">
                  <h1>Login</h1>
                  <div class="input-box">
                      <input type="text" name="Username" placeholder="Username" required>
                      <i class='bx bxs-user'></i>
                  </div>
                  <div class="input-box">
                      <input type="password" name="Password" placeholder="Password" required>
                      <i class='bx bxs-lock-alt' ></i>
                  </div>
                  <div class="forgot-link">
                      <a href="../contact/forgot_password.php">Forgot Password?</a>
                  </div>
                  <button type="submit" class="btn">Login</button>
              </form>
          </div>

          <div class="form-box register">
              <form action="../register.php" method= "POST">
                  <h1>Registration</h1>
                  <div class="input-box">
                      <input type="text" name="Username" placeholder="Username" required>
                      <i class='bx bxs-user'></i>
                  </div>
                  <div class="input-box">
                      <input type="email" name="Email" placeholder="Email" required>
                      <i class='bx bxs-envelope' ></i>
                  </div>
                  <div class="input-box">
                      <input type="password" name="Password" placeholder="Password" required>
                      <i class='bx bxs-lock-alt' ></i>
                  </div>
                  <button type="submit" class="btn">Register</button>
              </form>
          </div>

          <div class="toggle-box">
              <div class="toggle-panel toggle-left">
                  <img src="../img_bg/Logo.png" alt="logo" width="300" height="60">
                  <h1>Welcome!</h1>
                  <p>Don't have an account?</p>
                  <button class="btn register-btn">Register</button>
              </div>

              <div class="toggle-panel toggle-right">
                  <img src="../img_bg/Logo.png" alt="logo" width="300" height="60">
                  <h1>Welcome Back!</h1>
                  <p>Already have an account?</p>
                  <button class="btn login-btn">Login</button>
              </div>
          </div>
      </div>
      <script>
        const container = document.querySelector('.container');
        const registerBtn = document.querySelector('.register-btn');
        const loginBtn = document.querySelector('.login-btn');

        registerBtn.addEventListener('click', () => {
            container.classList.add('active');
        })

        loginBtn.addEventListener('click', () => {
            container.classList.remove('active');
        })
      </script>
</body>
</html>