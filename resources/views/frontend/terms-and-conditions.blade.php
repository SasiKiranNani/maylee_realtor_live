@extends('layouts.frontend.index')

@section('contents')
    <div class="rt-page-title-breadcrumb">
        <div class="container">
            <h1 class="page-title">Terms and Conditions</h1>
        </div>
    </div>
    <section class="privacy-policy py-5 bg-light">
        <div class="container">

            <div class="p-4 p-md-5 bg-white shadow-sm rounded-4">
                <!-- 1. Introduction -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">1. Introduction</h4>
                    <p class="text-muted">
                        Welcome to <a href="{{ route('home') }}"><strong style="color: #e6a4b4;">www.mayleerealtor.com</strong></a>. These Terms and Conditions govern
                        your use of this website and services
                        provided by May Lee, a licensed real estate professional affiliated with Right At Home Realty. By
                        accessing
                        this website and using our services, you agree to comply with all applicable Canadian real estate
                        laws,
                        brokerage policies, and these Terms and Conditions.
                    </p>
                </div>

                <!-- 2. Services Provided -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">2. Services Provided</h4>
                    <p class="text-muted">May Lee offers real estate brokerage services including property buying, selling,
                        leasing, consulting, and related
                        customer support in compliance with Canadian real estate regulations and brokerage standards.
                        Services may
                        involve property showings, negotiations, marketing, and transaction management.
                    </p>
                </div>

                <!-- 3. User Obligations -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">3. User Obligations</h4>
                    <p class="text-muted">Users agree to provide accurate and complete information when engaging with
                        services or submitting inquiries.
                        Users must comply with all applicable laws and not use this website for any unlawful or harmful
                        activities.
                    </p>
                </div>

                <!-- 4. Intellectual Property -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">4. Intellectual Property</h4>
                    <p class="text-muted">All content provided on this website, including text, images, logos, and designs,
                        is the property of May Lee and
                        Right At Home Realty or their licensors. Unauthorized use, reproduction, or distribution of website
                        content is
                        prohibited.
                    </p>
                </div>

                <!-- 5. Privacy and Data Protection -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">5. Privacy and Data Protection</h4>
                    <p class="text-muted">
                        May Lee is committed to protecting your privacy and handling your personal information according to
                        Canada's
                        Personal Information Protection and Electronic Documents Act (PIPEDA). Please consult our Privacy
                        Policy
                        for detailed information about how we collect, use, and protect your personal data.
                    </p>
                </div>

                <!-- 6. Limitations of Liability -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">6. Limitations of Liability</h4>
                    <p class="text-muted">
                        While May Lee strives to provide accurate and up-to-date information, the website and its content
                        are provided
                        "as is" and without warranties of any kind. May Lee and Right At Home Realty disclaim liability for
                        any errors,
                        omissions, or damages resulting from reliance on website information.
                    </p>
                </div>

                <!-- 7. Real Estate Disclosures and Compliance -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">7. Real Estate Disclosures and Compliance</h4>
                    <p class="text-muted">
                        All real estate transactions facilitated comply with provincial laws, brokerage policies, and
                        ethical standards of
                        the Canadian Real Estate Association (CREA). Clients acknowledge responsibility to conduct their own
                        due
                        diligence and seek independent legal or financial advice as needed.
                    </p>
                </div>

                <!-- 8. Commission and Fees -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">8. Commission and Fees</h4>
                    <p class="text-muted">
                        Any commission or fees related to real estate transactions will be disclosed and agreed upon in
                        advance as per
                        brokerage and provincial regulatory requirements. Specific terms will be outlined in formal
                        agreements between
                        May Lee and the client.

                    </p>
                </div>

                <!-- 9. Third-Party Links and Services -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">9. Third-Party Links and Services</h4>
                    <p class="text-muted">
                        This website may contain links to third-party websites or services. These links are provided for
                        convenience and
                        do not constitute endorsement. May Lee and Right At Home Realty are not responsible for the content
                        or
                        practices of linked sites.
                    </p>
                </div>

                <!-- 10. Changes to Terms and Conditions -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">10. Changes to Terms and Conditions</h4>
                    <p class="text-muted">
                        May Lee reserves the right to update or modify these Terms and Conditions at any time without prior
                        notice.
                        Users are encouraged to review these Terms periodically. Continued use of the website constitutes
                        acceptance
                        of any changes.
                    </p>
                </div>

                <!-- 11. Governing Law -->
                <div class="mb-5 border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">11. Governing Law</h4>
                    <p class="text-muted">
                        These Terms and Conditions are governed by and construed in accordance with the laws of the province
                        in
                        which services are provided and the federal laws of Canada applicable therein.
                    </p>
                </div>

                <!-- 12. Contact Information -->
                <div class="border-start border-3 ps-4 border-warning-subtle hover-section">
                    <h4 class="fw-semibold text-dark mb-3">12. Contact Information</h4>
                    <p class="text-muted mb-2">Questions regarding these Terms and Conditions or any website-related issues
                        may be directed to:
                    </p>
                    <ul class="list-unstyled text-muted">
                        <li><strong>Name:</strong> May Lee</li>
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
