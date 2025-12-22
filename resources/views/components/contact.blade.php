<div class="row align-items-center">
    <div class="col-lg-6 col-md-6 col-sm-12" data-aos="fade-right">
        <div class="rt-contact-info-box pb-5 pb-md-0">
            <h2 class="title mb-5">Your Dream Home Awaits — Let’s Connect!</h2>
            <div class="rt-icon-box-list rt-contact-list">
                <div class="rt-icon-box">
                    <div class="icon">
                        <i class="fa-solid fa-phone-volume"></i>
                    </div>
                    <div class="content">
                        <h3>Phone</h3>
                        <a href="tel:+16478850114">May Lee (Realtor) : (647) 885 0114</a>
                    </div>
                </div>
                <div class="rt-icon-box">
                    <div class="icon">
                        <i class="fa-solid fa-phone-volume"></i>
                    </div>
                    <div class="content">
                        <h3>Phone</h3>
                        <a href="tel:+16478857848">Paul Wang (Assistant) : (647) 885 7848</a>
                    </div>
                </div>
                <div class="rt-icon-box">
                    <div class="icon">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div class="content">
                        <h3>Office</h3>
                        <a href="tel:+19056957888">(905) 695 7888</a>
                    </div>
                </div>
                <div class="rt-icon-box">
                    <div class="icon">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <div class="content">
                        <h3>Email</h3>
                        <a href="mailto:may.lee@mayleerealtor.com"> may.lee@mayleerealtor.com</a>
                    </div>
                </div>
                <div class="rt-icon-box">
                    <div class="icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="content">
                        <h3>Address</h3>
                        <p>1550 16th Ave, Suite: 3 & 4, Richmond Hill, Ontario, L4B 3K9, Canada</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12" data-aos="fade-left">
        <div class="rt-contact-form-box">
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <input type="hidden" name="source" value="{{ $source }}">
                @if($city)
                    <input type="hidden" name="city" value="{{ $city }}">
                @endif
                @if($listingKey)
                    <input type="hidden" name="listing_key" value="{{ $listingKey }}">
                @endif

                <div class="rt-input-wrap">
                    <input type="text" name="name" id="name" placeholder="" required>
                    <div class="error-msg"></div>
                </div>
                <div class="rt-input-wrap">
                    <input type="email" name="email" id="email" placeholder="" required>
                    <div class="error-msg"></div>
                </div>
                <div class="rt-input-wrap">
                    <input type="text" name="phone" id="phone" placeholder="">
                    <div class="error-msg"></div>
                </div>
                <div class="rt-input-wrap">
                    <textarea name="message" id="message" placeholder=""></textarea>
                    <div class="error-msg"></div>
                </div>
                <div class="rt-btn-wrap">
                    <button type="submit" class="rt-btn btn-pink btn-outline">
                        Send
                        <span class="icon"><i class="fa-solid fa-arrow-right"></i></span>
                    </button>
                </div>
                @if(session('success'))
                    <div class="alert alert-success mt-3" style="color: green; margin-top: 10px;">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="rt-response-msg"></div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const placeholders = [
        { id: 'name', text: 'Name *' },
        { id: 'email', text: 'Email *' },
        { id: 'phone', text: 'Phone *' },
        { id: 'message', text: 'Message' }
    ];

    placeholders.forEach(function(item) {
        const element = document.getElementById(item.id);
        let index = 0;
        const interval = setInterval(function() {
            if (index < item.text.length) {
                element.placeholder += item.text[index];
                index++;
            } else {
                clearInterval(interval);
            }
        }, 100); // Speed: 100ms per letter
    });
});
</script>