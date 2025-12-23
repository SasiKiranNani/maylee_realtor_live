@extends('layouts.frontend.index')
@section('contents')
    <div class="page-wrapper d-block w-100 position-relative">
        <!-- Banner Section -->
        <section class="rt-about-banner-section d-flex align-items-center justify-content-center">
            <div class="container position-relative zindex-2">
                <h1 class="text-center position-relative title">About Us</h1>
                <h2 class="main-title text-center text-white"><b>" </b> Your Goals. <span class="rt-text-primary">Our
                        Guidance.</span> Exceptional
                    Results.<b> "</b></h2>
            </div>
        </section>
        <!-- Banner Section End -->
        <!-- Founder Section -->
        <section class="about-founder sec-pad d-block w-100 bg-white position-relative">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-5 col-md-12 col-sm-12 text-center">
                        <img src="{{ asset('assets/maylee-about.jpg') }}">
                        <div class="rt-social-block text-center mt-4 d-grid row-gap-25">
                            <h3 class="title mt-2">Where I’m Active</h3>
                            <div class="call-to-action justify-content-center my-4">
                                <a href="tel:+16478850114" class="phone">
                                    <span class="icon"><i class="fa-solid fa-phone-volume trin-trin"></i></span>
                                    <span class="text">Phone</span>
                                </a>
                                <a href="mailto:may.lee@mayleerealtor.com" class="email">
                                    <span class="icon"><i class="fa-solid fa-envelope-open-text trin-trin"></i></span>
                                    <span class="text">Email</span>
                                </a>
                            </div>
                            <p id="intro-text-en" class="text-start">Whether you’re a first-time buyer, a growing family, or
                                an investor, May
                                Lee delivers the clarity, care,
                                and fierce advocacy you deserve on your homeward journey.</p>
                            <p id="intro-text-mn" class="text-start d-none">無論您是首次購房者、成長中的家庭，還是投資者，May Lee
                                都將在您的安家之旅中，提供您應得的清
                                晰指引、細心關懷與全力以赴的支援。</p>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12 col-sm-12">
                        <div class="rt-content-block-2">
                            <div class="main-title">
                                <h1 style="word-wrap: break-word;">Reliable.Genuine.Experienced.</h1>
                            </div>
                            <div class="sub-title caps">
                                <h2>Delivering Honest Advice and Expert Support at Every Step</h2>
                            </div>
                            <div class="content">
                                <p>Licensed REALTOR® under the Toronto Regional Real Estate Board (TREBB), I bring
                                    experience, transparency, and dedication to every client. My goal is to provide honest
                                    advice, professional service, and trusted guidance for buyers and sellers across the
                                    GTA.</p>
                            </div>
                            <div class="rt-icon-text-list">
                                <div class="d-flex align-items-center justify-content-between my-4">
                                    <h3 class="title mb-0">Why Work With Me?</h3>
                                    <button id="lang-toggle-btn" class="rt-btn btn-small btn-outline">
                                        普通话 <span class="icon"><i class="fa-solid fa-language"></i></span>
                                    </button>
                                </div>

                                {{-- English Content --}}
                                <ul id="about-points-en" class="rt-hover-accordion row">
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-briefcase"></i></div>
                                            <div class="text-box">
                                                <span class="title">Experience</span>
                                                <p class="description">With over six years guiding GTA clients, May brings a
                                                    deeply personal touch, her educator roots shaping her uniquely
                                                    empathetic approach.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-graduation-cap"></i></div>
                                            <div class="text-box">
                                                <span class="title">Education</span>
                                                <p class="description">Holding a Bachelor of Science and Bachelor of
                                                    Education, May's teaching background infuses her real estate guidance
                                                    with patience and clarity.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-people-group"></i></div>
                                            <div class="text-box">
                                                <span class="title">Teamwork</span>
                                                <p class="description">May champions a supportive team dynamic. For over six
                                                    years, her seamless partnership with dedicated assistant Paul Wang
                                                    blends his meticulous coordination with her client-centered focus.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-comments"></i></div>
                                            <div class="text-box">
                                                <span class="title">Client Awareness</span>
                                                <p class="description">May excels at demystifying complexities, patiently
                                                    explaining the "why" behind each step to foster trust and make major
                                                    decisions feel approachable.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-mug-hot"></i></div>
                                            <div class="text-box">
                                                <span class="title">Personalized Attention</span>
                                                <p class="description">This enables May to stay fully present—deciphering
                                                    market data, strategizing negotiations, or simply listening over coffee.
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                                            <div class="text-box">
                                                <span class="title">Hands-On Approach</span>
                                                <p class="description">Her end-to-end process attends to every detail,
                                                    ensuring nothing escapes notice for flawless results.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-compass"></i></div>
                                            <div class="text-box">
                                                <span class="title">Guided Journey</span>
                                                <p class="description">From your first chat to keys in hand, May empowers
                                                    you with neighborhood insights and market wisdom for confident steps
                                                    forward.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-handshake"></i></div>
                                            <div class="text-box">
                                                <span class="title">Trusted Network</span>
                                                <p class="description">May's network of personally vetted pros—from brokers
                                                    to inspectors—extends her advocacy for your success.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-shield-halved"></i></div>
                                            <div class="text-box">
                                                <span class="title">All-In-One Ally</span>
                                                <p class="description">She's your guide, negotiator, and solver, wholly
                                                    committed to your ideal outcome.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-house-chimney-window"></i></div>
                                            <div class="text-box">
                                                <span class="title">Local at Heart</span>
                                                <p class="description">May lives the GTA deeply—savoring eateries, trails,
                                                    and community events—helping you discover a home where life truly
                                                    thrives.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>

                                {{-- Mandarin Content --}}
                                <ul id="about-points-mn" class="rt-hover-accordion row d-none">
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-briefcase"></i></div>
                                            <div class="text-box">
                                                <span class="title">經驗</span>
                                                <p class="description">憑藉六年以上服務大多倫多地區客戶的經驗，May 帶來極具個人溫度的服務。她的教育工作者背景，
                                                    塑造了獨特而富同理心的處事方式。</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-graduation-cap"></i></div>
                                            <div class="text-box">
                                                <span class="title">教育背景</span>
                                                <p class="description">擁有理學學士及教育學士學位的 May，將教學背景中蘊含的耐心與清晰思維，融入房地產指導之中。
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-people-group"></i></div>
                                            <div class="text-box">
                                                <span class="title">團隊協作
                                                </span>
                                                <p class="description">May 致力營造互助的團隊氛圍。六年多來，她與專注盡責的助理 Paul Wang 無間合作，結合其細緻的協
                                                    調能力與她以客戶為核心的服務理念。
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-comments"></i></div>
                                            <div class="text-box">
                                                <span class="title">客戶溝通
                                                </span>
                                                <p class="description">May 擅長化繁為簡，耐心解釋每個步驟背後的「原因」，從而建立信任，讓重大決策變得輕鬆易懂。
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-mug-hot"></i></div>
                                            <div class="text-box">
                                                <span class="title">個性化關注
                                                </span>
                                                <p class="description">這讓 May 能全心投入當下——無論是解讀市場數據、制定談判策略，或只是與您邊喝咖啡邊傾聽需
                                                    求。</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                                            <div class="text-box">
                                                <span class="title">親力親為
                                                </span>
                                                <p class="description">她的端到端服務流程關注每個細節，確保無一遺漏，實現完美成果。</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-compass"></i></div>
                                            <div class="text-box">
                                                <span class="title">全程引導
                                                </span>
                                                <p class="description">從初次諮詢到交鑰匙，May 會為您深入解析社區動態與市場智慧，讓每一步都走得自信踏實。
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-handshake"></i></div>
                                            <div class="text-box">
                                                <span class="title">可信賴的專業網絡
                                                </span>
                                                <p class="description">May 擁有親自篩選的專業人士網絡（從經紀到驗房師），這份資源將延伸為對您成功之路的堅實支
                                                    持。
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-shield-halved"></i></div>
                                            <div class="text-box">
                                                <span class="title">全方位的夥伴
                                                </span>
                                                <p class="description">她是您的嚮導、協商專家與問題解決者，全心致力於實現您理想的結局。
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                        <div class="rt-accordion-item">
                                            <div class="icon-box"><i class="fa-solid fa-house-chimney-window"></i></div>
                                            <div class="text-box">
                                                <span class="title">在地生活家
                                                </span>
                                                <p class="description">May 深深融入大多倫多地區的生活——品味美食、探索步道、參與社區活動，助您找到真正讓生活茁壯
                                                    的家園。
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Founder Section End -->
        <!-- Associate Executive Section -->
        <section class="rt-associate-executive-sec d-block w-100 position-relative sec-pad">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-9 col-sm-12">
                        <div class="rt-content-block">
                            <div class="d-flex justify-content-between">
                                <div class="assist">
                                    <h2 class="title">Paul Wang</h2>
                                    <h3 class="subtitle">Assistant</h3>
                                </div>
                                <button id="lang-toggle-btn-assistant" class="rt-btn btn-small btn-outline">
                                    普通话 <span class="icon"><i class="fa-solid fa-language"></i></span>
                                </button>
                            </div>
                            <div id="assistant-text-en" class="content">
                                <p>Paul Wang serves as an integral assistant to a dedicated realtor May Lee, bringing a
                                    multifaceted skill set to the
                                    practice. His background—including a foundation in arts and education, over two decades
                                    of non-profit
                                    leadership, and experience co-founding an IT services firm—informs his client-focused
                                    approach. This unique
                                    blend of community-minded service and strategic problem-solving allows him to provide
                                    exceptional support.
                                </p>
                                <p>Working closely with May, Paul is characterized by his deep commitment to understanding
                                    client needs, a
                                    creative approach to challenges, and unwavering ethical standards. He helps ensure every
                                    client receives
                                    attentive, comprehensive, and smooth guidance throughout their real estate journey.</p>
                                <p>Ultimately, Paul’s distinct value lies in merging the heart of a community advocate with
                                    the analytical mind of a
                                    strategist. This positions him as a trusted and insightful support professional,
                                    dedicated to achieving the best
                                    possible outcomes for clients.
                                </p>
                            </div>

                            <div id="assistant-text-mn" class="content d-none">
                                <p>Paul Wang 是一位專業房地產經紀人 May Lee 的得力助理，他的服務根基於獨特的社區導向精神與策略
                                    性問題解決能力。擁有藝術與教育背景，超過二十年的非營利組織領導經驗，以及共同創辦 IT 服務公
                                    司的經歷，他為客戶關係帶來多面向的視角。
                                </p>
                                <p>他的工作特點在於深刻理解客戶需求、以創意應對挑戰，並始終秉持專業倫理與真誠服務。作為 May
                                    的緊密合作夥伴與助理，Paul 協助確保每一位客戶在房地產旅程中獲得細心、周全且流暢的專業指
                                    引。</p>
                                <p>最終，Paul 的獨特價值在於他能將社區倡導者的熱忱，與戰略家的分析思維相結合，這使他成為客戶
                                    實現房地產目標過程中一位值得信賴、具有洞察力的得力夥伴。
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <img src="{{ asset('assets/assistant.jpg') }}">
                    </div>
                </div>
            </div>
        </section>
        <!-- Associate Executive Section End -->
        <!-- Mission Section -->
        <section class="rt-mission-section d-block w-100 position-relative">
            <div id="mission-text-en" class="container text-center">
                <h1>Mission Statement</h1>
                <p>"To guide you through every stage of your real estate journey with transparency,
                    integrity, and
                    unwavering commitment. We prioritize your best interests, delivering exceptional results that empower
                    your next chapter."</p>

            </div>
            <div id="vision-text-en" class="container text-center">
                <h1>Our Vision</h1>
                <p>"To be your lifelong real estate partner—recognized for transforming every
                    transaction into a seamless,
                    rewarding, and trusted experience."</p>

            </div>

            <div id="mission-text-mn" class="container text-center d-none">
                <h1>使命:</h1>
                <p>"以透明誠信與堅定承諾，引領您走過房地產旅程的每個階段。我們始終以您的權益為先，創造卓越成果，開啟您的人生新篇章"</p>
            </div>
            <div id="vision-text-mn" class="container text-center d-none">
                <h1>願景:</h1>
                <p>"成為您終身信賴的房地產夥伴——將每次交易化為流暢無礙、回報豐厚且值得託付的體驗而備受認可。"</p>
            </div>
        </section>
        <!-- Mission Section End -->
        <!-- Values Section -->
        <section class="rt-value-section d-block w-100 position-relative sec-pad bg-white">
            <div class="container">
                <div class="rt-section-title-wrap text-center d-block w-100 mb-5">
                    <h2 class="main-title">Our Values</h2>
                </div>
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/family-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Family Focused</h3>
                                <p>We believe in building meaningful relationships and treating every client like
                                    family—ensuring comfort, trust, and support throughout each real estate journey.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box style-2">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/dream-home-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Dream & Milestones</h3>
                                <p>Passionate about helping clients achieve their dreams, we celebrate every milestone—from
                                    first homes to new beginnings—making these moments memorable and rewarding.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/service-come-first-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Service Comes First</h3>
                                <p>Exceptional service is at the heart of our business; we listen, understand unique needs,
                                    and go the extra mile to deliver a smooth, positive experience for buyers, sellers, and
                                    renters.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box style-2">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/honesty-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Integrity Always</h3>
                                <p>Honesty and transparency are non-negotiable; we act with integrity in every interaction,
                                    fostering trust and long-lasting client relationships.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/community-building-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Community Connection</h3>
                                <p>We are dedicated to connecting families with vibrant communities, supporting their
                                    well-being, and nurturing a sense of belonging within the GTA.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box style-2">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/referral-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Referrals Grow Our Family</h3>
                                <p>Our clients' referrals are the heart of our growth—trusted recommendations from happy
                                    families help us serve more clients and strengthen our community bonds.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Testimonial -->
        <section class="rt-testimonial-sec sec-pad overflow-hidden bg-white">
            <div class="container">
                <div class="rt-section-title-wrap d-block w-100">
                    <h2 class="main-title">Hear What Our Happy Home Owners Say</h2>
                </div>
                <x-testimonials />
            </div>
        </section>
        <!-- Testimonial End -->
        <!-- Conatct Section -->
        <section class="rt-home-contact-sec sec-pad position-relative overflow-hidden">
            <div class="container">
                <x-contact source="About Us" />
            </div>
        </section>
        <!-- Conatct Section End -->
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('lang-toggle-btn').addEventListener('click', function () {
            const enList = document.getElementById('about-points-en');
            const mnList = document.getElementById('about-points-mn');
            const enIntro = document.getElementById('intro-text-en');
            const mnIntro = document.getElementById('intro-text-mn');
            const enMission = document.getElementById('mission-text-en');
            const mnMission = document.getElementById('mission-text-mn');
            const enVision = document.getElementById('vision-text-en');
            const mnVision = document.getElementById('vision-text-mn');
            const btn = this;

            if (enList.classList.contains('d-none')) {
                enList.classList.remove('d-none');
                mnList.classList.add('d-none');
                enIntro.classList.remove('d-none');
                mnIntro.classList.add('d-none');
                enMission.classList.remove('d-none');
                mnMission.classList.add('d-none');
                enVision.classList.remove('d-none');
                mnVision.classList.add('d-none');
                btn.innerHTML = '普通话 <span class="icon"><i class="fa-solid fa-language"></i></span>';
            } else {
                enList.classList.add('d-none');
                mnList.classList.remove('d-none');
                enIntro.classList.add('d-none');
                mnIntro.classList.remove('d-none');
                enMission.classList.add('d-none');
                mnMission.classList.remove('d-none');
                enVision.classList.add('d-none');
                mnVision.classList.remove('d-none');
                btn.innerHTML = 'English <span class="icon"><i class="fa-solid fa-language"></i></span>';
            }
        });

        document.getElementById('lang-toggle-btn-assistant').addEventListener('click', function () {
            const enAssistant = document.getElementById('assistant-text-en');
            const mnAssistant = document.getElementById('assistant-text-mn');
            const btn = this;
            
            if (enAssistant.classList.contains('d-none')) {
                enAssistant.classList.remove('d-none');
                mnAssistant.classList.add('d-none');
                btn.innerHTML = '普通话 <span class="icon"><i class="fa-solid fa-language"></i></span>';
            } else {
                enAssistant.classList.add('d-none');
                mnAssistant.classList.remove('d-none');
                btn.innerHTML = 'English <span class="icon"><i class="fa-solid fa-language"></i></span>';
            }
        });

    </script>
@endsection