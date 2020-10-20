<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>video notification</title>    
        <meta name="robots" content="noindex">
        <style type="text/css">
        /* Android 4.4 margin */
        div[style*="margin: 16px 0"] {
        margin: 0 auto !important;
        font-size: 100% !important;
        }
        </style>
        <style type="text/css">
        .video-wrapper {display:none;}
        @import url('https://fonts.googleapis.com/css?family=Open+Sans:400,800');
        /* RESET */
        a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        }
        #outlook a {
        padding: 0;
        }
        body {
        margin: 0 auto !important;
        }
        
        body {
        width: 100% !important;
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
        margin: 0;
        padding: 0;
        }
        .ExternalClass {
        width: 100%;
        }
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
        line-height: 100%;
        }
        #backgroundTable {
        margin: 0;
        padding: 0;
        width: 100% !important;
        line-height: 100% !important;
        }
        th {
        font-weight: normal;
        }
        /*MOBILE TARGETING  */
        @media screen and (max-width: 414px) {
        /* DEFAULT */
        .m-hide {
        display: none !important;
        }
        .m-img {width: 250px !important; height: auto !important;}
        .m-header {font-size: 22px !important;}
        .m-body {font-size: 16px !important;}
        .m-height {height: 25px !important; line-height: 25px !important;}
        .m-block {
        display: block;
        width: 100% !important;
        float: none;
        }
        .m-clear {
        float: none !important;
        }
        .m-center {
        text-align: center !important;
        margin: 0 auto;
        float: none !important;
        }
        .m-left {
        text-align: left !important;
        margin: 0 auto;
        float: none !important
        }
        .m-pull--l {
        float: left !important;
        text-align: left !important;
        }
        .m-pull--r {
        float: right !important;
        text-align: right !important;
        }
        /* GRID SYSTEM */
        .m-span10 {
        width: 100% !important
        }
        .m-span9 {
        width: 90% !important
        }
        .m-span8 {
        width: 88% !important
        }
        .m-span5 {
        width: 50% !important
        }
        /* MARGINS */
        .m-margin--top {
        margin-top: 15px;
        }
        .m-margin--bottom {
        margin-bottom: 15px;
        }
        .m-margin--header {
        margin-bottom: 12px;
        }
        .m-margin--left {
        margin-left: 15px;
        }
        .m-margin--right {
        margin-right: 15px;
        }
        .margin-top-change {
        margin-top: -70px !important;
        }
        .m-height-150 {
        height: 150px !important;
        }
        .m-bg-1 {
        background-size: 80% !important;
        }
        /* FOOTER QUERIES */
        .footer .link {
        display: block !important;
        width: 100% !important;
        background: #EEEEEE;
        text-align: center;
        margin-bottom: 1.5px;
        padding: 10px 0;
        }
        .m-bg-2 {
        background-size: 100% 480px !important;
        }
        .footer .first {
        border-radius: 6px 6px 0 0;
        }
        .footer .last {
        border-radius: 0 0 6px 6px;
        }
        .footer .unsub {
        border-radius: 6px;
        }
        .d-hide {
        display: block !important;
        font-size: 12px !important;
        max-height: none !important;
        line-height: 1.5 !important;
        width: 20px !important;
        }
        /* MISC */
        .show {
        display: block!important;
        margin: 0!important;
        padding: 0!important;
        overflow: visible!important;
        width: auto!important;
        max-height: inherit!important
        }
        }
        /* More Specific Targeting */
        @media only screen and (max-width: 599px) {
        .responsive-img {
        width: 100% !important;
        max-width: 500px !important;
        height: auto !important;
        }
        }
        </style>
        <style>
        .width-610{
    		width: 610px;
    	}
    	.mobile-img{
    		display: none;
    	}
        /*@media (-webkit-min-device-pixel-ratio: 0) and (max-device-width:720px) {*/
        @media (max-width: 720px) {
        	.width-610{
        		width: 100%;
        	}
        	.m-bg-1{
	        	display: none !important;
	        }
	        .mobile-img{
	        	display: block;
	        }
	        .mobile-hide{
	        	display: none;
	        }
        }
        @media (min-width: 0px)  {
        
	        .video-wrapper { display:block !important; line-height: inherit !important; overflow: visible !important; visibility: visible !important; max-height: inherit !important;}
	        .video-fallback { display:none !important; }


        }
        @supports (-webkit-overflow-scrolling:touch) and (color:#ffffffff) {
        div[class^=video-wrapper] { display:block !important; line-height: inherit !important; overflow: visible !important; visibility: visible !important; max-height: inherit !important; }
        div[class^=video-fallback] { display:none!important; }
        }
        #MessageViewBody .video-wrapper { display:block !important; line-height: inherit !important; overflow: visible !important; visibility: visible !important; max-height: inherit !important; }
        #MessageViewBody .video-fallback { display:none !important; }
        /* GRID SYSTEM */
        .rotated {
        transform: rotate(-7deg);
        }
        /*Animation*/
        .zoomInUp {
        -webkit-animation-name: zoomInUp;
        animation-name: zoomInUp;
        -webkit-animation-duration: 1.5s;
        animation-duration: 1.5s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
        }
        @-webkit-keyframes zoomInUp {
        0% {
        opacity: 0;
        -webkit-transform: scale3d(.1, .1, .1) translate3d(0, 1000px, 0);
        transform: scale3d(.1, .1, .1) translate3d(0, 1000px, 0);
        -webkit-animation-timing-function: cubic-bezier(0.550, 0.055, 0.675, 0.190);
        animation-timing-function: cubic-bezier(0.550, 0.055, 0.675, 0.190);
        }
        60% {
        opacity: 1;
        -webkit-transform: scale3d(.475, .475, .475) translate3d(0, -60px, 0);
        transform: scale3d(.475, .475, .475) translate3d(0, -60px, 0);
        -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.320, 1);
        animation-timing-function: cubic-bezier(0.175, 0.885, 0.320, 1);
        }
        }
        @keyframes zoomInUp {
        0% {
        opacity: 0;
        -webkit-transform: scale3d(.1, .1, .1) translate3d(0, 1000px, 0);
        transform: scale3d(.1, .1, .1) translate3d(0, 1000px, 0);
        -webkit-animation-timing-function: cubic-bezier(0.550, 0.055, 0.675, 0.190);
        animation-timing-function: cubic-bezier(0.550, 0.055, 0.675, 0.190);
        }
        60% {
        opacity: 1;
        -webkit-transform: scale3d(.475, .475, .475) translate3d(0, -60px, 0);
        transform: scale3d(.475, .475, .475) translate3d(0, -60px, 0);
        -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.320, 1);
        animation-timing-function: cubic-bezier(0.175, 0.885, 0.320, 1);
        }
        }
        </style>
    </head>
    <body style="padding: 0; margin: 0;">
        <!-- Wrapper Table-->
        <table id="backgroundTable" width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%; table-layout:fixed; margin: 0 auto; padding:0;">
            <tr>
                <td align="center">
                    <!--LOGO & NUMBER-->
                    <table width="100%" cellspacing="0" cellpadding="0" class="header" style="border-collapse: collapse; width: 100%;" bgcolor="#FFFFFF">
                        <tr>
                            <td align="center" style="border-collapse: collapse;">
                                <table cellspacing="0" cellpadding="0" class="m-span9 width-610" style="border-collapse: collapse;">
                                    <tr>
                                        <td colspan="2" height="15" style="border-collapse: collapse; height:15px;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="m-block" width="100%" align="center" style="border-collapse: collapse; width:100%">
                                            <a target="_blank" class="logo m-center" href="#"><img alt="Rentalcars.com"  src="{{asset('images/streamtube.png')}}" style="display: block; border: 0 none; font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#0E94F7;width: 180px;margin: 0 auto"></a>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan="2" height="15" style="border-collapse: collapse; height:15px;">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!--[END] LOGO & NUMBER-->
                    <div class="video-wrapper m-span10 m-bg-2 width-610" background="{{asset('images/bg-blue2.jpg')}}" style="background-image: url({{asset('images/bg-blue2.jpg')}}); overflow: hidden; visibility: hidden; max-height: 0; line-height: 0; background-repeat: no-repeat; background-position: top center; background-size: 100%; display: none; margin: 0 auto; ">
                        <div>
                            
                            <table class="m-span9 width-610" align="center" border="0" cellspacing="0" cellpadding="0" style="">
                                <tbody>
                                    
                                    <tr>
                                        <td colspan="3" height="25" style="height: 35px; line-height: 35px; font-size: 35px; mso-line-height-rule: exactly;" colspan="1">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td width="15" style="width: 15px;"></td>
                                        <td class="m-body" align="center" style="color:#FFFFFF; font-size:18px;font-family:Arial, Helvetica, Sans-serif; line-height: 1.1; font-weight: 400;"><span style="font-family: 'Open Sans', Helvetica, Arial, sans-serif !important; font-weight: 400;">Lorem Ipsum has been the industry's standard dummy text </span></td>
                                        <td width="15" style="width: 15px;"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="15" style="height: 15px; line-height: 15px; font-size: 15px; mso-line-height-rule: exactly;" colspan="1">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td width="15" style="width: 15px;"></td>
                                        <td class="m-header" align="center" style="color:#FFFFFF; font-size:30px;font-family:Arial, Helvetica, Sans-serif; line-height: 1.1; font-weight: 800;"><span style="font-family: 'Open Sans', Helvetica, Arial, sans-serif !important; font-weight: 800;">Lorem Ipsum has been the industry's standard dummy text ever since the 1500</span></td>
                                        <td width="15" style="width: 15px;"></td>
                                    </tr>
                                    <tr>
                                        <td class="m-height" height="50" style="height: 50px; line-height: 50px; font-size: 50px; mso-line-height-rule: exactly;" colspan="3">&nbsp;</td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                        
                        <div>
                        	<table class="m-span9" align="center" border="0" cellspacing="0" cellpadding="0" style="width:100%;">
                        		<tbody>
                                    <tr>
                                    	<td align="center">
                                            <div>
                                                <a href=""><img src="{{asset('images/christmas-poster.png')}}" style="width: 90%;border:10px solid #fff; height:215px;object-fit: cover;margin-bottom: 20px;" class="mobile-img"></a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                        	</table>
                        </div>
                        <div class="m-span9 m-bg-1" background="{{asset('images/envelopebg.png')}}" style="background-image: url({{asset('images/envelopebg.png')}}); width: 500px; background-repeat: no-repeat; background-position: top center; background-size: 90%; margin: 0 auto; ">
                            <table class="m-span9" align="center" width="550" border="0" cellspacing="0" cellpadding="0" style="width: 610px;">
                                <tbody>
                                    <tr>
                                        <td height="50" style="height: 50px; line-height: 50px; font-size: 50px; mso-line-height-rule: exactly;" colspan="1">&nbsp;</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div>
                                
                                <table class="zoomInUp rotated m-span9" align="center" width="400" border="0" cellspacing="0" cellpadding="0" style="width: 400px; z-index: 2; position: relative;">
                                    <tbody>
                                        <tr>
                                            <td align="center">
                                                <div>
                                                    <a href=""><img src="{{asset('images/christmas-poster.png')}}" style="width: 100%;border:10px solid #fff;height: 215px;background-color: #fff;object-fit: cover;object-position: center;"></a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <table class="m-span9 margin-top-change" align="center" width="500" border="0" cellspacing="0" cellpadding="0" style="width: 500px; margin-top: -140px;">
                                <tbody>
                                    <tr>
                                        <td align="center" width="50%">
                                            <img src="{{asset('images/1.png')}}" alt="" width="250" height="" style="display: block; border: 0; z-index: 1; position: relative;" class="m-span10"/>
                                        </td>
                                        <td align="center" width="50%">
                                            <img src="{{asset('images/2.png')}}" alt="" width="250" height="" style="display: block; border: 0; z-index: 3; position: relative;" class="m-span10"/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                    <!--<![endif]-->
                    <!--[if !mso 9]><!-->
                    <div class="show" style="display:none;margin:0;padding:0;width:0;max-height:0;overflow:hidden; mso-hide:all;">
                        <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td height="50" style="height: 50px; line-height: 50px; font-size: 50px; mso-line-height-rule: exactly;" colspan="1">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[END] CONTENT-->

                    <!-- POPULAR DESTINATIONS -->
                    <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" class="m-span9 width-610" >
                        <tbody>
                            <tr>
                                <td colspan="3" height="25" style="height: 25px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="15" style="width: 15px;"></td>
                                <td align="center" style="color:#444444; font-size:30px;font-family:Arial, Helvetica, Sans-serif; line-height: 1.1; font-weight: 800;"><span style="font-family: 'Open Sans', Helvetica, Arial, sans-serif !important; font-weight: 800;">Lorem Ipsum is simply dummy</span></td>
                                <td width="15" style="width: 15px;"></td>
                            </tr>
                            <tr>
                                <td height="10" style="height: 10px; line-height: 10px; font-size: 10px; mso-line-height-rule: exactly;" colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="15" style="width: 15px;"></td>
                                <td align="center" style="font-family: arial, Helvetica, sans-serif; font-size: 16px; color: #444444; line-height:1.4; text-align: center;">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries</td>
                                <td width="15" style="width: 15px;"></td>
                            </tr>
                            <tr>
                                <td colspan="3" height="25" style="height: 25px; line-height: 25px; font-size: 25px; mso-line-height-rule: exactly;" colspan="1">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <table align="center" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" style="border-radius: 3px;" bgcolor="#cc181e">
                                                <a target="_blank" href="#" target="_blank" style="font-size: 16px; font-family: arial, helvetica, sans-serif; color: #FFFFFF; text-decoration: none; border-radius: 3px; padding:10px 20px 10px 20px; border: 1px solid #cc181e; display: inline-block; text-align: center;">Watch video</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td height="35" style="height: 35px; line-height: 35px; font-size: 35px; mso-line-height-rule: exactly;" colspan="1">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                    <table cellpadding="0" align="center" bgcolor="#ffffff" cellspacing="0" style="border-collapse: collapse; width: 100%;">
                        <tr>
                            <td align="center" style="border-collapse: collapse; width: 100%;">
                                <table class="m-span10 width-610" align="center" style="border-collapse: collapse; background: #0e94f7;">
                                    <tr>
                                        <td>
                                            <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tbody>
                                                    <tr>
                                                        <td height="10"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tbody>
                                                                    <tr style="text-align: center;">
                                                                        <th class="m-block m-center m-margin--bottom" align="center" style="font-family: Arial, Sans-serif; font-size: 12px; color: #ffffff;"> <a target="_blank" style="font-family: Arial, Sans-Serif; color: #FFFFFF; text-decoration: none;" href="#">About Us</a> | <a target="_blank" style="font-family: Arial, Sans-Serif; color: #FFFFFF; text-decoration: none;" href="#">Terms and Conditions</a> | <a target="_blank" style="font-family: Arial, Sans-Serif; color: #FFFFFF; text-decoration: none;" href="#">Privacy Policy</a> | <a target="_blank" style="font-family: Arial, Sans-Serif; color: #FFFFFF; text-decoration: none;" href="#">Help</a> </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="10" class="mobile-hide"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!-- NEW FOOTER -->
                    <table bgcolor="#FFFFFF" cellspacing="0" cellpadding="0" class="footer" style="border-collapse: collapse; width: 100%;">
                        <tr>
                            <td height="15" style="height:15px;"></td>
                        </tr>
                        <tr>
                            <td align="center" style="border-collapse: collapse;">
                                <table cellspacing="0" cellpadding="0" class="m-span9 width-610" style="border-collapse: collapse;">
                                    <tr>
                                        <td colspan="2" style="font-size: 14px; color: #919191; line-height: 1.5; font-family:Helvetica, Arial, Sans-serif; font-weight: normal; text-align:left;">
                                            <br> Copyright © 2018. All rights reserved
                                            <br/>
                                        <br/> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" height="15" style="height:15px;"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!--[END] FOOTER -->
                </td>
            </tr>
        </table>
        <!--[END] Wrapper Table -->
      
    </body>
</html>