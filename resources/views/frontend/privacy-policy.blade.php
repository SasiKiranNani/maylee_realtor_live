@extends('layouts.frontend.index')

@section('contents')
    <div class="rt-page-title-breadcrumb">
        <div class="container">
            <h1 class="page-title">Privacy Policy</h1>
        </div>
    </div>
    <section class="privacy-policy py-5 bg-light">
        <div class="container">

            <div class="p-4 p-md-5 bg-white shadow-sm rounded-4">
                <!-- 1. Introduction -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">1. Introduction</h4>
                    <p class="text-muted">
                        May Lee, a licensed realtor affiliated with Right At Home Realty, is committed to protecting
                        the privacy and security of all personal information collected through
                        <a href="{{ route('home') }}"><strong style="color: #e6a4b4;">www.mayleerealtor.com</strong></a>.
                        This Privacy Policy
                        explains how personal information is
                        collected, used, disclosed, and protected in compliance with Canada’s
                        <em>Personal Information Protection and Electronic Documents Act (PIPEDA)</em>, CREA’s Privacy Code,
                        and relevant real estate regulations.
                    </p>
                </div>

                <!-- 2. Information We Collect -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">2. Information We Collect</h4>
                    <p class="text-muted">To provide professional real estate services, we collect the following personal
                        information from
                        clients and website users:</p>
                    <ul class="text-muted">
                        <li>Full names, contact information (email, telephone, mailing address)</li>
                        <li>Property preferences, transaction details, and financial information relevant to buying,
                            selling, or leasing real estate
                        </li>
                        <li>Identification data required to comply with legal and brokerage requirements
                        </li>
                        <li>Website usage data, cookies, and IP address for improving user experience and security
                        </li>
                    </ul>
                </div>

                <!-- 3. How We Collect Information -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">3. How We Collect Information</h4>
                    <p class="text-muted">Information is collected through:
                    </p>
                    <ul class="text-muted">
                        <li>Direct client interactions, including forms, emails, calls, and in-person meetings
                        </li>
                        <li>Website activities such as inquiry forms, newsletter sign-ups, and property search
                            features
                        </li>
                        <li>Publicly available sources and referrals as permitted by law
                        </li>
                        <li>Cookies and tracking technologies on the website, detailed in a separate cookie policy
                        </li>
                    </ul>
                </div>

                <!-- 4. Purpose of Collecting Information -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">4. Purpose of Collecting Information</h4>
                    <p class="text-muted">Personal information is collected and used to:
                    </p>
                    <ul class="text-muted">
                        <li>Provide real estate services tailored to client needs
                        </li>
                        <li>Facilitate communication regarding property listings, negotiations, and transactions</li>
                        <li>Comply with legal, brokerage, and regulatory obligations
                        </li>
                        <li>Improve and personalize website functionality and service offerings
                        </li>
                        <li>Send marketing communications with client consent
                        </li>
                    </ul>
                </div>

                <!-- 5. Disclosure of Personal Information -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">5. Disclosure of Personal Information</h4>
                    <p class="text-muted">We only share personal information:
                    </p>
                    <ul class="text-muted">
                        <li>
                            With authorized parties involved in the real estate transaction (e.g., lawyers, mortgage
                            brokers, inspectors)
                        </li>
                        <li>When required by law or to protect rights and safety </li>
                        <li>With third-party service providers who have agreed to comply with data protection
                            obligations</li>
                        <li>With client consent for marketing purposes </li>
                    </ul>
                </div>

                <!-- 6. Data Retention -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">6. Data Retention</h4>
                    <p class="text-muted">
                        Personal information is retained only as long as necessary for business operations, legal
                        compliance, or to fulfill client service agreements. Upon request, clients may ask for
                        information deletion or amendment, subject to legal or contractual limitations.
                    </p>
                </div>

                <!-- 7. Security Measures -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">7. Security Measures</h4>
                    <p class="text-muted">
                        May Lee and Right At Home Realty implement appropriate physical, organizational, and
                        technological safeguards to protect personal data from unauthorized access, disclosure, or
                        misuse.
                    </p>
                </div>
                
                    <!-- 8. Client Rights -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">8. Client Rights</h4>
                    <p class="text-muted">Clients have the right to:
                    </p>
                    <ul class="text-muted">
                        <li>
                            Access and request correction of their personal information
                        </li>
                        <li>Withdraw consent for marketing communications at any time</li>
                        <!--<li>File complaints regarding privacy practices with May Lee or the Office of the Privacy Commissioner of Canada</li>-->
                        
                    </ul>
                </div>

                <!-- 9. Children’s Privacy -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">9. Children’s Privacy</h4>
                    <p class="text-muted">
                        Our services are not directed at children under 18 years old, and we do not knowingly collect
                        data from minors. If personal information about a minor is inadvertently collected, please
                        contact us immediately for removal.
                    </p>
                </div>

                <!-- 10. Use of Cookies and Tracking -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">10. Use of Cookies and Tracking</h4>
                    <p class="text-muted">
                        Our website uses cookies to enhance user experience and analyze website traffic. Users can
                        manage cookie preferences via browser settings. Detailed cookie use is described in a separate
                        <strong>Cookie Policy</strong>.
                    </p>
                </div>

                <!-- 11. Changes to This Policy -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">11. Changes to This Policy</h4>
                    <p class="text-muted">
                        This Privacy Policy may be updated periodically to reflect changes in legal requirements or our
                        business practices. Updated versions will be posted on the website with the effective date.

                    </p>
                </div>

                <!-- 12. Contact Information -->
                <div class="border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">12. Contact Information</h4>
                    <p class="text-muted mb-2">For questions or concerns about this Privacy Policy or personal information,
                        contact May Lee at
                        :</p>
                    <ul class="list-unstyled text-muted">
                        <li><strong>Email:</strong> mayleerah@gmail.com</li>
                        <li><strong>Phone:</strong> (001) - 2345678901</li>
                        <li><strong>Brokerage:</strong> Right At Home Realty</li>
                        <li><strong>Address:</strong> 1550 16th Avenue, Bldg B, Unit 3 & 4</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('styles')
    <style>
        .privacy-policy h4 {
            transition: color 0.3s ease;
        }

        .hover-section:hover h4 {
            color: #4c418c !important;
        }

        .hover-section {
            transition: transform 0.2s ease, background-color 0.3s ease;
        }

        .border-warning-subtle {
            border-color: #4c418c !important;
        }

        .hover-section:hover {
            background-color: #fffdf8;
            transform: translateY(-2px);
        }
    </style>
@endsection
