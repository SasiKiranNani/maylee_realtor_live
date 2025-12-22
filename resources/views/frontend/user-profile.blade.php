@extends('layouts.frontend.index')

@section('contents')
    <div class="user-profile-page bg-light pb-5">
            <!-- Header Section -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <!-- Cover Image -->
                <div class="position-relative cover-image-container" style="height: 250px; background-color: #e9ecef;">
                    <div class="cover-overlay"></div>
                    <img src="{{ $user->cover_image ? Storage::url($user->cover_image) : asset('frontend/assets/images/hero-banner.webp') }}"
                        alt="Cover Image" class="w-100 h-100 object-fit-cover" style="object-fit: cover;">

                    <button class="btn btn-light btn-sm position-absolute top-75 end-0 m-3 edit-cover-btn"
                        data-bs-toggle="modal" data-bs-target="#updateCoverModal">
                        <i class="fa-solid fa-camera me-1"></i> Edit Cover
                    </button>
                </div>

                <div class="card-body position-relative pt-0 row">
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <!-- Profile Image & Info -->
                        <div class="row justify-content-center">
                            <div class="col-lg-3 col-md-4 text-center">
                                <div class="position-relative d-inline-block avatar-container" style="margin-top: -80px;">
                                    <div class="avatar-wrapper position-relative">
                                        <img src="{{ $user->avatar ? Storage::url($user->avatar) : asset('frontend/assets/images/user.webp') }}"
                                            alt="Profile"
                                            class="rounded-circle border border-4 border-white shadow bg-white"
                                            style="width: 150px; height: 150px; object-fit: cover;">

                                        <div class="avatar-overlay rounded-circle" data-bs-toggle="modal"
                                            data-bs-target="#updateAvatarModal">
                                            <i class="fa-solid fa-camera text-white fs-4"></i>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="mt-3 mb-1 fw-bold">{{ $user->name }}</h4>
                            </div>
                        </div>
                    </div>
                    <!-- Tabs Navigation -->
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 d-flex align-items-center justify-content-center">
                        <div class="card-footer bg-white border-top border-light py-0">
                            <nav class="profile-nav">
                                <div class="nav nav-tabs border-0 gap-4" id="nav-tab" role="tablist">
                                    <button class="nav-link active border-0 bg-transparent py-3" id="nav-profile-tab"
                                        data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="true">
                                        <i class="fa-regular fa-user me-2"></i> Profile
                                    </button>
                                    <button class="nav-link border-0 bg-transparent py-3" id="nav-password-tab"
                                        data-bs-toggle="tab" data-bs-target="#nav-password" type="button" role="tab"
                                        aria-controls="nav-password" aria-selected="false">
                                        <i class="fa-solid fa-lock me-2"></i> Change Password
                                    </button>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="nav-tabContent">

                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold m-0">Profile Information</h5>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">FULL NAME</label>
                                    <input type="text" class="form-control form-control-lg fs-6" name="name"
                                        value="{{ $user->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">EMAIL ADDRESS</label>
                                    <input type="email" class="form-control form-control-lg fs-6" value="{{ $user->email }}"
                                        disabled style="background-color: #f8f9fa;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">PHONE NUMBER</label>
                                    <input type="text" class="form-control form-control-lg fs-6" name="phone"
                                        value="{{ $user->phone }}" placeholder="+1 234 567 8900">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">ADDRESS</label>
                                    <textarea class="form-control form-control-lg fs-6" name="address" rows="2"
                                        placeholder="Your full address">{{ $user->address }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">BIO</label>
                                    <textarea class="form-control form-control-lg fs-6" name="bio" rows="3"
                                        placeholder="Tell us about yourself">{{ $user->bio }}</textarea>
                                </div>

                                <div class="col-12 text-end mt-4">
                                    <button type="submit" class="btn btn-primary px-5 rounded-pill"
                                        style="background-color: #696cff; border-color: #696cff;">Update Profile</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Tab -->
                <div class="tab-pane fade" id="nav-password" role="tabpanel" aria-labelledby="nav-password-tab">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4">Security / Change Password</h5>

                        <div class="alert alert-light border-0 bg-light-info mb-4">
                            <i class="fa-solid fa-circle-info text-info me-2"></i>
                            If you need to reset your password via email, please log out and use the "Forgot Password"
                            feature.
                        </div>

                        <form action="{{ route('user.profile.password') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label text-muted small fw-bold">CURRENT PASSWORD</label>
                                    <input type="password" class="form-control form-control-lg fs-6" name="current_password"
                                        required>
                                    @error('current_password') <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">NEW PASSWORD</label>
                                    <input type="password" class="form-control form-control-lg fs-6" name="password"
                                        required>
                                    @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">CONFIRM NEW PASSWORD</label>
                                    <input type="password" class="form-control form-control-lg fs-6"
                                        name="password_confirmation" required>
                                </div>

                                <div class="col-12 text-end mt-4">
                                    <button type="submit" class="btn btn-primary px-5 rounded-pill"
                                        style="background-color: #696cff; border-color: #696cff;">Change Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
    </div>

    <!-- Update Avatar Modal -->
    <div class="modal fade" id="updateAvatarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Update Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{ route('user.profile.avatar') }}" method="POST" enctype="multipart/form-data"
                        id="avatarForm">
                        @csrf
                        <div class="mb-3">
                            <div class="preview-container mx-auto mb-3"
                                style="width: 200px; height: 200px; overflow: hidden; border-radius: 50%;">
                                <img id="avatarPreview"
                                    src="{{ $user->avatar ? Storage::url($user->avatar) : asset('frontend/assets/images/user.webp') }}"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <input type="file" class="form-control" name="avatar" accept="image/*"
                                onchange="previewImage(this, 'avatarPreview')">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"
                                style="background-color: #696cff; border-color: #696cff;">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Cover Modal -->
    <div class="modal fade" id="updateCoverModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Update Cover Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{ route('user.profile.cover') }}" method="POST" enctype="multipart/form-data"
                        id="coverForm">
                        @csrf
                        <div class="mb-3">
                            <div class="preview-container mx-auto mb-3 w-100"
                                style="height: 300px; overflow: hidden; border-radius: 8px;">
                                <img id="coverPreview"
                                    src="{{ $user->cover_image ? Storage::url($user->cover_image) : asset('frontend/assets/images/hero-banner.webp') }}"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <input type="file" class="form-control" name="cover_image" accept="image/*"
                                onchange="previewImage(this, 'coverPreview')">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"
                                style="background-color: #696cff; border-color: #696cff;">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(previewId).src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection

@section('styles')
    <style>
        .nav-tabs .nav-link {
            color: #566a7f;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: #696cff;
            border-color: transparent;
        }

        .nav-tabs .nav-link.active {
            color: #696cff;
            border-bottom: 2px solid #696cff;
            background: transparent;
        }

        .form-control:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1);
        }

        .bg-light-info {
            background-color: rgba(13, 202, 240, 0.1);
        }

        /* Profile Image Styles */
        .cover-image-container {
            overflow: hidden;
        }

        .cover-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 28, 56, 0.6););
            opacity: 1; /* Always visible as per request */
            transition: opacity 0.3s ease;
            pointer-events: none; /* Let clicks pass through */
        }
        
        /* Edit button z-index */
        .edit-cover-btn {
            z-index: 10;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .cover-image-container:hover .edit-cover-btn {
            opacity: 1;
        }

        .avatar-container {
            cursor: pointer;
        }

        .avatar-wrapper {
            position: relative;
            display: inline-block;
        }

        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
            cursor: pointer;
            border-radius: 50% !important;
            /* Ensure it matches the avatar border radius + border width offset if needed */
        }

        /* Fix overlay size to match image inner size inside border */
        .avatar-wrapper img {
            /* The image has a white border. We want the overlay inside it? 
               Or covering it? Usually covering everything looks cleaner.
               The image has `border border-4 border-white`.
            */
            position: relative;
            z-index: 1;
        }

        .avatar-overlay {
            z-index: 2;
            /* Adjustments to sit nicely over the image */
            top: 4px; /* match border width */
            left: 4px;
            width: calc(100% - 8px);
            height: calc(100% - 8px);
        }

        .avatar-wrapper:hover .avatar-overlay {
            opacity: 1;
        }
        .top-75 {
            top: 75%;
        }
    </style>
@endsection