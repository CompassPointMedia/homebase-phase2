<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Organic Tabs</title>
<style type="text/css">
/*
	 Organic Tabs
	 by Chris Coyier
	 http://css-tricks.com
*/

* { margin: 0; padding: 0; }
body { font: 12px Georgia, serif; }
html { overflow-y: scroll; }
a { text-decoration: none; }
a:focus { outline: 0; }
p { font-size: 15px; margin: 0 0 20px 0; }
#page-wrap { width: 440px; margin: 80px auto; }
h1 { font: bold 40px Sans-Serif; margin: 0 0 20px 0; }

/* Generic Utility */
.hide { position: absolute; top: -9999px; left: -9999px; }





/* Specific to example two */

#example-two .list-wrap { background: #eee; padding: 10px; margin: 0 0 15px 0; }

#example-two ul { list-style: none; }
#example-two ul li a { display: block; border-bottom: 1px solid #666; padding: 4px; color: #666; }
#example-two ul li a:hover { background: #333; color: white; }
#example-two ul li:last-child a { border: none; }

#example-two .nav { overflow: hidden; }
#example-two .nav li { width: 97px; float: left; margin: 0 10px 0 0; }
/*#example-two .nav li.last { margin-right: 0; }*/
#example-two .nav li a { display: block; padding: 5px; background: #666; color: white; font-size: 10px; text-align: center; border: 0; }

#example-two li a.current,#example-two li a.current:hover { background-color: #eee !important; color: black; }
#example-two .nav li a:hover, #example-two .nav li a:focus { background: #999;}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" language="javascript">
(function($) {

    $.organicTabs = function(el, options) {
    
        var base = this;
        base.$el = $(el);
        base.$nav = base.$el.find(".nav");
                
        base.init = function() {
        
            base.options = $.extend({},$.organicTabs.defaultOptions, options);
            
            // Accessible hiding fix
            $(".hide").css({
                "position": "relative",
                "top": 0,
                "left": 0,
                "display": "none"
            }); 
            
            base.$nav.on("click", "li > a", function() {
            
                // Figure out current list via CSS class
                var curList = base.$el.find("a.current").attr("href").substring(1),
                
                // List moving to
                    $newList = $(this),
                    
                // Figure out ID of new list
                    listID = $newList.attr("href").substring(1),
                
                // Set outer wrapper height to (static) height of current inner list
                    $allListWrap = base.$el.find(".list-wrap"),
                    curListHeight = $allListWrap.height();
                $allListWrap.height(curListHeight);
                                        
                if ((listID != curList) && ( base.$el.find(":animated").length == 0)) {
                                            
                    // Fade out current list
                    base.$el.find("#"+curList).fadeOut(base.options.speed, function() {
                        
                        // Fade in new list on callback
                        base.$el.find("#"+listID).fadeIn(base.options.speed);
                        
                        // Adjust outer wrapper to fit new list snuggly
                        var newHeight = base.$el.find("#"+listID).height();
                        $allListWrap.animate({
                            height: newHeight
                        });
                        
                        // Remove highlighting - Add to just-clicked tab
                        base.$el.find(".nav li a").removeClass("current");
                        $newList.addClass("current");
                            
                    });
                    
                }   
                
                // Don't behave like a regular link
                // Stop propegation and bubbling
                return false;
            });
            
        };
        base.init();
    };
    
    $.organicTabs.defaultOptions = {
        "speed": 300
    };
    
    $.fn.organicTabs = function(options) {
        return this.each(function() {
            (new $.organicTabs(this, options));
        });
    };
    
})(jQuery);
</script>
<script type="text/javascript" language="javascript">
$(function() {
	$("#example-two").organicTabs({
		"speed": 200
	});
});
/*
soe the a.bookmark maps to the container div id



*/
</script>
</head>

<body>

<div id="example-two">
		
<ul class="nav">
	<li><a href="#featured2" class="current">Featured</a></li>
	<li><a href="#core2">Core</a></li>
	<li><a href="#jquerytuts2">jQuery</a></li>
	<li class="last"><a href="#classics2">Classics</a></li>
</ul>

