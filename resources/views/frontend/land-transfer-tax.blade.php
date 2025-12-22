@extends('layouts.frontend.index')

@section('contents')
<div class="rt-single-page d-block w-100 position-reltaive">
    <div class="rt-page-title-bar">
        <div class="container position-relative">
            <h2 class="text-center text-white">Land Transfer Tax</h2>
            <ul class="rt-breadcrumb">
                <li>
                    <a href="{{ route('home') }}">Home</a>
                </li>
                <li>
                    Land Transfer Tax
                </li>
            </ul>
        </div>
    </div>
    <div class="rt-single-page-wrapper d-block w-100 position-relative sec-pad">
        <div class="container">
            <div class="col-xs-12 col-sm-12 col-md-8">
                <p>Understand Land Transfer Tax costs when buying a home. Learn how it’s calculated, available rebates, and what to expect at closing.</p>
                <div class="rt-land-transfer-tax-calculator-widget inner-box">
                    <div class="box-header">
                        <h2 class="box-title">land transfer tax calculator</h2>
                    </div>
                    <div class="box-content">
                        <div class="row align-items-center">
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="land-amount">Price</label>
                                    <input type="text" name="land-amount" id="land-amount" placeholder="$ Enter amount">
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="land-location">Location</label>
                                    <input type="text" name="land-location" id="land-location">
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 mb-4">
                                <div class="form-group">
                                    <label for="first_time_buyer" class="rt-checkbox">
                                        <input class="rt_checkbox_input" type="checkbox" id="first_time_buyer">
                                        <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                            <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#006F94" rx="3"></rect>
                                            <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4" d="M4 10l5 5 9-9"></path>
                                        </svg>
                                        <span class="rt_checkbox_label">I’m a first time home buyer</span>
                                    </label>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="rt-land-transfer-result">
                                    <div class="info-group">
                                        <div class="title">Provincial</div>
                                        <div class="value">$16,475</div>
                                    </div>
                                    <div class="info-group">
                                        <div class="title">Municipal</div>
                                        <div class="value">$16,475</div>
                                    </div>
                                    <div class="info-group">
                                        <div class="title">Rebate</div>
                                        <div class="value">$0</div>
                                    </div>
                                    <div class="info-group total">
                                        <div class="title">Land transfer tax</div>
                                        <div class="value">$32,950</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="chart-container d-block w-100 position-relative">
                                    <canvas id="rt-land-transfer-tax-chart"></canvas>
                                    <div class="centerText" id="chartCenter">$32,950</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-4"></div>
        </div>
    </div>
</div>
@endsection
