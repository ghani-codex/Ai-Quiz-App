@extends('layouts.public')

@section('title', 'Pricing')

@section('content')
    <section id="pricing" class="pricing section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Pricing</h2>
            <p>Pick the plan that fits — students are always free<br></p>
        </div>
        <div class="container">
            <div class="row gy-4">
                @foreach($plans as $i => $plan)
                    <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="{{ 100 + $i * 100 }}">
                        <div class="pricing-tem">
                            @if($i === 1) <span class="featured">Most Popular</span> @endif
                            <h3 style="color: {{ ['#20c997','#0dcaf0','#fd7e14','#0d6efd'][$i % 4] }};">{{ $plan->name }}</h3>
                            <div class="price"><sup>$</sup>{{ (int) $plan->price }}<span> / {{ $plan->billing_period === 'lifetime' ? 'lifetime' : 'mo' }}</span></div>
                            <div class="icon">
                                <i class="bi {{ ['bi-box','bi-send','bi-airplane','bi-rocket'][$i % 4] }}" style="color: {{ ['#20c997','#0dcaf0','#fd7e14','#0d6efd'][$i % 4] }};"></i>
                            </div>
                            <ul>
                                @foreach((array) $plan->features as $f)
                                    <li>{{ $f }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('register') }}" class="btn-buy">Choose plan</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
