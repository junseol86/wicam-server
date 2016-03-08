<?php
    $src = $_GET['src'];
?>
<html>
    <head>
        <script language="JavaScript">
            var imageWidth;
            var imageHeight;
            var windowWidth;
            var windowHeight;
            var imageAdjustedWidth;
            var imageAdjustedHeight;
            var leftMargin;
            var topMargin;

            function loadImage() {
                var image = new Image();
                image.src = "<?php echo $src; ?>"
                image.onload = imageDimension;
            }

            function imageDimension() {
                imageWidth = this.width;
                imageHeight = this.height;
                fitToWindow();
            }

            function fitToWindow() {
                if(window.screen != null) {
                    windowWidth = window.screen.availWidth;
                    windowHeight = window.screen.availHeight;
                }

                if(window.innerWidth != null) {
                    windowWidth = window.innerWidth;
                    windowHeight = window.innerHeight;
                }

                if(document.body != null) {
                    windowWidth = document.body.clientWidth;
                    windowHeight = document.body.clientHeight;
                }

                if (windowHeight > (imageHeight * windowWidth / imageWidth)) {
                    imageAdjustedWidth = windowWidth;
                    imageAdjustedHeight = imageHeight * windowWidth / imageWidth;
                    leftMargin = (windowWidth - imageAdjustedWidth) / 2;
                    topMargin = (windowHeight - imageAdjustedHeight) / 2;
                }
                else {
                    imageAdjustedHeight = windowHeight;
                    imageAdjustedWidth = imageWidth * windowHeight / imageHeight;
                    leftMargin = (windowWidth - imageAdjustedWidth) / 2;
                    topMargin = (windowHeight - imageAdjustedHeight) / 2;
                }

                document.getElementById("the_image").style.width = imageAdjustedWidth + "px";
                document.getElementById("the_image").style.height = imageAdjustedHeight + "px";
                document.getElementById("the_image").style.marginLeft = leftMargin + "px";
                document.getElementById("the_image").style.marginTop = topMargin + "px";
            }

        </script>

    </head>
    <body onload="loadImage()" style="margin: 0px; padding: 0px; background-color: #000000">
        <img id="the_image" src="<?php echo $src; ?>">
    </body>

</html>