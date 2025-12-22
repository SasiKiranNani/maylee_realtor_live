// Set CSRF token globally for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
});

jQuery(function ($) {
    "use strict";
    var clone_menu = $('.main-header .navbar .navbar-collapse .nav').clone('true', 'true');
    $('.offcanvas .offcanvas-body').append(clone_menu);

    $('.navbar-toggler').on('click', function (e) {
        e.preventDefault();
        $('#mobileOffCanvas').addClass('show');
    })

    $('.offcanvas-header .btn-close').on('click', function () {
        $('#mobileOffCanvas').removeClass('show');
    })
    $('.offcanvas-body ul.main-menu li.nav-item.dropdown > a').on('click', function (e) {
        e.preventDefault();
        $(this).next('ul.dropdown-menu').toggleClass('show');
    })
    // user dropdown menu
    $('.rt-user-nav-widget .toggle-btn').on('click', function () {
        $(this).next('.user-dropdown').toggleClass('show');
    })
    //Get Current Year
    var currentYear = new Date().getFullYear();
    $("#current-year").text(currentYear);

    //Sticky menu
    var menu = $("#main-header");
    var offset = menu.offset().top; // Get the initial position of the menu

    $(window).scroll(function () {
        if ($(window).scrollTop() > offset) {
            menu.addClass("sticky-fixed");
        } else {
            menu.removeClass("sticky-fixed");
        }
    });
    //Aos animation active

    AOS.init({
        offset: 100,
        duration: 1200,
        easing: "ease-in-out",
        anchorPlacement: "top-bottom",
        disable: "mobile",
    });

    // carousel initalization
    const property_swiper = new Swiper(".rt-property-carousel", {
        spaceBetween: 20,
        speed: 1000,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        breakpoints: {
            1280: {
                slidesPerView: 3
            },
            991: {
                slidesPerView: 3
            },
            768: {
                slidesPerView: 2
            },
            320: {
                slidesPerView: 1
            }
        }
    });
    /** Location Carousel**/
    const location_swiper = new Swiper(".rt-location-carousel", {
        spaceBetween: 20,
        speed: 1000,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        breakpoints: {
            1280: {
                slidesPerView: 4
            },
            991: {
                slidesPerView: 3
            },
            768: {
                slidesPerView: 2
            },
            320: {
                slidesPerView: 1
            }
        }
    });
    /** Hero Slider **/
    const hero_slider = new Swiper(".rt-hero-slider", {
        slidesPerView: 1,
        parallax: true,
        speed: 3000,
        autoplay: true,
        loop: true,
        /*effect: "creative",*/
        /*effect: "cube",*/
        /*effect: "slide",*/
        /*effect: "coverflow",*/
        effect: "fade",
        creativeEffect: {
            prev: {
                shadow: true,
                origin: "left center",
                translate: ["-5%", 0, -200],
                rotate: [0, 100, 0],
            },
            next: {
                origin: "right center",
                translate: ["5%", 0, -200],
                rotate: [0, -100, 0],
            },
        },
    })
    /** Category Carousel **/
    const category_swiper = new Swiper(".rt-category-carousel", {
        spaceBetween: 20,
        speed: 1000,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        breakpoints: {
            1280: {
                slidesPerView: 5
            },
            991: {
                slidesPerView: 4
            },
            768: {
                slidesPerView: 3
            },
            320: {
                slidesPerView: 1
            }
        }
    });
    /** Testimonial Carousel **/
    const testimonial_swiper = new Swiper(".rt-testimonial-carousel", {
        spaceBetween: 20,
        speed: 1000,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        breakpoints: {
            1280: {
                slidesPerView: 3
            },
            991: {
                slidesPerView: 2
            },
            768: {
                slidesPerView: 1
            },
            320: {
                slidesPerView: 1
            }
        }
    });

    /** Single Property Gallery Slider With Thumbnails **/
    const single_property_gallery_thumbs_slider = new Swiper(".rt-single-property-gallery-thumbs-slider", {
        slidesPerView: 4,
        freeMode: true,
        watchSlidesVisibility: true,
        watchSlidesProgress: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        }
    })
    const single_property_gallery_slider = new Swiper(".rt-single-property-gallery-slider", {
        slidesPerView: 1,
        autoplay: true,
        loop: true,
        effect: "slide",
        parralex: true,
        speed: 3000,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        },
        thumbs: {
            swiper: single_property_gallery_thumbs_slider
        }
    })

    /** Sign In Sign Up Popup **/
    $('#open-signin-signup-popup').on('click', function (e) {
        e.preventDefault();
        $('#signin-signup-popup').toggleClass('active');
    })
    $('#signin-signup-popup #rt-popup-close').on('click', function () {
        $('#signin-signup-popup').removeClass('active');
    })
    $('#signin-signup-popup #signup-form-link').on('click', function (e) {
        e.preventDefault();
        $('#signin-signup-popup .form-block').removeClass('active');
        $('#signin-signup-popup #signup-form-block').addClass('active');
    })
    $('#signin-signup-popup #signin-form-link').on('click', function (e) {
        e.preventDefault();
        $('#signin-signup-popup .form-block').removeClass('active');
        $('#signin-signup-popup #signin-form-block').addClass('active');
    })
    $('#signin-signup-popup #resetpwd-form-link').on('click', function (e) {
        e.preventDefault();
        $('#signin-signup-popup .form-block').removeClass('active');
        $('#signin-signup-popup #resetpwd-form-block').addClass('active');
    })

    $('#open-assessment-popup').on('click', function (e) {
        e.preventDefault();
        $('.rt-inperson-assessment-popup').toggleClass('active');
    })

    $('#rt-inperson-assessment-popup #rt-popup-close').on('click', function () {
        $('#rt-inperson-assessment-popup').removeClass('active');
    })



    //date filed restriction
    var today = new Date();

    today.setDate(today.getDate() + 1);
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();

    var minDate = yyyy + '-' + mm + '-' + dd;
    $('#reqt-date').attr('min', minDate);

    $('#reqt-date').on('change', function () {
        var selected = new Date($(this).val());
        var min = new Date($(this).attr('min'));
        if (selected < min) {
            $(this).val(''); // clear invalid input
            alert("Please select a future date.");
        }
    })

       /** Accordions **/
    $('.rt-accordion-header').click(function () {
        $('.rt-accordion-body').slideUp();
        $('.rt-accordion-header').removeClass('active');
        $('.rt-accordion-header').find('span.icon i').removeClass('fa-minus');
        $('.rt-accordion-header').find('span.icon i').addClass('fa-plus');

        if (!$(this).next('.rt-accordion-body').is(':visible')) {
            $(this).next('.rt-accordion-body').slideDown();
            $(this).addClass('active');
            $(this).find('span.icon i').removeClass('fa-plus');
            $(this).find('span.icon i').addClass('fa-minus');
        }
    })

  //nice select
  $('.rt-property-filters select, .rt-select').niceSelect();


  // Initialize currentUserId variable for wishlist functionality
  window.currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content') || null;
  if (window.currentUserId === '') window.currentUserId = null;
  
