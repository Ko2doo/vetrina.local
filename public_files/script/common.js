$(function(){
//Скрипты:

	// Включаем Aos.js
	AOS.init();

	AOS.init({
	  // Настройки, которые могут быть переопределены для каждого элемента с помощью атрибутов data-aos- *:
	  delay: 0, // значения от 0 до 3000 с шагом 50 мс
	  duration: 400, // значения от 0 до 3000 с шагом 50 мс
	  easing: 'ease', // ослабление по умолчанию для анимации AOS
	  once: true, // должна ли анимация происходить только один раз - при прокрутке вниз
	  mirror: true, // должны ли элементы анимироваться при прокрутке мимо них
	  anchorPlacement: 'top-bottom', // определяет, какая позиция элемента относительно окна должна вызывать анимацию

	});


	// Универсальный dropdown-menu:
	/*
	*Use To html -->>
	    <div class="dropdown-menu">
	      <button class="btn-open">
	        <i class="fa fa-bars" aria-hidden="true"></i>
	      </button>
	      <button class="btn-close">
	        <i class="fa fa-times" aria-hidden="true"></i>
	      </button>

	      <ul class="your classname">
					your content
	      </ul>
	    </div>
	*/
	var $btn_open 			= $('.btn-open');
	var $btn_close 			= $('.btn-close');
	var $dropdowm_menu 	= $('.dropdown-menu > .menu-content');
	
	$btn_open.click(function(event){
		 event.stopPropagation();
		 event.preventDefault();

		 $(this).parents('.dropdown-menu').find('.menu-content').toggleClass('is-open');		 
	});

	$btn_close.click(function(event){
			event.stopPropagation();
			event.preventDefault();
			$dropdowm_menu.removeClass('is-open').addClass('close');

			setTimeout(function(event){
				$dropdowm_menu.removeClass('close');
			}, 1000);
	});

	// Magnific-popup
	$('.popup-modal').magnificPopup({
		type: 'inline',

		fixedContentPos: false,
		fixedBgPos: true,

		overflowY: 'auto',

		closeBtnInside: true,
		preloader: false,
		
		midClick: true,
		mainClass: 'my-mfp-slide-bottom'
	});

	$(document).on('click', '.popup-modal-dismiss', function (e) {
		e.preventDefault();
		$.magnificPopup.close();
	});

	// Magnific-popup image
	$('.image-popup-vertical-fit').magnificPopup({
		type: 'image',
		closeOnContentClick: true,
		mainClass: 'mfp-img-mobile',
		image: {
			verticalFit: true
		}
	});

	$('.image-popup-fit-width').magnificPopup({
		type: 'image',
		closeOnContentClick: true,
		image: {
			verticalFit: false
		}
	});

	$('.image-popup-no-margins').magnificPopup({
		type: 'image',
		closeOnContentClick: true,
		closeBtnInside: false,
		fixedContentPos: true,
		mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
		image: {
			verticalFit: true
		},
		zoom: {
			enabled: true,
			duration: 300 // don't foget to change the duration also in CSS
		}
	});

	//табы:
	jQuery.fn.lightTabs = function(options){

		var createTabs = function(){
			tabs = this;
				 i = 0;

			showPage = function(i){
			  $(tabs).children("div").children(".tabs-item").hide().removeClass('animate');
			  $(tabs).children("div").children(".tabs-item").eq(i).show().addClass('animate');
			  $(tabs).children("aside").children(".tab-nav").children(".tap-me").removeClass("active");
			  $(tabs).children("aside").children(".tab-nav").children(".tap-me").eq(i).addClass("active");
			}

			showPage(0);

			$(tabs).children("aside").children(".tab-nav").children(".tap-me").each(function(index, element){
			  $(element).attr("data-tab", i);
			  	i++;       
			});

			$(tabs).children("aside").children(".tab-nav").children(".tap-me").click(function(){
			  showPage(parseInt($(this).attr("data-tab")));
			});
		};
		return this.each(createTabs);
	};
	
	//инициализация:
	$(document).ready(function(){
  	$(".tabs").lightTabs();
	});


});
