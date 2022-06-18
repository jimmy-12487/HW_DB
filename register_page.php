
    <!DOCTYPE html>
    <html style="font-size: 16px;">
    <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta name="keywords" content="​&nbsp;FoodPigeon">
    <meta name="description" content="">
    <title>FoodPigeon</title>
    <link rel="stylesheet" href="css/nicepage.css" media="screen">
    <link rel="stylesheet" href="css/Register.css" media="screen">
    <script class="u-script" type="text/javascript" src="jquery.js" defer=""></script>
    <script class="u-script" type="text/javascript" src="nicepage.js" defer=""></script>
    <meta name="generator" content="Nicepage 4.11.3, nicepage.com">
    <link id="u-theme-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i|Open+Sans:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i">
    <meta name="theme-color" content="#478ac9">
    <meta property="og:title" content="Register">
    <meta property="og:type" content="website">
    </head>
    <body class="u-body u-xl-mode">
    <section class="u-align-left u-clearfix u-image u-section-1" id="carousel_6553" data-image-width="150" data-image-height="103">
        <div class="u-clearfix u-layout-wrap u-layout-wrap-1">
        <div class="u-gutter-0 u-layout">
            <div class="u-layout-row">
            <div class="u-size-31">
                <div class="u-layout-row">
                <div class="u-align-right u-container-style u-image u-layout-cell u-size-60 u-image-1" data-image-width="1280" data-image-height="720">
                    <div class="u-container-layout u-container-layout-1">
                    <br>
                    <img class="u-image u-image-default u-preserve-proportions u-image-2" src="images/H1.png" alt="" data-image-width="1500" data-image-height="1500">
                    <br><br><br><br><br><br>
                    <img class="u-image u-image-default u-preserve-proportions u-image-3" src="images/p.png" alt="" data-image-width="2000" data-image-height="2000">
                    <div class="u-custom-color-1 u-shape u-shape-rectangle u-shape-1"></div>
                    <div class="u-black u-shape u-shape-rectangle u-shape-2"></div>
                    <p class="u-text u-text-default u-text-1">
                        <span class="u-text-grey-10">FoodPigeon</span>
                        <span style="font-weight: 700;">
                        <span class="u-text-grey-10"></span>
                        </span>
                    </p>
                    </div>
                </div>
                </div>
            </div>
            <div class="u-size-29">
                <div class="u-layout-col">
                <div class="u-container-style u-layout-cell u-right-cell u-size-60 u-white u-layout-cell-2">
                    <div class="u-container-layout u-container-layout-2">
                    <h1 class="u-text u-text-default u-text-grey-70 u-text-2"><span class="u-icon"></span>&nbsp;FoodPigeon
                    </h1>
                    <div class="u-border-8 u-border-grey-dark-1 u-line u-line-horizontal u-opacity u-opacity-75 u-line-1"></div>
                    <div class="u-align-center u-form u-form-1">
                        <form action="./register.php" method="POST" class="u-clearfix u-form-spacing-10 u-form-vertical u-inner-form" source="custom" name="form" style="padding: 10px;">
                        <div class="u-form-group">
                            
                            <label class="u-label">Account<not id = 'not' style = 'font-size: 6px; color: red;'></not></label>
                            <input type="text" id = 'account' placeholder="your account" name="Account" class="u-border-1 u-border-grey-30 u-input u-input-rectangle u-radius-8 u-white u-input-1" required="required">
                            
                            <label class="u-label">Username</label>
                            <input type="text" id = "username" placeholder="your Username(alphabet only)" name="Username" class="u-border-1 u-border-grey-30 u-input u-input-rectangle u-radius-8 u-white u-input-1" required="required">
                            
                            <label class="u-label">Phonenumber</label>
                            <input type="text" placeholder="your Phonenumber(10 digits)" name="Phonenumber" class="u-border-1 u-border-grey-30 u-input u-input-rectangle u-radius-8 u-white u-input-1" required="required">
                            <label class="u-label">Password</label>
                            <input type="text" placeholder="your Password" name="Password" class="u-border-1 u-border-grey-30 u-input u-input-rectangle u-radius-8 u-white u-input-2" required="">
                            <label class="u-label">Checkpassword</label>
                            <input type="text" placeholder="Retype your Password" name="RetypePassword" class="u-border-1 u-border-grey-30 u-input u-input-rectangle u-radius-8 u-white u-input-2" required="">
                            <label class="u-label">Longitude</label>
                            <input type="text" placeholder="Enter your Longitude" name="Longitude" class="u-border-1 u-border-grey-30 u-input u-input-rectangle u-radius-8 u-white u-input-2" required="">
                            <label class="u-label">Latitude</label>
                            <input type="text" placeholder="Enter your Latitude" name="Latitude" class="u-border-1 u-border-grey-30 u-input u-input-rectangle u-radius-8 u-white u-input-2" required="">
                        </div>

                        <script>
                            $('#account').change( function(){
                                $.ajax({
                                    type: 'GET',
                                    url: 'CheckRegistered.php',
                                    data:{
                                        account: $('#account').val()
                                    },
                                    success: function(msg){
                                        $('#not').html(msg);
                                    }
                                })
                            });
                        </script>

                        <div class="u-align-left u-form-group">
                            <input type="submit" value="submit">
                        </div>
                        </form>
                    </div>
                    <img class="u-image u-image-default u-image-4" src="images/NGSL4.png" alt="" data-image-width="1269" data-image-height="793">
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
        <img class="u-image u-image-default u-image-5" src="images/C.png" alt="" data-image-width="939" data-image-height="615">
        <img class="u-image u-image-default u-image-6" src="images/dd.png" alt="" data-image-width="907" data-image-height="496">
        <img class="u-image u-image-default u-preserve-proportions u-image-7" src="images/U.png" alt="" data-image-width="1200" data-image-height="1200">
    </section>

    </body>
</html>