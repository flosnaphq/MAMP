<!DOCTYPE HTML>

<html>
    <head>
        <title><?php echo FatApp::getConfig('conf_website_title');?> | <?php echo Info::t_lang('ERROR_404');?></title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="shortcut icon" href="assets/favicons/favicon.ico">
        <link rel="apple-touch-icon" sizes="57x57" href="assets/favicons/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="assets/favicons/apple-touch-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="assets/favicons/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="assets/favicons/apple-touch-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="assets/favicons/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="assets/favicons/apple-touch-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="assets/favicons/apple-touch-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="assets/favicons/apple-touch-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="assets/favicons/apple-touch-icon-180x180.png">
        <link rel="icon" type="image/png" href="assets/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/favicons/favicon-192x192.png" sizes="192x192">
        <link rel="icon" type="image/png" href="assets/favicons/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/png" href="assets/favicons/android-chrome-192x192.png" sizes="192x192">
        <link rel="icon" type="image/png" href="assets/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="assets/favicons/manifest.json">
        <link rel="mask-icon" href="assets/favicons/safari-pinned-tab.svg" color="#000000">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="assets/favicons/mstile-144x144.png">
        <meta name="theme-color" content="#ffffff">

        <!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
        <style>
            * {
              -ms-box-sizing: border-box;
              -moz-box-sizing: border-box;
              -webkit-box-sizing: border-box;
              box-sizing: border-box;
              outline: none;
            }

            span svg {
              display: inline;
            }

            img {
              display: inline-block;
              vertical-align: middle;
              max-width: 100%;
            }

            /*! normalize.css v4.0.0 | MIT License | github.com/necolas/normalize.css */
            progress, sub, sup {
              vertical-align: baseline;
            }

            button, hr, input, select {
              overflow: visible;
            }

            [type=checkbox], [type=radio], legend {
              box-sizing: border-box;
              padding: 0;
            }

            html {
              font-family: sans-serif;
              -ms-text-size-adjust: 100%;
              -webkit-text-size-adjust: 100%;
            }

            body {
              margin: 0;
            }

            article, aside, details, figcaption, figure, footer, header, main, menu, nav, section, summary {
              display: block;
            }

            audio, canvas, progress, video {
              display: inline-block;
            }

            audio:not([controls]) {
              display: none;
              height: 0;
            }

            [hidden], template {
              display: none;
            }

            a {
              background-color: transparent;
            }

            a:active, a:hover {
              outline-width: 0;
            }

            abbr[title] {
              border-bottom: none;
              text-decoration: underline;
              text-decoration: underline dotted;
            }

            b, strong {
              font-weight: bolder;
            }

            dfn {
              font-style: italic;
            }

            h1 {
              font-size: 2em;
              margin: .67em 0;
            }

            mark {
              background-color: #ff0;
              color: #000;
            }

            small {
              font-size: 80%;
            }

            sub, sup {
              font-size: 75%;
              line-height: 0;
              position: relative;
            }

            sub {
              bottom: -.25em;
            }

            sup {
              top: -.5em;
            }

            img {
              border-style: none;
            }

            svg:not(:root) {
              overflow: hidden;
            }

            code, kbd, pre, samp {
              font-family: monospace,monospace;
              font-size: 1em;
            }

            figure {
              margin: 1em 40px;
            }

            hr {
              box-sizing: content-box;
              height: 0;
            }

            button, input, select, textarea {
              font: inherit;
              margin: 0;
            }

            optgroup {
              font-weight: 700;
            }

            button, select {
              text-transform: none;
            }

            [type=button], [type=reset], [type=submit], button {
              cursor: pointer;
            }

            [disabled] {
              cursor: default;
            }

            [type=reset], [type=submit], button, html [type=button] {
              -webkit-appearance: button;
            }

            button::-moz-focus-inner, input::-moz-focus-inner {
              border: 0;
              padding: 0;
            }

            button:-moz-focusring, input:-moz-focusring {
              outline: ButtonText dotted 1px;
            }

            fieldset {
              border: 1px solid silver;
              margin: 0 2px;
              padding: .35em .625em .75em;
            }

            legend {
              color: inherit;
              display: table;
              max-width: 100%;
              white-space: normal;
            }

            textarea {
              overflow: auto;
            }

            [type=number]::-webkit-inner-spin-button, [type=number]::-webkit-outer-spin-button {
              height: auto;
            }

            [type=search] {
              -webkit-appearance: textfield;
            }

            [type=search]::-webkit-search-cancel-button, [type=search]::-webkit-search-decoration {
              -webkit-appearance: none;
            }

            @-ms-viewport {
              width: device-width;
            }
            body {
              -ms-overflow-style: scrollbar;
            }

            /* Button */
            .buttons__group {
              white-space: nowrap;
            }

            .s-button {
              display: inline-block;
              width: 100%;
              padding: 0 0.71429em;
              line-height: 3.57143em;
              min-height: calc(3.57143em + 2px);
              min-width: 3.57143em;
              border-radius: 0;
              font-size: 0.875em;
              text-align: center;
              color: inherit;
              background-color: #fff;
              border: 1px solid #e3e9f0;
              transition: border 0.3s linear, background 0.3s linear;
              -webkit-appearance: none;
              -moz-appearance: none;
              appearance: none;
              text-transform: uppercase;
            }
            .s-button::-ms-expand {
              display: none;
            }

            .button {
              -moz-appearance: none;
              -webkit-appearance: none;
              appearance: none;
              cursor: pointer;
              display: inline-block;
              height: calc(2.8125em);
              font-family: "solitas_norm_regular", sans-serif;
              font-size: 1em;
              font-weight: normal;
              letter-spacing: 0.3125em;
              line-height: 2.8125em;
              padding: 0 1.21875em 0 1.40625em;
              text-align: center;
              text-decoration: none;
              text-transform: uppercase;
              white-space: nowrap;
              -webkit-border-radius: 2.8125em;
              border-radius: 2.8125em;
              border: 1px solid transparent;
              background-color: transparent;
              color: #00153b;
              min-width: 7.5em;
              -webkit-transition: background-color 0.2s ease-in-out;
              transition: background-color 0.2s ease-in-out;
            }
            .button.button--fit {
              display: block;
              margin: 0 0 1.25em 0;
              width: 100%;
            }
            .button.button--small {
              font-size: 0.7em;
            }
            .button.button--large {
              font-size: 1.3em;
            }
            .button.button--focus, .button:focus {
              box-shadow: 0 0 0 5px rgba(171, 192, 220, 0.1);
            }
            .button.button--fill {
              color: #ffffff;
            }
            .button.button--fill:hover, .button.button--fill:active {
              border-color: rgba(255, 255, 255, 0.2);
            }
            .button.button--fill.button--red {
              background-color: #d03e2a;
            }
            .button.button--fill.button--red:hover, .button.button--fill.button--red:active {
              background-color: #00153b;
            }

            @media screen and (min-width: 30em) {
              .button:not(:first-of-type) {
                margin-left: 0.625em;
              }
            }
            @media screen and (max-width: 63.9375em) {
              .button {
                padding: 0 1.21875em 0 1.40625em;
                min-width: 2.8125em;
              }
            }
            @media screen and (max-width: 29.9375em) {
              .button {
                display: block;
                width: 100%;
                height: auto;
                white-space: normal;
              }
              .button:not(:first-of-type) {
                margin-top: 0.625em;
              }
            }

            .error__block {
              position: relative;
              display: block;
			  padding:0 1.25em 1.25em;
              text-align: center;
              overflow: hidden;
              background-color: #67c9d3;
              color: #ffffff;
            }
            .error__block__inner {
              display: table;
              width: 100%;
              height: 62.5em;
              height: 100vh;
            }
            .error__block .circle {
              position: absolute;
              display: block;
              top: 50%;
              left: 50%;
              -webkit-transform: translate(-50%, -50%);
              transform: translate(-50%, -50%);
              display: block;
              -webkit-border-radius: 100%;
              border-radius: 100%;
              background-color: currentColor;
              opacity: 0.1;
            }
            .error__block .circle:nth-child(1) {
              width: 100%;
              padding-bottom: 100%;
            }
            .error__block .circle:nth-child(2) {
              width: 80%;
              padding-bottom: 80%;
            }
            .error__block .circle:nth-child(3) {
              width: 50%;
              padding-bottom: 50%;
            }
            .error__block__content {
              position: relative;
              display: table-cell;
              vertical-align: middle;
              width: 100%;
              height: 100%;
              z-index: 5;
            }
            .error__block .error__image svg {
              display: inline-block;
              width: 100%;
              height: 100%;
              max-width: 50em;
              max-height: 22.8125em;
              fill: currentColor;
            }
            @media screen and (-webkit-min-device-pixel-ratio: 0) {
              .error__block .error__image svg {
                /* Safari 5+ ONLY */
              }
              .error__block .error__image svg ::i-block-chrome, .error__block .error__image svg {
                width: 50em;
                height: 22.8125em;
              }
            }
            .error__block .error__heading {
              margin: 0;
              padding: 0;
              font-family: "solitas_ext_demi", sans-serif;
              font-size: 27.5em;
              font-size: 20vw;
              line-height: 1;
            }
            @media screen and (max-width: 101.1875em) {
              .error__block .error__heading {
                font-size: 13.75em;
                font-size: 25vw;
              }
            }
            @media screen and (max-width: 79.9375em) {
              .error__block .error__heading {
                font-size: 6.875em;
                font-size: 30vw;
              }
            }
            .error__block .error__sub-heading {
              font-family: "solitas_ext_light", sans-serif;
              font-size: 1.5em;
              line-height: 1;
              letter-spacing: 0.3125em;
              text-transform: uppercase;
            }
            @media screen and (max-width: 79.9375em) {
              .error__block .error__sub-heading {
                font-size: 1.25em;
              }
            }
            @media screen and (max-width: 63.9375em) {
              .error__block .error__sub-heading {
                font-size: 1.0625em;
              }
            }
        </style>
        <script src="assets/js/vendors/modernizr.min.js"></script>
    </head>
    <body class="is--404">
    
    <div class="error__block">
    <div class="error__block__inner">
        <span class="circle"></span>
        <span class="circle"></span>
        <span class="circle"></span>
        <div class="error__block__content">
            <h6 class="error__sub-heading"><?php echo Info::t_lang('Oops_nothing_found');?></h6>
            <div class="error__image">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="800" height="365" viewBox="0 0 834.255 380.906">
                  <path d="M819.735,325.016 L801.255,325.016 L801.255,351.856 C801.255,366.816 796.415,371.656 781.015,371.656 L748.455,371.656 C733.495,371.656 728.655,366.816 728.655,351.856 L728.655,325.016 L578.174,325.016 C566.294,325.016 562.774,320.616 562.774,310.936 L562.774,285.856 C562.774,276.175 564.974,273.095 572.894,260.775 L693.015,68.054 C699.175,58.374 707.535,54.854 717.215,54.854 L764.295,54.854 C776.615,54.854 779.255,62.774 771.775,74.214 L646.815,266.055 L728.655,266.055 L728.655,193.895 C728.655,178.935 733.495,174.095 748.455,174.095 L781.015,174.095 C796.415,174.095 801.255,178.935 801.255,193.895 L801.255,266.055 L819.735,266.055 C831.176,266.055 834.256,270.015 834.256,280.575 L834.256,310.936 C834.256,320.616 831.176,325.016 819.735,325.016 ZM403.501,380.896 C362.839,380.896 333.798,368.203 313.398,348.256 L354.600,285.866 C363.507,306.604 378.667,321.056 403.501,321.056 C452.781,321.056 464.221,265.175 464.221,215.455 C464.221,189.235 460.982,161.148 450.528,140.602 L490.647,79.851 C526.706,112.413 537.702,166.457 537.702,215.455 C537.702,292.456 510.861,380.896 403.501,380.896 ZM432.501,60.295 C383.660,60.295 372.220,116.175 372.220,165.455 C372.220,189.894 374.989,215.818 383.600,235.866 L342.398,298.256 C308.937,265.538 298.740,213.292 298.740,165.455 C298.740,88.895 325.140,0.014 432.501,0.014 C471.151,0.014 499.364,11.535 519.647,29.851 L479.528,90.602 C470.335,72.532 455.561,60.295 432.501,60.295 ZM256.946,275.016 L238.466,275.016 L238.466,301.856 C238.466,316.816 233.626,321.656 218.226,321.656 L185.666,321.656 C170.706,321.656 165.866,316.816 165.866,301.856 L165.866,275.016 L15.385,275.016 C3.505,275.016 -0.015,270.616 -0.015,260.936 L-0.015,235.856 C-0.015,226.175 2.185,223.095 10.105,210.775 L130.226,18.054 C136.386,8.374 144.746,4.854 154.426,4.854 L201.506,4.854 C213.826,4.854 216.466,12.774 208.986,24.214 L84.026,216.055 L165.866,216.055 L165.866,143.895 C165.866,128.935 170.706,124.095 185.666,124.095 L218.226,124.095 C233.626,124.095 238.466,128.935 238.466,143.895 L238.466,216.055 L256.946,216.055 C268.387,216.055 271.467,220.015 271.467,230.575 L271.467,260.936 C271.467,270.616 268.387,275.016 256.946,275.016 Z"/>
                </svg>
            </div>
            <a href="<?php echo FatUtility::generateUrl();?>" class="button button--fill button--red" style="margin-top:1.5em;"><?php echo Info::t_lang('BACK_TO_HOME');?></a>
        </div>
    </div>
    </div>
                   

    </body>
</html>