<div class="list-wrap">
	<div id="featured2">
		<p><a href="http://css-tricks.com/perfect-full-page-background-image/">Full Page Background Images</a></p>
		<p><a href="http://css-tricks.com/designing-for-wordpress-complete-series-downloads/">Designing for WordPress</a></p>
		<p><a href="http://css-tricks.com/build-your-own-social-home/">Build Your Own Social Home!</a></p>
		<p><a href="http://css-tricks.com/absolute-positioning-inside-relative-positioning/">Absolute Positioning Inside Relative Positioning</a></p>
		<p><a href="http://css-tricks.com/ie-css-bugs-thatll-get-you-every-time/">IE CSS Bugs That'll Get You Every Time</a></p>
		<p><a href="http://css-tricks.com/404-best-practices/">404 Best Practices</a></p>
		<p><a href="http://css-tricks.com/date-display-with-sprites/">Date Display with Sprites</a></p>
	</div>
	<div id="core2" class="hide">
		<p><a href="http://css-tricks.com/video-screencasts/58-html-css-the-very-basics/">The VERY Basics of HTML &amp; CSS</a></p>
		<p><a href="http://css-tricks.com/the-difference-between-id-and-class/">Classes and IDs</a></p>
		<p><a href="http://css-tricks.com/the-css-box-model/">The CSS Box Model</a></p>
		<p><a href="http://css-tricks.com/all-about-floats/">All About Floats</a></p>
		<p><a href="http://css-tricks.com/the-css-overflow-property/">CSS Overflow Property</a></p>
		<p><a href="http://css-tricks.com/css-font-size/">CSS Font Size - (px - em - % - pt - keyword)</a></p>
		<p><a href="http://css-tricks.com/css-transparency-settings-for-all-broswers/">CSS Transparency / Opacity</a></p>
		<p><a href="http://css-tricks.com/css-sprites/">CSS Sprites</a></p>
		<p><a href="http://css-tricks.com/nine-techniques-for-css-image-replacement/">CSS Image Replacement</a></p>
		<p><a href="http://css-tricks.com/what-is-vertical-align/">CSS Vertial Align</a></p>
		<p><a href="http://css-tricks.com/the-css-overflow-property/">The CSS Overflow Property</a></p>
	 </div>
	 <div id="jquerytuts2" class="hide">
		<p><a href="http://css-tricks.com/anythingslider-jquery-plugin/">Anything Slider jQuery Plugin</a></p>
		<p><a href="http://css-tricks.com/moving-boxes/">Moving Boxes</a></p>
		<p><a href="http://css-tricks.com/simple-jquery-dropdowns/">Simple jQuery Dropdowns</a></p>
		<p><a href="http://css-tricks.com/creating-a-slick-auto-playing-featured-content-slider/">Featured Content Slider</a></p>
		<p><a href="http://css-tricks.com/startstop-slider/">Start/Stop Slider</a></p>
		<p><a href="http://css-tricks.com/banner-code-displayer-thing/">Banner Code Displayer Thing</a></p>
		<p><a href="http://css-tricks.com/highlight-certain-number-of-characters/">Highlight Certain Number of Characters</a></p>
		<p><a href="http://css-tricks.com/auto-moving-parallax-background/">Auto-Moving Parallax Background</a></p>
	 </div>
	 <div id="classics2" class="hide">
		<p><a href="http://css-tricks.com/css-wishlist/">Top Designers CSS Wishlist</a></p>
		<p><a href="http://css-tricks.com/what-beautiful-html-code-looks-like/">What Beautiful HTML Code Looks Like</a></p>
		<p><a href="http://css-tricks.com/easily-password-protect-a-website-or-subdirectory/">Easily Password Protect a Website or Subdirectory</a></p>
		<p><a href="http://css-tricks.com/how-to-create-an-ie-only-stylesheet/">IE-Only Stylesheets</a></p>
		<p><a href="http://css-tricks.com/ecommerce-considerations/">eCommerce Considerations</a></p>
		<p><a href="http://css-tricks.com/php-for-beginners-building-your-first-simple-cms/">PHP: Build Your First CMS</a></p>
	 </div>	 
</div>

</div>
</body>
</html>
