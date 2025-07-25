$(function() {
    "use strict";
    
    // Initialize PerfectScrollbar
    const sidebarElement = document.querySelector('.sidebar-wrapper');
    if (sidebarElement) {
        new PerfectScrollbar('.sidebar-wrapper');
    }

    const pageContentElement = document.querySelector('.page-content');
    if (pageContentElement) {
        new PerfectScrollbar('.page-content');
    }

    // Mobile Search
    $(".mobile-search-icon").on("click", function() {
        $(".search-bar").addClass("full-search-bar");
    });

    $(".search-close").on("click", function() {
        $(".search-bar").removeClass("full-search-bar");
    });

    // Mobile Toggle Menu
    $(".mobile-toggle-menu").on("click", function() {
        $(".wrapper").addClass("toggled");
    });

    // Dark Mode Toggle
    $(".dark-mode").on("click", function() {
        if($(".dark-mode-icon i").attr("class") == 'bx bx-sun') {
            $(".dark-mode-icon i").attr("class", "bx bx-moon");
            $("html").attr("class", "light-theme");
        } else {
            $(".dark-mode-icon i").attr("class", "bx bx-sun");
            $("html").attr("class", "dark-theme");
        }
    });

    // Sidebar Toggle
    $(".toggle-icon").click(function() {
        if ($(".wrapper").hasClass("toggled")) {
            $(".wrapper").removeClass("toggled");
            $(".sidebar-wrapper").unbind("hover");
        } else {
            $(".wrapper").addClass("toggled");
            $(".sidebar-wrapper").hover(function() {
                $(".wrapper").addClass("sidebar-hovered");
            }, function() {
                $(".wrapper").removeClass("sidebar-hovered");
            });
        }
    });

    // Back to Top
    $(window).on("scroll", function() {
        $(this).scrollTop() > 300 ? $(".back-to-top").fadeIn() : $(".back-to-top").fadeOut();
    });
    
    $(".back-to-top").on("click", function() {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });

    // Active Menu
    for (var e = window.location, o = $(".metismenu li a").filter(function() {
        return this.href == e;
    }).addClass("").parent().addClass("mm-active"); o.is("li");) {
        o = o.parent("").addClass("mm-show").parent("").addClass("mm-active");
    }

    // Initialize MetisMenu
    $("#menu").metisMenu();

    // Chat Toggle
    $(".chat-toggle-btn").on("click", function() {
        $(".chat-wrapper").toggleClass("chat-toggled");
    });
    
    $(".chat-toggle-btn-mobile").on("click", function() {
        $(".chat-wrapper").removeClass("chat-toggled");
    });

    // Email Toggle
    $(".email-toggle-btn").on("click", function() {
        $(".email-wrapper").toggleClass("email-toggled");
    });
    
    $(".email-toggle-btn-mobile").on("click", function() {
        $(".email-wrapper").removeClass("email-toggled");
    });
    
    $(".compose-mail-btn").on("click", function() {
        $(".compose-mail-popup").show();
    });
    
    $(".compose-mail-close").on("click", function() {
        $(".compose-mail-popup").hide();
    });

    // Theme Switcher
    $(".switcher-btn").on("click", function() {
        $(".switcher-wrapper").toggleClass("switcher-toggled");
    });
    
    $(".close-switcher").on("click", function() {
        $(".switcher-wrapper").removeClass("switcher-toggled");
    });

    // Theme Settings
    $("#lightmode").on("click", function() {
        $("html").attr("class", "light-theme");
    });
    
    $("#darkmode").on("click", function() {
        $("html").attr("class", "dark-theme");
    });
    
    $("#semidark").on("click", function() {
        $("html").attr("class", "semi-dark");
    });
    
    $("#minimaltheme").on("click", function() {
        $("html").attr("class", "minimal-theme");
    });

    // Header Colors
    $("#headercolor1").on("click", function() {
        $("html").addClass("color-header headercolor1").removeClass("headercolor2 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8");
    });
    
    $("#headercolor2").on("click", function() {
        $("html").addClass("color-header headercolor2").removeClass("headercolor1 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8");
    });
    
    $("#headercolor3").on("click", function() {
        $("html").addClass("color-header headercolor3").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8");
    });
    
    $("#headercolor4").on("click", function() {
        $("html").addClass("color-header headercolor4").removeClass("headercolor1 headercolor2 headercolor3 headercolor5 headercolor6 headercolor7 headercolor8");
    });
    
    $("#headercolor5").on("click", function() {
        $("html").addClass("color-header headercolor5").removeClass("headercolor1 headercolor2 headercolor4 headercolor3 headercolor6 headercolor7 headercolor8");
    });
    
    $("#headercolor6").on("click", function() {
        $("html").addClass("color-header headercolor6").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor3 headercolor7 headercolor8");
    });
    
    $("#headercolor7").on("click", function() {
        $("html").addClass("color-header headercolor7").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor3 headercolor8");
    });
    
    $("#headercolor8").on("click", function() {
        $("html").addClass("color-header headercolor8").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor3");
    });

    // Sidebar Colors
    $('#sidebarcolor1').click(theme1);
    $('#sidebarcolor2').click(theme2);
    $('#sidebarcolor3').click(theme3);
    $('#sidebarcolor4').click(theme4);
    $('#sidebarcolor5').click(theme5);
    $('#sidebarcolor6').click(theme6);
    $('#sidebarcolor7').click(theme7);
    $('#sidebarcolor8').click(theme8);

    function theme1() {
        $('html').attr('class', 'color-sidebar sidebarcolor1');
    }

    function theme2() {
        $('html').attr('class', 'color-sidebar sidebarcolor2');
    }

    function theme3() {
        $('html').attr('class', 'color-sidebar sidebarcolor3');
    }

    function theme4() {
        $('html').attr('class', 'color-sidebar sidebarcolor4');
    }

    function theme5() {
        $('html').attr('class', 'color-sidebar sidebarcolor5');
    }

    function theme6() {
        $('html').attr('class', 'color-sidebar sidebarcolor6');
    }

    function theme7() {
        $('html').attr('class', 'color-sidebar sidebarcolor7');
    }

    function theme8() {
        $('html').attr('class', 'color-sidebar sidebarcolor8');
    }
});

// Fix jVectorMap NaN errors
$(document).ready(function() {
    // Check if jVectorMap elements exist before initializing
    if ($('.jvectormap-container').length > 0) {
        console.log('jVectorMap container exists, initializing maps');
    } else {
        // Prevent jVectorMap errors when no map elements exist
        // Override the problematic methods with empty functions if no map exists
        if (typeof jvm !== 'undefined' && typeof jvm.SVGCanvasElement !== 'undefined') {
            const originalApplyTransformParams = jvm.SVGCanvasElement.prototype.applyTransformParams;
            jvm.SVGCanvasElement.prototype.applyTransformParams = function(scale, transX, transY) {
                // Only apply if all parameters are valid numbers
                if (isNaN(scale) || isNaN(transX) || isNaN(transY)) {
                    console.warn('Invalid jVectorMap parameters:', { scale, transX, transY });
                    return;
                }
                originalApplyTransformParams.call(this, scale, transX, transY);
            };
        }
    }
});