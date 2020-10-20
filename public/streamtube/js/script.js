$(document).ready(function(){

      $("#search-btn").click(function(){
          $("#header-section").slideUp();
          $("#search-section").slideDown();
      });

      $("#close-btn").click(function(){
          $("#search-section").slideUp();
          $("#header-section").slideDown();
      });
      
      $('.toggle-icon').click(function () {
          $('.y-menu').toggleClass('hidden');
          $('.y-menu').toggleClass('overlay1');
      });

      $( ".toggle-icon" ).click(function() {
          $( ".page-inner").toggleClass('col-sm-12');
          $( ".page-inner").toggleClass('col-sm-9');
          $( ".page-inner").toggleClass('col-md-10');
          $( ".page-inner").toggleClass('col-md-12');

          // if ($(window).width() <= 767){  
          //     $("body").toggleClass("sidebar-open");
          //     $(".sidebar-back").toggleClass("sidebar-backdrop");
          //     console.log("open");
          // }
      });

      $("#video-side").click(function(){
          $("#video-dropdown").fadeIn();
      }); 

      $("#menu-close").click(function(){
          $("#video-dropdown").fadeOut();
      }); 

      var height = $('.footer1').outerHeight();
      console.log(height);
      $(".bottom-height").height(height);

});

$(window).resize(function(){
    if ($(window).width() <= 767){  
        $(".y-menu").addClass('hidden');
    } 
    else{  
        if($('.page-inner').hasClass('.col-md-10')){
           $(".y-menu").removeClass("hidden"); 
        }
        else if($('.page-inner').hasClass('.col-md-12')){
           $(".y-menu").addClass("hidden"); 
        }    
    }    
});

$(document).ready(function(){
    if ($(window).width() <= 767){  
        $(".y-menu").addClass('hidden');
        // $(".page-inner").addClass("sidebar-open");
        // $(".sidebar-back").addClass("sidebar-backdrop");
    }
    else{  
        $(".y-menu").removeClass("hidden");
        // $(".page-inner").removeClass("sidebar-open");
        // $(".sidebar-back").removeClass("sidebar-backdrop");
    }   
});

$(window).load(function(){

    $(".y-menu").css({'height':($(".page-inner").outerHeight( true )+'px')});

});

$(window).resize(function(){

    $(".y-menu").css({'height':($(".page-inner").outerHeight( true )+'px')});

});


