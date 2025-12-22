 <div class="swiper rt-testimonial-carousel rt-carousel top-right-nav">
     <div class="swiper-wrapper p-2">
         @forelse ($testimonials as $testimonial)
             <div class="swiper-slide">
                 <div class="rt-testimonial-item">
                     <div class="testimonial-content">
                         {!! $testimonial->description !!}
                     </div>
                      <div class="testimonial-rating" style="color: #ffc107; font-size: 1.1rem; margin: 15px 0;">
                        @php
                            $rating = $testimonial->rating ?? 5.0;
                            $fullStars = floor($rating);
                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                        @endphp

                        @for ($i = 0; $i < $fullStars; $i++)
                            <i class="fa-solid fa-star"></i>
                        @endfor

                        @if ($hasHalfStar)
                            <i class="fa-regular fa-star-half-stroke"></i>
                        @endif

                        @for ($i = 0; $i < $emptyStars; $i++)
                            <i class="fa-regular fa-star"></i>
                        @endfor
                    </div>
                     <div class="testimonial-user-details">
                         {{-- <div class="image">
                             <img src="{{ asset('frontend/assets/images/user-male.webp') }}">
                         </div> --}}
                         <div class="content">
                             <h3 class="name">{{ $testimonial->name }}</h3>
                             <h6 class="designation">{{ $testimonial->designation }}</h6>
                         </div>
                     </div>
                 </div>
             </div>
         @empty
             <p class="text-center text-muted">No testimonials available at the moment.</p>
         @endforelse
     </div>
     <!-- Navigation & Pagination -->
     <!--<div class="swiper-pagination"></div>-->
     <div class="rt-carousel-nav swiper-button-prev">
         <i class="fa-solid fa-arrow-left"></i>
     </div>
     <div class="rt-carousel-nav swiper-button-next">
         <i class="fa-solid fa-arrow-right"></i>
     </div>
 </div>