// Wishlist functionality
  $(document).on('click', '.property-wishlist', function(e) {
      e.preventDefault();
      e.stopPropagation();

      if (!window.currentUserId) {
          const loginButton = document.getElementById('open-signin-signup-popup');
          if (loginButton) loginButton.click();
          return;
      }

      const $this = $(this);
      const listingKey = $this.data('listing-key');
      const transactionType = $this.data('transaction-type');
      const $icon = $this.find('i');

      // Debug logging
      console.log('Wishlist toggle:', { listingKey, transactionType });

      // Validate data before sending
      if (!listingKey || !transactionType) {
          console.error('Missing listing_key or transaction_type');
          if (window.ToasterMagic) {
              window.ToasterMagic.error('Invalid property data');
          }
          return;
      }

      // Check if we're on the wishlist page (trash icon)
      const isWishlistPage = $icon.hasClass('fa-trash');
      const $propertyCard = $this.closest('.rt-property-item');

      $.ajax({
          url: '/wishlist/toggle',
          method: 'POST',
          data: {
              listing_key: listingKey,
              transaction_type: transactionType,
              _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          success: function(response) {
              if (response.success) {
                  if (isWishlistPage) {
                      // On wishlist page, remove the property card with animation
                      $propertyCard.fadeOut(300, function() {
                          $(this).remove();
                          
                          // Check if there are any properties left
                          const remainingProperties = $('#wishlist-container .rt-property-item').length;
                          if (remainingProperties === 0) {
                              // Show "no wishlist" message
                              $('#wishlist-container').html(`
                                  <div class="no-properties text-center" id="no-wishlist-message">
                                      <p>No wishlist found. Please try again later.</p>
                                  </div>
                              `);
                          }
                      });
                  } else {
                      // On other pages, toggle the heart icon
                      $icon.removeClass('fa-regular fa-solid').addClass(response.added ? 'fa-solid' : 'fa-regular');
                      $this.addClass('animate-heart');
                      setTimeout(() => $this.removeClass('animate-heart'), 600);
                  }

                  if (window.ToasterMagic) {
                      window.ToasterMagic.success(response.message);
                  }
              }
          },
          error: function(xhr) {
              console.error('Wishlist error:', xhr.responseJSON);
              let errorMessage = 'Failed to update wishlist';
              
              if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                  const errors = Object.values(xhr.responseJSON.errors).flat();
                  errorMessage = errors.join(', ');
              }
              
              if (window.ToasterMagic) {
                  window.ToasterMagic.error(errorMessage);
              }
          }
      });
  });
})
// amenities filter in details page
document.addEventListener('DOMContentLoaded', function () {
    // Check if we're on a page with amenities map
    const amenitiesMap = document.getElementById('amenitiesMap');
    if (!amenitiesMap) return;

    // Get coordinates from data attributes or use defaults
    const mapElement = document.getElementById('amenitiesMap');
    const lat = parseFloat(mapElement.dataset.lat) || 43.6532;
    const lng = parseFloat(mapElement.dataset.lng) || -79.3832;
    const city = mapElement.dataset.city || 'Toronto';

    // Validate coordinates
    const isValidCoords = !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0;
    const finalLat = isValidCoords ? lat : 43.6532;
    const finalLng = isValidCoords ? lng : -79.3832;

    console.log('Map coordinates:', {
        original: { lat, lng },
        final: { finalLat, finalLng },
        isValid: isValidCoords,
        city: city
    });

    // Initialize map
    const map = L.map('amenitiesMap').setView([finalLat, finalLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Main property marker
    const propertyIcon = L.divIcon({
        className: 'property-marker',
        html: '<i class="fa-solid fa-location-dot" style="color:#e74c3c;font-size:24px;"></i>',
        iconSize: [24, 24],
        iconAnchor: [12, 24]
    });

    const propertyMarker = L.marker([finalLat, finalLng], {
        icon: propertyIcon
    }).addTo(map);

    // Create popup content
    const popupContent = `
        <div style="text-align: center;">
            <strong>Property Location</strong><br>
            ${mapElement.dataset.address || 'Address not available'}<br>
            <small style="color: #666;">
                ${!isValidCoords ? `<em>Showing approximate location based on ${city}</em>` : ''}
            </small>
        </div>
    `;

    propertyMarker.bindPopup(popupContent).openPopup();

    // Amenity functionality
    const amenityLayers = {};
    const amenityTags = {
        school: ['school'],
        bus_station: ['bus_station', 'bus_stop'],
        restaurant: ['restaurant', 'fast_food'],
        pharmacy: ['pharmacy'],
        gym: ['gym', 'fitness_centre'],
        supermarket: ['supermarket', 'grocery'],
        hospital: ['hospital', 'clinic'],
        park: ['park', 'playground'],
        cafe: ['cafe'],
        convenience_store: ['convenience'],
        gas_station: ['fuel'],
        laundry: ['laundry'],
        bar: ['bar', 'pub'],
        bank: ['bank']
    };

    const amenityIcons = {
        school: 'fa-school',
        bus_station: 'fa-bus',
        restaurant: 'fa-utensils',
        pharmacy: 'fa-pills',
        gym: 'fa-dumbbell',
        supermarket: 'fa-cart-shopping',
        hospital: 'fa-hospital',
        park: 'fa-tree',
        cafe: 'fa-mug-hot',
        convenience_store: 'fa-store',
        gas_station: 'fa-gas-pump',
        laundry: 'fa-soap',
        bar: 'fa-beer-mug-empty',
        bank: 'fa-building-columns'
    };

    // Store original label text for each amenity type
    const originalLabelTexts = new Map();
    document.querySelectorAll('.amenity-toggle-input').forEach(cb => {
        const label = cb.closest('.places-type');
        const textSpan = label.querySelector('span');
        originalLabelTexts.set(cb.value, textSpan.innerHTML);
    });

    async function fetchAmenities(type) {
        const radius = 1500;
        const tags = amenityTags[type] || [type];
        const queryParts = tags.map(tag => `
            node["amenity"="${tag}"](around:${radius},${finalLat},${finalLng});
            way["amenity"="${tag}"](around:${radius},${finalLat},${finalLng});
            relation["amenity"="${tag}"](around:${radius},${finalLat},${finalLng});
        `).join('');

        const query = `[out:json][timeout:25];(${queryParts});out center;`;

        try {
            const res = await fetch("https://overpass-api.de/api/interpreter", {
                method: 'POST',
                body: query
            });
            const data = await res.json();
            return data.elements || [];
        } catch (error) {
            console.error('Error fetching amenities for', type, error);
            return [];
        }
    }

    async function toggleAmenity(type, show) {
        const checkbox = document.querySelector(`.amenity-toggle-input[value="${type}"]`);
        if (!checkbox) return;

        const label = checkbox.closest('.places-type');
        const textSpan = label.querySelector('span');

        if (show) {
            // Show loading state
            const originalHTML = textSpan.innerHTML;
            textSpan.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading...';
            checkbox.disabled = true;

            const elements = await fetchAmenities(type);

            // Restore original text content
            textSpan.innerHTML = originalLabelTexts.get(type);
            checkbox.disabled = false;

            // Only add markers if checkbox is still checked after loading
            if (checkbox.checked) {
                const layerGroup = L.layerGroup();
                const iconClass = amenityIcons[type] || 'fa-map-marker-alt';

                elements.forEach(el => {
                    const coords = el.type === "node" ?
                        [el.lat, el.lon] :
                        [el.center?.lat, el.center?.lon];

                    if (!coords[0] || !coords[1]) return;

                    const icon = L.divIcon({
                        className: 'amenity-icon',
                        html: `<i class="fa-solid ${iconClass}" style="color:#007bff;font-size:18px;"></i>`,
                        iconSize: [18, 18],
                        iconAnchor: [9, 9]
                    });

                    const name = el.tags?.name || type;
                    L.marker(coords, {
                        icon
                    }).bindPopup(`<strong>${name}</strong><br>${type}`).addTo(layerGroup);
                });

                layerGroup.addTo(map);
                amenityLayers[type] = layerGroup;
            }
        } else {
            // Remove the layer from map
            if (amenityLayers[type]) {
                map.removeLayer(amenityLayers[type]);
                delete amenityLayers[type];
            }
        }
    }

    // Add event listeners to amenity checkboxes
    document.querySelectorAll('.amenity-toggle-input').forEach(cb => {
        cb.addEventListener('change', function () {
            toggleAmenity(this.value, this.checked);
        });
    });
});


/** Mortgage calculator **/
document.addEventListener('DOMContentLoaded', function () {
    // Get property price from data attribute
    const mortgageAmountSlider = document.getElementById('rt-amount-slider');
    // Use parseFloat to handle precise amounts
    const propertyPrice = mortgageAmountSlider
        ? parseFloat(mortgageAmountSlider.getAttribute('data-property-price')) || 100000
        : 100000;

    // ---- Helper: Canadian minimum down payment rules (Ratehub logic) ----
    function getMinDownPayment(price) {
        if (price >= 1500000) {
            // Updated Dec 2024: 20% for 1.5M+ homes
            return price * 0.20;
        }

        if (price <= 500000) {
            // 5% of entire price
            return price * 0.05;
        }

        // 500k – 1.5M:
        // 5% of first 500k + 10% of remaining
        const firstPart = 500000 * 0.05;
        const remaining = price - 500000;
        const secondPart = remaining * 0.10;
        return firstPart + secondPart;
    }

    // ---- Helper: CMHC Insurance Premium Rates ----
    // Updated Dec 2024: Higher premiums for amortizations over 25 years
    function getCMHCPremiumRate(downPaymentPercent, amortizationMonths) {
        if (downPaymentPercent >= 20) return 0;
        
        // For amortizations over 25 years (300 months) with less than 10% down:
        // CMHC applies a higher premium rate
        if (amortizationMonths > 300 && downPaymentPercent < 10) {
            return 0.0420; // 4.20% for 30-year with <10% down
        }
        
        // Standard rates for 25 years or less
        if (downPaymentPercent >= 15) return 0.0280;
        if (downPaymentPercent >= 10) return 0.0310;
        if (downPaymentPercent >= 5) return 0.0400;
        return 0;
    }

    // ---- Property Price Slider ----
    if (mortgageAmountSlider) {
        // Set dynamic range based on property price
        const minPrice = Math.max(50000, Math.floor(propertyPrice * 0.5));
        const maxPrice = Math.max(1000000, Math.ceil(propertyPrice * 1.5));

        noUiSlider.create(mortgageAmountSlider, {
            start: propertyPrice,
            connect: true,
            step: 1, // allow exact amounts
            range: {
                min: minPrice,
                max: maxPrice
            },
            behaviour: 'tap-drag'
        });
    }

    // ---- Interest Rate Slider ----
    var mortgageInterestRateSlider = document.getElementById('rt-interest-slider');
    if (mortgageInterestRateSlider) {
        // Set default interest rate based on property price
        const defaultInterestRate = propertyPrice < 1500000 ? 3.94 : 4.19;
        
        noUiSlider.create(mortgageInterestRateSlider, {
            start: defaultInterestRate,
            connect: true,
            step: 0.01,
            range: {
                min: 1.0,
                max: 15.0
            },
            format: {
                to: function (value) {
                    return value.toFixed(2);
                },
                from: function (value) {
                    return parseFloat(value);
                }
            },
            behaviour: 'tap-drag'
        });
    }

    // ---- Down Payment Slider (5% to 50% of price) ----
    var mortgageDownPaymentSlider = document.getElementById('rt-down-payment-slider');
    if (mortgageDownPaymentSlider) {
        const minDP = Math.floor(propertyPrice * 0.05);
        const maxDP = Math.floor(propertyPrice * 0.50);
        // Default to Ratehub minimum calculation
        const defaultDP = Math.floor(getMinDownPayment(propertyPrice));

        noUiSlider.create(mortgageDownPaymentSlider, {
            start: defaultDP,
            connect: true,
            step: 1,
            range: {
                min: minDP,
                max: maxDP
            },
            behaviour: 'tap-drag'
        });
    }

    // ---- Tenure Slider (in months) ----
    var mortgageTenureSlider = document.getElementById('rt-tenuer-slider');
    if (mortgageTenureSlider) {
        noUiSlider.create(mortgageTenureSlider, {
            start: 300, // 25 years
            connect: true,
            step: 1,
            range: {
                min: 12,
                max: 360
            },
            tooltips: false
        });
    }

    // ---- Loan Chart ----
    const canvas = document.getElementById('mortgage-chart');
    if (canvas) {
        let ctx = canvas.getContext('2d');
        let loanChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Principal', 'CMHC Insurance', 'PST on CMHC', 'Monthly Payment'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: ['#4c418c', '#F7DC6F', '#85C1E2', '#E6A4B4'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                }
            }
        });

        // ---- Update all values ----
        function updateValues() {
            let loan_amount = parseInt(mortgageAmountSlider.noUiSlider.get());
            let loan_interest = parseFloat(mortgageInterestRateSlider.noUiSlider.get());
            let down_payment = parseInt(mortgageDownPaymentSlider.noUiSlider.get());
            let total_tenure_months = parseInt(mortgageTenureSlider.noUiSlider.get());
            let frequencyVal = $('#mortgage-payment-frequency').val() || "12";

            // Enforce Canadian minimum down payment for current price
            const minAllowedDown = Math.floor(getMinDownPayment(loan_amount));
            if (down_payment < minAllowedDown) {
                down_payment = minAllowedDown;
                mortgageDownPaymentSlider.noUiSlider.set(down_payment);
            }

            // Ensure down payment doesn't exceed price
            if (down_payment > loan_amount) {
                down_payment = loan_amount;
                mortgageDownPaymentSlider.noUiSlider.set(down_payment);
            }

            // Update % next to Down Payment Label
            let downPaymentPercent = (down_payment / loan_amount) * 100;
            $('#down-percent').text('(' + downPaymentPercent.toFixed(1) + '%)');

            // Loan Calculation
            let netPrincipal = loan_amount - down_payment;
            
            // CMHC Insurance Calculation (now considers amortization length)
            let premiumRate = getCMHCPremiumRate(downPaymentPercent, total_tenure_months);
            let cmhcPremium = Math.round(netPrincipal * premiumRate);
            
            // PST on CMHC (8% in Ontario)
            let pstOnCMHC = Math.round(cmhcPremium * 0.08);
            
            let principal = netPrincipal + cmhcPremium;

            let annualRate = loan_interest / 100;

            // Canadian mortgages → nominal annual, compounded semi-annually, convert to effective monthly.[web:100][web:102][web:105][web:114]
            let semiAnnualRate = annualRate / 2;
            let monthlyRate = 0;
            if (annualRate > 0) {
                monthlyRate = Math.pow(1 + semiAnnualRate, 1 / 6) - 1;
            }

            let numberOfMonths = total_tenure_months;

            // Standard amortization formula with effective monthly rate
            let monthlyPayment = 0;
            if (monthlyRate > 0) {
                monthlyPayment = principal * monthlyRate * Math.pow(1 + monthlyRate, numberOfMonths) /
                    (Math.pow(1 + monthlyRate, numberOfMonths) - 1);
            } else {
                monthlyPayment = principal / numberOfMonths;
            }

            let payment = 0;
            let paymentsPerYear = 12;
            let label = "Monthly Payment";

            // Frequency logic (derived from monthly)
            if (frequencyVal === "12") {
                payment = monthlyPayment;
                label = "Monthly Payment";
                paymentsPerYear = 12;
            } else if (frequencyVal === "24") {
                // Semi-monthly
                payment = monthlyPayment / 2;
                label = "Semi-Monthly Payment";
                paymentsPerYear = 24;
            } else if (frequencyVal === "26") {
                // Bi-Weekly
                payment = (monthlyPayment * 12) / 26;
                label = "Bi-Weekly Payment";
                paymentsPerYear = 26;
            } else if (frequencyVal === "52") {
                // Weekly
                payment = (monthlyPayment * 12) / 52;
                label = "Weekly Payment";
                paymentsPerYear = 52;
            }

            let total = monthlyPayment * numberOfMonths;
            let interest = total - principal;

            // Format numbers with commas
            $('#loan-value').text('$' + loan_amount.toLocaleString());
            if (!$('#interest-input').is(':focus')) {
                $('#interest-input').val(loan_interest.toFixed(2));
            }
            $('#down-value').text('$' + down_payment.toLocaleString());

            // Tenure display
            let tYears = Math.floor(total_tenure_months / 12);
            let tMonths = total_tenure_months % 12;
            let tenureText = "";
            if (tYears > 0 && tMonths > 0) {
                tenureText = tYears + " Yrs " + tMonths + " Months";
            } else if (tYears > 0) {
                tenureText = tYears + " Yrs";
            } else {
                tenureText = tMonths + " Months";
            }
            $('#tenure-value').text(tenureText);

            // Update chart with Monthly Payment, CMHC, PST, and Principal
            loanChart.data.datasets[0].data = [principal, cmhcPremium, pstOnCMHC, payment];
            loanChart.update();

            // Update Result with formatted numbers
            $('#rmonthly-emi').text('$' + payment.toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }));
            $('#rmonthly-emi').parent().find('.title').text(label);

            $('#rcmhc-value').text('$' + cmhcPremium.toLocaleString());
            $('#rpst-value').text('$' + pstOnCMHC.toLocaleString());
            $('#rprincipal-value').text('$' + principal.toLocaleString());
            $('#rinterest-value').text('$' + interest.toLocaleString());
            $('#rtotal-value').text('$' + total.toLocaleString());
        }

        // Attach events
        mortgageAmountSlider.noUiSlider.on('update', function (values) {
            let price = parseInt(values[0]);
            $('#loan-value').text('$' + price.toLocaleString());
            
            // Dynamically update Down Payment slider range (5% - 50%)
            const minDP = Math.floor(price * 0.05);
            const maxDP = Math.floor(price * 0.50);
            
            // Auto-apply the "actual value" (Ratehub minimum) whenever price changes
            const ratehubMinDP = Math.floor(getMinDownPayment(price));

            mortgageDownPaymentSlider.noUiSlider.updateOptions({
                range: {
                    min: minDP,
                    max: maxDP
                }
            });
            mortgageDownPaymentSlider.noUiSlider.set(ratehubMinDP);

            updateValues();
        });

        mortgageInterestRateSlider.noUiSlider.on('update', function (values) {
            if (!$('#interest-input').is(':focus')) {
                $('#interest-input').val(values[0]);
            }
            updateValues();
        });

        $('#interest-input').on('input', function () {
            let val = parseFloat($(this).val());
            if (!isNaN(val)) {
                let sliderVal = val;
                if (sliderVal < 1.0) sliderVal = 1.0;
                if (sliderVal > 15.0) sliderVal = 15.0;
                mortgageInterestRateSlider.noUiSlider.set(sliderVal);
            }
            updateValues();
        });

        $('#interest-input').on('blur', function () {
            let val = parseFloat($(this).val());
            if (isNaN(val)) val = 1.0;
            if (val < 1.0) val = 1.0;
            if (val > 15.0) val = 15.0;
            $(this).val(val.toFixed(2));
            mortgageInterestRateSlider.noUiSlider.set(val);
            updateValues();
        });


        mortgageDownPaymentSlider.noUiSlider.on('update', function (values) {
            $('#down-value').text('$' + parseInt(values[0]).toLocaleString());
            updateValues();
        });

        mortgageTenureSlider.noUiSlider.on('update', function () {
            updateValues();
        });

        $('#mortgage-payment-frequency').on('change', function () {
            updateValues();
        });

        // Initial Run
        updateValues();
    }

    /** Land Transfer Tax Calculator **/
    const landTransferChartCanvas = document.getElementById('rt-land-transfer-tax-chart');
    if (landTransferChartCanvas) {
        const lctx = landTransferChartCanvas.getContext('2d');
        new Chart(lctx, {
            type: 'doughnut',
            data: {
                labels: ['Provincial', 'Municipal', 'Rebate'],
                datasets: [{
                    data: [12, 19, 10],
                    backgroundColor: ['#1473C9', '#24A9E0', '#2DA573'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Letter animation on scroll (unchanged)
    $('.animate-letters').each(function () {
        const $this = $(this);
        const text = $this.text();
        $this.empty();
        text.split('').forEach((letter) => {
            const span = $('<span>').text(letter).css({
                display: 'inline-block',
                opacity: 0,
                transform: 'translateY(20px)'
            });
            $this.append(span);
        });

        gsap.registerPlugin(ScrollTrigger);

        gsap.to($this.find('span'), {
            opacity: 1,
            y: 0,
            duration: 0.5,
            stagger: 0.05,
            ease: "power2.out",
            scrollTrigger: {
                trigger: $this[0],
                start: "top 80%",
                end: "bottom 20%",
                toggleActions: "play none none reverse"
            }
        });
    });
});

// Explore Property button login check
$(document).on('click', '.btn-property-explore', function(e) {
    if (!window.currentUserId) {
        e.preventDefault();
        const loginButton = document.getElementById('open-signin-signup-popup');
        if (loginButton) loginButton.click();
        return false;
    }
    // If logged in, allow default behavior
});