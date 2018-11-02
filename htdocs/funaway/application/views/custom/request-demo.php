<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <style media="screen">
      body{font-family: sans-serif; font-family: 14px;}
      *{box-sizing: border-box;}
      .modal {position: fixed; top: 100px; left: 50%; right: auto; bottom: auto; transform: translate(-50%, 0); z-index: 990; display: none; max-height: calc(100% - 100px);}
      .modal .modal-body {height: 100%; margin: 20px;}
      .modal-overlay { background-color: rgba(0, 0, 0, 0.85); display: none; position: fixed; top: 0; right: 0; bottom: 0; left: 0; z-index: 980; }
      .modal.modal-is-visible {display: block; z-index: 1010;}
      .modal-overlay.modal-is-visible {display: block; z-index: 1009;}

      /* Custom Popup */
      .popup{padding: 50px;}

      .popup.-bg {position: relative; padding-bottom: 50px; background: #fff url(./images/bg-dot.png) repeat 0 0;}
      .popup.-bg:after {position: absolute; bottom: 0; left: 0; right: 0; height: 131px; width: 100%; content: ""; background: url(./images/floating-layer.png) no-repeat center bottom;}
      .popup.-bg:before {position: absolute;top: 0; left: 0; right: 0; height: 131px; width: 100%; content: "";
        background: rgb(255, 255, 255); background: -moz-linear-gradient(0deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
        background: -webkit-linear-gradient(0deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%); background: linear-gradient(0deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff", endColorstr="#ffffff", GradientType=1);}

      .popup-content {position: relative; z-index: 1;}
      .popup__crossed {position: absolute; z-index: 1; position: absolute; top: 10px; right: 10px; padding: 0px; -ms-transform: rotate(45deg); -webkit-transform: rotate(45deg); transform: rotate(45deg);
        width: 20px; height: 20px; text-align: center; line-height: 18px;}
        .popup__crossed:before {position: absolute; top: 0; left: 50%; width: 2px; height: 22px; margin: 0 0 0 -1px; background: #000; content: "";}
        .popup__crossed:after {position: absolute; left: 0px; top: 50%; width: 22px; height: 2px; margin: -0px 0 0 0; background: #000; content: "";}
      .popup__btn{position: relative; display: inline-block; vertical-align: middle; margin-bottom: 0; font-weight: normal; text-align: center; touch-action: manipulation; cursor: pointer; white-space: normal;
          padding: 10px 20px; font-size: 1.1em; line-height: 1.42857; border-radius: 2px; text-decoration: none; background-image: none; border: solid 1px transparent;color: #fff; background: none;}

      .popup__logo {margin-bottom: 30px; text-align: center;}
      .popup__logo img {display: inline-block;}

      .started-wrapper {display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; margin-bottom: 50px;}
      .started-box {position: relative; flex: 1; max-width: 350px; padding: 0 50px; text-align: center;}

      .started-box:first-child:after {position: absolute; top: 50%; right: 0; width: 1px; height: 190px; margin-top: -95px; background: #d9dade; content: "";}

      .started-box .graphic {margin-bottom: 30px;}
      .started-box .graphic img {display: inline-block;}
      .started-box p{font-size: 0.9em; line-height: 1.5; opacity: 0.7;}
      .started-box .popup__btn {min-width: 200px;}
      .started-box .btn--primary {background: #00B3A4; border: none; color: #fff;}
      .started-box .btn--secondary {background: #cc3b0a; border: none;color:#fff;}

      .view-packages__btn {padding: 10px; padding-right: 35px; font-size: 16px; font-weight: 400; text-decoration: none; background: url(../images/arrow-black.png) no-repeat right center; color: #000;}

      @media only screen and (max-width:767px) {
        .popup {overflow-y: scroll;}

        .started-wrapper {flex-direction: column;}
        .started-box:first-child{margin-bottom: 30px;}
        .started-box:first-child:after{display: none;}
      }
	  .popup__logo img {
			max-width: 150px;
	  }
  </style>
</head>
  <body>
    <div class="modal modal-is-visible">
      <div class="modal-body">
        <div class="popup -bg" style="max-width: 700px; margin:auto">
          <!--a href="#" class="popup__crossed"></a-->
          <div class="popup-content">
              <div class="popup__logo"> 
			  
			  <img src="http://demo.fun-away.com/image/company-logo/conf_website_logo" alt="">
			  </div>
              <div class="started-wrapper">
                <div class="started-box">
                  <div class="graphic"><img src="<?php echo CONF_WEBROOT_URL; ?>images/get-started.png" alt=""></div>
                  <p>I Really Liked The Features Of Funaway And Want To Discuss My Project</p>
                  <a href="https://www.fatbit.com/website-design-company/requestaquote.html#demo" target="_blank" class="popup__btn btn--primary">Get Started</a>
                </div>
                <div class="started-box">
                  <div class="graphic"><img src="<?php echo CONF_WEBROOT_URL; ?>images/free-demo.png" alt=""></div>
                  <p>I Want To Learn More About The Product And Need A Personalized Live Demo</p>
                  <a href="https://www.fatbit.com/online-travel-activity-marketplace-solution.html#requestdemo" target="_blank" class="popup__btn btn--secondary">Book A Free Demo</a>
                </div>
              </div>
              <div style="text-align:center;"> <a href="http://www.fun-away.com/" target="_blank" class="view-packages__btn">Know More About FunAway</a></div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-overlay modal-is-visible"></div>
  </body>
</html>
